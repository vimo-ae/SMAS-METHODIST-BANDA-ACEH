<?php

use App\Models\User;

test('a guru cannot access admin pages even when an admin page was the intended URL', function () {
    session(['url.intended' => '/admin/dashboard']);

    $guru = new User([
        'user_id' => '90001',
        'name' => 'Guru Test',
        'role' => 'guru',
    ]);

    $this->actingAs($guru)
        ->get('/admin/dashboard')
        ->assertForbidden()
        ->assertSee('Halaman ini tidak bisa dibuka');
});

test('a student cannot access guru pages', function () {
    $student = new User([
        'user_id' => '90002',
        'name' => 'Siswa Test',
        'role' => 'siswa',
    ]);

    $this->actingAs($student)
        ->get('/guru')
        ->assertForbidden();
});
