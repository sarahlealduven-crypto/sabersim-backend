<?php

use App\Models\EstadisticaUsuario;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns user statistics', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();

    EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
        'total_examenes' => 10,
        'puntaje_promedio' => 75.50,
    ]);

    $response = actingAs($user)->getJson('/api/v1/estadisticas');

    $response->assertStatus(200);
    expect($response->json('data.user_id'))->toBe($user->id);
});

it('returns statistics for a specific subject', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();

    EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
        'total_examenes' => 5,
        'puntaje_promedio' => 82.30,
    ]);

    $response = actingAs($user)->getJson("/api/v1/estadisticas/{$materia->slug}");

    $response->assertStatus(200);
    $materiaId = $response->json('data.materia.id') ?? $response->json('materia.id');
    expect($materiaId)->toBe($materia->id);
    $materiaNombre = $response->json('data.materia.nombre') ?? $response->json('materia.nombre');
    expect($materiaNombre)->toBe($materia->nombre);
});

it('requires authentication to view statistics', function () {
    getJson('/api/v1/estadisticas')
        ->assertStatus(401);
});

it('returns 404 for non-existent subject statistics', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->getJson('/api/v1/estadisticas/non-existent-slug');

    $response->assertStatus(404);
});

it('returns statistics when no previous data exists', function () {
    $user = User::factory()->create();
    Materia::factory()->create(['activo' => true]);

    $response = actingAs($user)->getJson('/api/v1/estadisticas');

    $response->assertStatus(200);
    // El servicio debería crear estadísticas por defecto si no existen
    expect($response->json('data.user_id'))->toBe($user->id);
});

it('returns zero statistics for user with no exams', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();

    EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
        'total_examenes' => 0,
        'puntaje_promedio' => 0.0,
    ]);

    $response = actingAs($user)->getJson("/api/v1/estadisticas/{$materia->slug}");

    $response->assertStatus(200);
    expect($response->json('data.total_examenes'))->toBe(0);
});
