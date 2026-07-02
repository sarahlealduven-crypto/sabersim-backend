<?php

use App\Enums\EstadoExamen;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\SeccionExamen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can create an exam by subject', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();

    $response = actingAs($user)->postJson('/api/v1/examenes', [
        'tipo_examen' => 'por_materia',
        'materia_id' => $materia->id,
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id') ?? $response->json('id');
    $examen = Examen::find($id);

    expect($examen)->not->toBeNull();
    expect($examen->seccionesExamen)->toHaveCount(1);
    expect($examen->seccionesExamen->first()->materia_id)->toBe($materia->id);
});

it('requires authentication to create an exam', function () {
    $response = postJson('/api/v1/examenes', [
        'tipo_examen' => 'completo',
    ]);

    $response->assertStatus(401);
});

it('rejects invalid exam type', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->postJson('/api/v1/examenes', [
        'tipo_examen' => 'invalid_type',
    ]);

    $response->assertJsonValidationErrors('tipo_examen');
});

it('validates materia_id for subject exams', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->postJson('/api/v1/examenes', [
        'tipo_examen' => 'por_materia',
    ]);

    $response->assertJsonValidationErrors('materia_id');
});

it('can submit an answer to an exam', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'activo' => true,
    ]);

    $opcion = OpcionRespuesta::factory()->create([
        'pregunta_id' => $pregunta->id,
        'es_correcta' => true,
    ]);

    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);

    $pregunta->seccionesExamen()->attach($seccion->id);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_id' => $opcion->id,
        'tiempo_gastado' => 30,
    ]);

    $response->assertStatus(200);
    expect($response->json('es_correcta'))->toBe($opcion->es_correcta);
});

it('rejects an answer when the question is not attached to the exam section', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);
    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);
    $pregunta = Pregunta::factory()->create(['materia_id' => $materia->id]);
    $opcion = OpcionRespuesta::factory()->create(['pregunta_id' => $pregunta->id]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_id' => $opcion->id,
    ]);

    $response->assertNotFound();
});

it('rejects an answer when the option does not belong to the submitted question', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);
    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);
    $pregunta = Pregunta::factory()->create(['materia_id' => $materia->id]);
    $otherQuestion = Pregunta::factory()->create(['materia_id' => $materia->id]);
    $optionFromOtherQuestion = OpcionRespuesta::factory()->create(['pregunta_id' => $otherQuestion->id]);
    $pregunta->seccionesExamen()->attach($seccion->id);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_id' => $optionFromOtherQuestion->id,
    ]);

    $response->assertNotFound();
});

it('prevents submitting to completed exam', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
    ]);

    $pregunta = Pregunta::factory()->create(['materia_id' => $materia->id, 'activo' => true]);
    $opcion = OpcionRespuesta::factory()->create(['pregunta_id' => $pregunta->id, 'es_correcta' => true]);
    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);
    $pregunta->seccionesExamen()->attach($seccion->id);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_id' => $opcion->id,
    ]);

    $response->assertStatus(400);
    expect($response->json('message'))->toBe('El examen ya ha sido completado o abandonado.');
});

it('prevents submitting to owned exam', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $otherUser = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $otherUser->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $pregunta = Pregunta::factory()->create(['materia_id' => $materia->id, 'activo' => true]);
    $opcion = OpcionRespuesta::factory()->create(['pregunta_id' => $pregunta->id, 'es_correcta' => true]);
    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);
    $pregunta->seccionesExamen()->attach($seccion->id);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_id' => $opcion->id,
    ]);

    $response->assertStatus(403);
});

it('can finalize an exam', function () {
    $user = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/finalizar");

    $response->assertStatus(200);
    expect($examen->fresh()->estado)->toBe(EstadoExamen::Completado);
    expect($examen->fresh()->puntaje_total)->not->toBeNull();
});

it('can finalize an exam section that has no questions without dividing by zero', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);
    SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 0,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/finalizar");

    $response->assertOk();
    expect($examen->fresh())
        ->estado->toBe(EstadoExamen::Completado)
        ->puntaje_total->toBe('0.00');
});

it('cannot finalize already completed exam', function () {
    $user = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/finalizar");

    $response->assertStatus(400);
    expect($response->json('message'))->toBe('El examen ya ha sido completado o abandonado.');
});

it('can abandon an exam', function () {
    $user = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/abandonar");

    $response->assertStatus(200);
    expect($examen->fresh()->estado)->toBe(EstadoExamen::Abandonado);
});

it('prevents abandoning owned exam', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/abandonar");

    $response->assertStatus(403);
});

it('can list user exams', function () {
    $user = User::factory()->create();
    Examen::factory()->count(3)->create(['user_id' => $user->id]);

    $response = actingAs($user)->getJson('/api/v1/examenes');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(3);
});

it('can show an exam', function () {
    $user = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $response = actingAs($user)->getJson("/api/v1/examenes/{$examen->id}");

    $response->assertStatus(200);
    expect($response->json('data.id'))->toBe($examen->id);
});

it('can create a complete exam', function () {
    $user = User::factory()->create();
    Materia::factory()->count(3)->create(['activo' => true]);

    $response = actingAs($user)->postJson('/api/v1/examenes', [
        'tipo_examen' => 'completo',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id') ?? $response->json('id');
    $examen = Examen::find($id);

    expect($examen)->not->toBeNull();
});

it('requires authentication to submit answer', function () {
    $response = postJson('/api/v1/examenes/1/respuesta', [
        'seccion_examen_id' => 1,
        'pregunta_id' => 1,
        'opcion_id' => 1,
    ]);

    $response->assertStatus(401);
});

it('returns 404 for non-existent option on answer submission', function () {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);

    $seccion = SeccionExamen::create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/respuesta", [
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => 999,
        'opcion_id' => 999,
    ]);

    $response->assertStatus(422);
});

it('cannot submit answer to non-existent exam', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->postJson('/api/v1/examenes/999/respuesta', [
        'seccion_examen_id' => 1,
        'pregunta_id' => 1,
        'opcion_id' => 1,
    ]);

    $response->assertStatus(404);
});

it('cannot show another users exam', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = actingAs($user)->getJson("/api/v1/examenes/{$examen->id}");

    $response->assertStatus(403);
});

it('cannot show exam without authentication', function () {
    $examen = Examen::factory()->create();

    getJson("/api/v1/examenes/{$examen->id}")->assertStatus(401);
});

it('cannot list exams without authentication', function () {
    getJson('/api/v1/examenes')->assertStatus(401);
});

it('cannot abandon exam without authentication', function () {
    $examen = Examen::factory()->create();

    postJson("/api/v1/examenes/{$examen->id}/abandonar")->assertStatus(401);
});

it('cannot finalize exam without authentication', function () {
    $examen = Examen::factory()->create();

    postJson("/api/v1/examenes/{$examen->id}/finalizar")->assertStatus(401);
});

it('cannot finalize abandoned exam', function () {
    $user = User::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Abandonado,
    ]);

    $response = actingAs($user)->postJson("/api/v1/examenes/{$examen->id}/finalizar");

    $response->assertStatus(400);
    expect($response->json('message'))->toBe('El examen ya ha sido completado o abandonado.');
});
