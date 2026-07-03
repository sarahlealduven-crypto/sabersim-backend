<?php

use App\Models\Materia;
use App\Models\MaterialApoyo;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('lists only active support materials ordered for display', function () {
    $materia = Materia::factory()->create(['nombre' => 'Matemáticas', 'slug' => 'matematicas']);

    MaterialApoyo::factory()->create([
        'materia_id' => $materia->id,
        'titulo' => 'Segundo material',
        'slug' => 'segundo-material',
        'orden_visualizacion' => 2,
    ]);
    MaterialApoyo::factory()->create([
        'materia_id' => $materia->id,
        'titulo' => 'Primer material',
        'slug' => 'primer-material',
        'orden_visualizacion' => 1,
    ]);
    MaterialApoyo::factory()->inactivo()->create([
        'materia_id' => $materia->id,
        'titulo' => 'Material oculto',
        'slug' => 'material-oculto',
    ]);

    $response = getJson('/api/v1/materiales');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.titulo'))->toBe('Primer material');
    expect($response->json('data.0.materia.slug'))->toBe('matematicas');
});

it('filters support materials by subject type and search query', function () {
    $matematicas = Materia::factory()->create(['slug' => 'matematicas']);
    $lectura = Materia::factory()->create(['slug' => 'lectura-critica']);

    MaterialApoyo::factory()->create([
        'materia_id' => $matematicas->id,
        'titulo' => 'Razones y porcentajes',
        'slug' => 'razones-y-porcentajes',
        'tipo' => MaterialApoyo::TIPO_YOUTUBE,
    ]);
    MaterialApoyo::factory()->googleDrive()->create([
        'materia_id' => $lectura->id,
        'titulo' => 'Cuadernillo de lectura',
        'slug' => 'cuadernillo-lectura',
    ]);

    $response = getJson('/api/v1/materiales?materia=lectura-critica&tipo=google_drive&q=cuadernillo');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.slug'))->toBe('cuadernillo-lectura');
    expect($response->json('data.0.tipo'))->toBe(MaterialApoyo::TIPO_GOOGLE_DRIVE);
});

it('returns a support material by slug', function () {
    $material = MaterialApoyo::factory()->create([
        'titulo' => 'Lectura crítica avanzada',
        'slug' => 'lectura-critica-avanzada',
    ]);

    $response = getJson("/api/v1/materiales/{$material->slug}");

    $response->assertOk();
    expect($response->json('data.id'))->toBe($material->id);
});

it('returns 404 for inactive support material details', function () {
    $material = MaterialApoyo::factory()->inactivo()->create([
        'slug' => 'material-inactivo',
    ]);

    $response = getJson("/api/v1/materiales/{$material->slug}");

    $response->assertNotFound();
});

it('normalizes youtube and google drive source urls into embeddable urls', function () {
    expect(MaterialApoyo::embedUrlFor(MaterialApoyo::TIPO_YOUTUBE, 'https://youtu.be/jjO21znJdiE'))
        ->toBe('https://www.youtube.com/embed/jjO21znJdiE');

    expect(MaterialApoyo::embedUrlFor(MaterialApoyo::TIPO_GOOGLE_DRIVE, 'https://drive.google.com/file/d/148MvqVppDg7w0x9HJQnadS2OaS2ymiOx/view?usp=sharing'))
        ->toBe('https://drive.google.com/file/d/148MvqVppDg7w0x9HJQnadS2OaS2ymiOx/preview');
});
