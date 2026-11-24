<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Kembali ke Beranda');
    $response->assertSee('href="' . route('home') . '"', false);
    $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->withSession(['url.intended' => '/admin/dashboard'])
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
