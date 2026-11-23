<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamTokenService
{
    public const TTL_MINUTES = 10;

    public function hash(string $token): string
    {
        return hash_hmac('sha256', strtoupper(trim($token)), (string) config('app.key'));
    }

    public function activeToken(Exam $exam): ExamToken
    {
        if (! $exam->is_published || now()->gt($exam->end_time)) {
            throw new \RuntimeException('Token ujian hanya tersedia saat ujian aktif.');
        }

        return DB::transaction(function () use ($exam) {
            $active = ExamToken::on(DB::getDefaultConnection())
                ->where('exam_id', $exam->exam_id)
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if ($active) {
                return $active;
            }

            ExamToken::on(DB::getDefaultConnection())
                ->where('exam_id', $exam->exam_id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            do {
                $plainToken = strtoupper(Str::random(6));
                $plainToken = str_replace(['O', 'I', 'L', '0', '1'], ['A', 'Z', 'N', 'X', 'Y'], $plainToken);
                $hash = $this->hash($plainToken);
            } while (ExamToken::on(DB::getDefaultConnection())
                ->where('exam_id', $exam->exam_id)
                ->where('token_hash', $hash)
                ->exists());

            return ExamToken::on(DB::getDefaultConnection())->create([
                'exam_id' => $exam->exam_id,
                'token_hash' => $hash,
                'token_cipher' => Crypt::encryptString($plainToken),
                'issued_at' => now(),
                'expires_at' => now()->addMinutes(self::TTL_MINUTES),
                'is_active' => true,
            ]);
        });
    }

    public function validate(Exam $exam, string $submittedToken): bool
    {
        $active = ExamToken::on(DB::getDefaultConnection())
            ->where('exam_id', $exam->exam_id)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        return $active !== null
            && hash_equals($active->token_hash, $this->hash($submittedToken));
    }

    public function rotatePublishedTokens(): int
    {
        $count = 0;

        Exam::on(DB::getDefaultConnection())
            ->where('is_published', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now())
            ->get()
            ->each(function (Exam $exam) use (&$count) {
                $this->activeToken($exam);
                $count++;
            });

        ExamToken::on(DB::getDefaultConnection())
            ->where('is_active', true)
            ->where('expires_at', '<=', now())
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        return $count;
    }
}
