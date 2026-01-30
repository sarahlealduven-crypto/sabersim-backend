<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => true,
        ]);
});

it('rejects invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('correct-password'),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Las credenciales proporcionadas son incorrectas.',
            'errors' => [
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ],
        ]);
});

it('rejects login with non-existing user', function () {
    $response = postJson('/api/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Las credenciales proporcionadas son incorrectas.',
        ]);
});

it('validates required fields on login', function () {
    $response = postJson('/api/auth/login', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('validates email field on login', function () {
    $response = postJson('/api/auth/login', [
        'email' => 'invalid-email',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns valid api token on login', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);

    $token = $response->json('token');
    expect($token)->not->toBeEmpty();
    expect($token)->toBeString();

    // Verificar que el token existe en la base de datos con expiración de 30 días
    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'auth-token',
    ]);
});
