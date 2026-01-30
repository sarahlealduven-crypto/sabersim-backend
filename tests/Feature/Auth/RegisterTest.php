<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'grade_level' => 11,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'user' => [
                'id' => true,
                'name' => 'Test User',
                'email' => 'test@example.com',
            ],
            'token' => true,
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

it('validates registration fields', function () {
    $response = postJson('/api/auth/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
        'password_confirmation' => '456',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);

    assertDatabaseCount('users', 0);
});

it('requires unique email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = postJson('/api/auth/register', [
        'name' => 'New User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('sets default grade level when not provided', function () {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'grade_level' => 11,
    ]);
});

it('sets default values for level and xp on registration', function () {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'grade_level' => 12,
    ]);

    $response->assertStatus(201);

    $user = User::where('email', 'test@example.com')->first();
    expect($user->current_level)->toBe(1);
    expect($user->total_xp)->toBe(0);
});

it('returns valid api token on registration', function () {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201);

    $token = $response->json('token');
    expect($token)->not->toBeEmpty();
    expect($token)->toBeString();

    // Verificar que el token existe en la base de datos con expiración de 30 días
    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'auth-token',
    ]);
});
