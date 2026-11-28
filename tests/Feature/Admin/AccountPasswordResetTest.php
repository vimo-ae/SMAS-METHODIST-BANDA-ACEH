<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config(['database.connections.mysql_admin' => array_merge(
        config('database.connections.sqlite'),
        ['database' => ':memory:'],
    )]);
    DB::purge('mysql_admin');

    Schema::connection('mysql_admin')->create('users', function (Blueprint $table) {
        $table->string('user_id')->primary();
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('role');
        $table->string('remember_token')->nullable();
        $table->timestamps();
    });
});

function createPasswordResetUser(string $userId, string $role): User
{
    return User::on('mysql_admin')->create([
        'user_id' => $userId,
        'name' => 'Test '.$role,
        'email' => $userId.'@example.test',
        'password' => Hash::make('old-password'),
        'role' => $role,
    ]);
}

test('admin can reset a teacher or student password and rotate the remember token', function (string $role) {
    $admin = new User(['user_id' => 'admin-1', 'name' => 'Admin', 'role' => 'admin']);
    $target = createPasswordResetUser('target-1', $role);
    $target->setRememberToken('old-remember-token');
    $target->save();

    $this->actingAs($admin)
        ->put(route('admin.accounts.password.update', $target->user_id), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertRedirect(route('admin.accounts.password.edit', $target->user_id))
        ->assertSessionHas('success');

    $updated = User::on('mysql_admin')->where('user_id', $target->user_id)->firstOrFail();
    expect(Hash::check('new-secure-password', $updated->password))->toBeTrue()
        ->and($updated->remember_token)->not->toBe('old-remember-token');
})->with(['guru', 'siswa']);

test('password reset requires a matching password of at least eight characters', function () {
    $admin = new User(['user_id' => 'admin-1', 'name' => 'Admin', 'role' => 'admin']);
    $target = createPasswordResetUser('target-1', 'guru');

    $this->actingAs($admin)
        ->from(route('admin.accounts.password.edit', $target->user_id))
        ->put(route('admin.accounts.password.update', $target->user_id), [
            'password' => 'short',
            'password_confirmation' => 'different',
        ])
        ->assertSessionHasErrors('password');

    expect(Hash::check('old-password', $target->fresh()->password))->toBeTrue();
});

test('password reset cannot target an admin account', function () {
    $admin = new User(['user_id' => 'admin-1', 'name' => 'Admin', 'role' => 'admin']);
    createPasswordResetUser('target-admin', 'admin');

    $this->actingAs($admin)
        ->put(route('admin.accounts.password.update', 'target-admin'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertNotFound();
});

test('non-admin users cannot reset account passwords', function () {
    $guru = new User(['user_id' => 'guru-1', 'name' => 'Guru', 'role' => 'guru']);
    $target = createPasswordResetUser('target-1', 'siswa');

    $this->actingAs($guru)
        ->put(route('admin.accounts.password.update', $target->user_id), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])
        ->assertForbidden();
});
