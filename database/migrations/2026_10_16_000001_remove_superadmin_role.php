<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Role "superadmin" dihapus. Akun superadmin lama dijadikan admin,
 * lalu enum role dipersempit menjadi: admin, guru, siswa, orangtua.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','guru','siswa','orangtua') NOT NULL");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','guru','siswa','orangtua') NOT NULL");
        }
    }
};
