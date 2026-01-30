<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('sends reset link for existing email', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = postJson('/api/auth/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Si el correo existe, enviaremos un enlace de restablecimiento.',
        ]);

    // Verificar que se envió la notificación
    Notification::assertSentTo($user, ResetPassword::class);
});

it('sends generic message for non-existing email', function () {
    Notification::fake();

    $response = postJson('/api/auth/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'No podemos encontrar el correo proporcionado.',
        ]);

    // Verificar que NO se envió ninguna notificación
    Notification::assertNothingSent();
});

it('can reset password with valid token', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    // Crear token de reset
    $token = Password::createToken($user);

    $response = postJson('/api/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Contraseña restablecida exitosamente.',
        ]);

    // Verificar que la contraseña fue actualizada
    $user->refresh();
    expect(Hash::check('new-password123', $user->password))
        ->toBeTrue();
    expect(Hash::check('old-password', $user->password))
        ->toBeFalse();
});

it('cannot reset password with invalid token', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    $response = postJson('/api/auth/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'El token de restablecimiento es inválido o ha expirado.',
        ]);

    // Verificar que la contraseña NO fue actualizada
    $user->refresh();
    expect(Hash::check('old-password', $user->password))
        ->toBeTrue();
});

it('validates password confirmation on reset', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = postJson('/api/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'different-password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('requires valid email format on forgot password', function () {
    $response = postJson('/api/auth/forgot-password', [
        'email' => 'invalid-email-format',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
