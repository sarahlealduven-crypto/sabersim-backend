<?php

use App\Filament\Resources\Preguntas\Pages\CreatePregunta;
use App\Models\Materia;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\Topico;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

function nuevaPregunta(): Pregunta
{
    $materia = Materia::factory()->create();
    $topico = Topico::factory()->for($materia)->create();

    return Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);
}

it('asigna automaticamente la letra al crear opciones sin letra_opcion', function () {
    $pregunta = nuevaPregunta();

    $primera = $pregunta->opcionesRespuesta()->create([
        'texto_opcion' => 'A. Primera opción',
        'es_correcta' => true,
    ]);

    $segunda = $pregunta->opcionesRespuesta()->create([
        'texto_opcion' => 'B. Segunda opción',
        'es_correcta' => false,
    ]);

    expect($primera->letra_opcion)->toBe('A');
    expect($segunda->letra_opcion)->toBe('B');
});

it('rellena el siguiente hueco de letra disponible', function () {
    $pregunta = nuevaPregunta();

    OpcionRespuesta::factory()->for($pregunta)->create(['letra_opcion' => 'A']);
    OpcionRespuesta::factory()->for($pregunta)->create(['letra_opcion' => 'C']);

    $nueva = $pregunta->opcionesRespuesta()->create([
        'texto_opcion' => 'Opción nueva',
        'es_correcta' => false,
    ]);

    expect($nueva->letra_opcion)->toBe('B');
});

it('respeta la letra cuando se proporciona explicitamente', function () {
    $pregunta = nuevaPregunta();

    $opcion = $pregunta->opcionesRespuesta()->create([
        'letra_opcion' => 'D',
        'texto_opcion' => 'Opción D',
        'es_correcta' => false,
    ]);

    expect($opcion->letra_opcion)->toBe('D');
});

it('guarda una pregunta con sus opciones desde el formulario de Filament', function () {
    $materia = Materia::factory()->create();
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);

    actingAs(User::factory()->create());

    Livewire::test(CreatePregunta::class)
        ->fillForm([
            'materia_id' => $materia->id,
            'topico_id' => $topico->id,
            'texto_pregunta' => '¿Cuál es la respuesta correcta?',
        ])
        ->set('data.opcionesRespuesta', [
            'opcion-a' => ['texto_opcion' => 'Primera opción', 'es_correcta' => true],
            'opcion-b' => ['texto_opcion' => 'Segunda opción', 'es_correcta' => false],
            'opcion-c' => ['texto_opcion' => 'Tercera opción', 'es_correcta' => false],
            'opcion-d' => ['texto_opcion' => 'Cuarta opción', 'es_correcta' => false],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $pregunta = Pregunta::query()->firstOrFail();

    expect($pregunta->opcionesRespuesta()->count())->toBe(4);
    expect($pregunta->opcionesRespuesta()->whereNull('letra_opcion')->count())->toBe(0);
    expect($pregunta->opcionesRespuesta()->orderBy('letra_opcion')->pluck('letra_opcion')->all())
        ->toBe(['A', 'B', 'C', 'D']);
});
