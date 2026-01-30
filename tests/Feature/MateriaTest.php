<?php

use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns all active subjects', function () {
    Materia::factory()->count(5)->create(['activo' => true]);

    $response = getJson('/api/v1/materias');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(5);
});

it('returns only active subjects ordered by display order', function () {
    Materia::factory()->create([
        'nombre' => 'Inactive Subject',
        'slug' => 'inactive',
        'orden_visualizacion' => 10,
        'activo' => false,
    ]);

    Materia::factory()->create([
        'nombre' => 'First Subject',
        'slug' => 'first',
        'orden_visualizacion' => 1,
        'activo' => true,
    ]);

    $response = getJson('/api/v1/materias');

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['nombre'])->toBe('First Subject');
});

it('returns empty list when no active subjects', function () {
    Materia::factory()->count(3)->create(['activo' => false]);

    $response = getJson('/api/v1/materias');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(0);
});

it('returns a subject with its topics', function () {
    $materia = Materia::factory()->create();
    for ($i = 0; $i < 3; $i++) {
        $materia->topicos()->create([
            'nombre' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'descripcion' => fake()->sentence(),
        ]);
    }

    $response = getJson("/api/v1/materias/{$materia->slug}");

    $response->assertStatus(200);
    $id = $response->json('data.id') ?? $response->json('id');
    expect($id)->toBe($materia->id);
    expect($response->json('data.topicos'))->toHaveCount(3);
});

it('returns 404 for non-existing subject', function () {
    $response = getJson('/api/v1/materias/non-existent-slug');

    $response->assertStatus(404);
});

it('returns 404 for inactive subject', function () {
    $materia = Materia::factory()->create(['activo' => false]);

    $response = getJson("/api/v1/materias/{$materia->slug}");

    $response->assertStatus(404);
});
