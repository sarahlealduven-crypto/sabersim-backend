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
