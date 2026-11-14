<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->value('user_id');

        if (! $adminId) {
            $this->command->warn('Belum ada user. Buat user dulu sebelum menjalankan seeder ini.');
            return;
        }

        $items = [
            ['pengumuman', 'Jadwal Ujian Tengah Semester', "UTS akan dilaksanakan secara daring melalui portal E-Exam.\n\nSiswa diminta memastikan akun sudah aktif dan koneksi internet stabil sebelum ujian dimulai.", 1],
            ['berita_sekolah', 'Sosialisasi Portal E-Learning untuk Guru dan Siswa', "Sekolah mengadakan sosialisasi penggunaan portal E-Class dan E-Exam bagi seluruh guru dan siswa.\n\nKegiatan ini membahas cara mengakses materi, mengumpulkan tugas, dan mengikuti ujian daring.", 3],
            ['berita_kesiswaan', 'Pembinaan Siswa Berprestasi Semester Ganjil', "Pembinaan rutin bagi siswa berprestasi dilaksanakan setiap pekan di lingkungan sekolah.", 5],
            ['agenda', 'Agenda Pekan Ujian Akhir Semester', "Pekan ujian akhir semester dimulai bulan depan. Jadwal lengkap akan diumumkan melalui portal.", 7],
        ];

        foreach ($items as [$category, $title, $content, $daysAgo]) {
            Announcement::updateOrCreate(
                ['title' => $title],
                [
                    'category'     => $category,
                    'content'      => $content,
                    'target_roles' => ['siswa', 'guru'],
                    'is_public'    => true,
                    'published_at' => now()->subDays($daysAgo),
                    'created_by'   => $adminId,
                ]
            );
        }
    }
}