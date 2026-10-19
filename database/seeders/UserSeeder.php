<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super admin dihapus. Hanya ada satu admin sekolah.
        User::where('role', 'superadmin')->delete();

        User::updateOrCreate(['user_id' => 1], [
            'name' => 'Admin Sekolah',
            'email' => 'admin@methodistbandaaceh.sch.id',
            'password' => 'password123', // di-hash otomatis oleh cast "hashed"
            'role' => 'admin',
        ]);
    }
}
