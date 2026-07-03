<?php

use App\Enums\EstadoExamen;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\EstadisticaUsuario;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\RespuestaUsuario;
use App\Models\SeccionExamen;
use App\Models\Topico;
use App\Models\User;
use Illuminate\Support\Str;

it('persists and traverses the core exam relationships', function (): void {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);
    $opcionCorrecta = OpcionRespuesta::factory()->correcta()->create([
        'pregunta_id' => $pregunta->id,
        'letra_opcion' => 'A',
    ]);
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
    ]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
    ]);
    $seccion->preguntas()->attach($pregunta);
    $respuesta = RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_seleccionada_id' => $opcionCorrecta->id,
        'es_correcta' => true,
    ]);
    $estadistica = EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
    ]);

    expect($materia->topicos)->toHaveCount(1)
        ->and($materia->preguntas)->toHaveCount(1)
        ->and($materia->seccionesExamen)->toHaveCount(1)
        ->and($materia->estadisticasUsuario)->toHaveCount(1)
        ->and($topico->materia->is($materia))->toBeTrue()
        ->and($topico->preguntas)->toHaveCount(1)
        ->and($pregunta->materia->is($materia))->toBeTrue()
        ->and($pregunta->topico->is($topico))->toBeTrue()
        ->and($pregunta->opcionesRespuesta)->toHaveCount(1)
        ->and($pregunta->opcionCorrecta->is($opcionCorrecta))->toBeTrue()
        ->and($pregunta->respuestasUsuario)->toHaveCount(1)
        ->and($pregunta->seccionesExamen)->toHaveCount(1)
        ->and($opcionCorrecta->pregunta->is($pregunta))->toBeTrue()
        ->and($opcionCorrecta->respuestasUsuario)->toHaveCount(1)
        ->and($user->examenes)->toHaveCount(1)
        ->and($user->estadisticasUsuario->first()->is($estadistica))->toBeTrue()
        ->and($examen->user->is($user))->toBeTrue()
        ->and($examen->seccionesExamen)->toHaveCount(1)
        ->and($examen->isCompleted())->toBeTrue()
        ->and($seccion->examen->is($examen))->toBeTrue()
        ->and($seccion->materia->is($materia))->toBeTrue()
        ->and($seccion->preguntas)->toHaveCount(1)
        ->and($seccion->respuestasUsuario)->toHaveCount(1)
        ->and($respuesta->seccionExamen->is($seccion))->toBeTrue()
        ->and($respuesta->pregunta->is($pregunta))->toBeTrue()
        ->and($respuesta->opcionSeleccionada->is($opcionCorrecta))->toBeTrue()
        ->and($estadistica->user->is($user))->toBeTrue()
        ->and($estadistica->materia->is($materia))->toBeTrue();
});

it('assigns answer option letters per question without leaking across questions', function (): void {
    $materia = Materia::factory()->create();
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    $firstQuestion = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);
    $secondQuestion = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);

    $firstA = $firstQuestion->opcionesRespuesta()->create([
        'texto_opcion' => 'Primera A',
        'es_correcta' => true,
    ]);
    $firstB = $firstQuestion->opcionesRespuesta()->create([
        'texto_opcion' => 'Primera B',
        'es_correcta' => false,
    ]);
    $secondA = $secondQuestion->opcionesRespuesta()->create([
        'texto_opcion' => 'Segunda A',
        'es_correcta' => true,
    ]);

    expect($firstA->letra_opcion)->toBe('A')
        ->and($firstB->letra_opcion)->toBe('B')
        ->and($secondA->letra_opcion)->toBe('A');
});

it('persists tutor conversation relationships with string identifiers', function (): void {
    $user = User::factory()->create();
    $conversation = AgentConversation::create([
        'id' => (string) Str::uuid(),
        'user_id' => $user->id,
        'title' => 'Tutor algebra',
    ]);
    $message = AgentConversationMessage::create([
        'id' => (string) Str::uuid(),
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'agent' => 'TutorAgent',
        'role' => 'assistant',
        'content' => 'Explicación',
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
    ]);

    expect($conversation->getKeyType())->toBe('string')
        ->and($message->getKeyType())->toBe('string')
        ->and($conversation->user->is($user))->toBeTrue()
        ->and($conversation->messages)->toHaveCount(1)
        ->and($message->conversation->is($conversation))->toBeTrue()
        ->and($message->user->is($user))->toBeTrue()
        ->and($user->agentConversations)->toHaveCount(1);
});
