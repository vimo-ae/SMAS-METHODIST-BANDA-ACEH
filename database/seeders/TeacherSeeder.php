<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public const TEACHERS = [
        ['Budi Santoso', 'budi.guru', 'Matematika'],
        ['Siti Rahma', 'siti.guru', 'Fisika'],
        ['Andi Wijaya', 'andi.guru', 'Biologi'],
        ['Rina Lestari', 'rina.guru', 'Kimia'],
        ['Maria Simanjuntak', 'maria.guru', 'Bahasa Indonesia'],
        ['John Panjaitan', 'john.guru', 'Bahasa Inggris'],
        ['Cut Nyak Dewi', 'dewi.guru', 'Sosial'],
        ['Daniel Sitompul', 'daniel.guru', 'Informatika & Seni'],
    ];

    public function run(): void
    {
        foreach (self::TEACHERS as $i => [$name, $mail, $spec]) {
            $nip = 1234567899887 + $i;
            User::updateOrCreate(['user_id' => $nip], [
                'name' => $name,
                'email' => $mail.'@methodistbandaaceh.sch.id',
                'password' => 'password123',
                'role' => 'guru',
            ]);
            Teacher::updateOrCreate(['nip' => $nip], ['specialization' => $spec]);
        }
    }
}
