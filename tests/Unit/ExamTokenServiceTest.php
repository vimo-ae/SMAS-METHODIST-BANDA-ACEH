<?php

use App\Models\Exam;
use App\Services\ExamTokenService;
use Tests\TestCase;

uses(TestCase::class);

it('throws a domain exception when a token is requested for an unpublished exam', function () {
    config(['exam.token_enabled' => true]);

    $exam = new Exam;
    $exam->is_published = false;
    $exam->end_time = now()->addHour();

    expect(fn () => app(ExamTokenService::class)->activeToken($exam))
        ->toThrow(DomainException::class, 'Token ujian hanya tersedia saat ujian aktif.');
});

it('disables token generation and validation when token access is off', function () {
    config(['exam.token_enabled' => false]);

    $exam = new Exam;
    $service = app(ExamTokenService::class);

    expect(fn () => $service->activeToken($exam))
        ->toThrow(DomainException::class, 'Token ujian sedang dinonaktifkan sementara.');
    expect($service->validate($exam, 'ABC123'))->toBeFalse();
    expect($service->rotatePublishedTokens())->toBe(0);
});
