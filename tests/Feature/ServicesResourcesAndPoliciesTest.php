<?php

use App\Enums\EstadoExamen;
use App\Enums\NivelDificultad;
use App\Enums\TipoExamen;
use App\Http\Requests\FinalizarExamenRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\IniciarExamenRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SubmitRespuestaRequest;
use App\Http\Requests\TutorAskRequest;
use App\Http\Requests\TutorContinueRequest;
use App\Http\Resources\EstadisticaUsuarioResource;
use App\Http\Resources\ExamenResource;
use App\Http\Resources\MateriaResource;
use App\Http\Resources\OpcionRespuestaResource;
use App\Http\Resources\PreguntaResource;
use App\Http\Resources\RespuestaUsuarioResource;
use App\Http\Resources\SeccionExamenResource;
use App\Http\Resources\TopicoResource;
use App\Http\Resources\TutorResponseResource;
use App\Http\Resources\UserResource;
use App\Models\EstadisticaUsuario;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\RespuestaUsuario;
use App\Models\SeccionExamen;
use App\Models\Topico;
use App\Models\User;
use App\Policies\ExamenPolicy;
use App\Services\EstadisticaService;
use App\Services\ExamenService;
use Illuminate\Http\Request;

it('generates a subject exam with active questions and loaded sections', function (): void {
    $user = User::factory()->create();
    $materia = Materia::factory()->create([
        'activo' => true,
        'cantidad_preguntas' => 2,
    ]);
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    Pregunta::factory()->count(3)->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
        'activo' => true,
    ]);

    $examen = app(ExamenService::class)->generarExamen($user, TipoExamen::PorMateria, $materia->id);

    expect($examen->user->is($user))->toBeTrue()
        ->and($examen->tipo_examen)->toBe(TipoExamen::PorMateria)
        ->and($examen->estado)->toBe(EstadoExamen::EnProgreso)
        ->and($examen->seccionesExamen)->toHaveCount(1)
        ->and($examen->seccionesExamen->first()->materia->is($materia))->toBeTrue()
        ->and($examen->seccionesExamen->first()->preguntas)->toHaveCount(2);
});

it('refuses to generate an exam when no active subject is available', function (): void {
    Materia::factory()->create(['activo' => false]);

    app(ExamenService::class)->generarExamen(User::factory()->create(), TipoExamen::Completo);
})->throws(Exception::class, 'No hay materias activas disponibles para crear el examen.');

it('calculates scores and abandons exams through the exam service', function (): void {
    $user = User::factory()->create();
    $materia = Materia::factory()->create();
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::EnProgreso,
    ]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 2,
        'tiempo_gastado' => 120,
    ]);
    $firstQuestion = Pregunta::factory()->create(['materia_id' => $materia->id]);
    $secondQuestion = Pregunta::factory()->create(['materia_id' => $materia->id]);
    RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $firstQuestion->id,
        'es_correcta' => true,
    ]);
    RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $secondQuestion->id,
        'es_correcta' => false,
    ]);

    app(ExamenService::class)->calcularPuntaje($examen->fresh('seccionesExamen'));

    expect($seccion->fresh()->respuestas_correctas)->toBe(1)
        ->and($seccion->fresh()->puntaje)->toBe('50.00')
        ->and($examen->fresh()->estado)->toBe(EstadoExamen::Completado)
        ->and($examen->fresh()->puntaje_total)->toBe('50.00')
        ->and($examen->fresh()->tiempo_gastado)->toBe(120);

    app(ExamenService::class)->abandonarExamen($examen);

    expect($examen->fresh()->estado)->toBe(EstadoExamen::Abandonado);
});

it('updates general and per-subject statistics from completed exams', function (): void {
    $user = User::factory()->create();
    $materia = Materia::factory()->create(['activo' => true]);
    $otherMateria = Materia::factory()->create(['activo' => true]);
    $completedAt = now()->subDay();

    $firstExam = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
        'puntaje_total' => 80,
        'fecha_completado' => $completedAt,
    ]);
    SeccionExamen::factory()->create([
        'examen_id' => $firstExam->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 10,
        'respuestas_correctas' => 8,
        'tiempo_gastado' => 600,
    ]);

    $secondExam = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
        'puntaje_total' => 60,
        'fecha_completado' => now(),
    ]);
    SeccionExamen::factory()->create([
        'examen_id' => $secondExam->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 5,
        'respuestas_correctas' => 3,
        'tiempo_gastado' => 300,
    ]);
    SeccionExamen::factory()->create([
        'examen_id' => $secondExam->id,
        'materia_id' => $otherMateria->id,
        'total_preguntas' => 5,
        'respuestas_correctas' => 4,
        'tiempo_gastado' => 240,
    ]);

    app(EstadisticaService::class)->actualizarEstadisticas($user);

    $general = EstadisticaUsuario::query()
        ->where('user_id', $user->id)
        ->whereNull('materia_id')
        ->firstOrFail();
    $bySubject = EstadisticaUsuario::query()
        ->where('user_id', $user->id)
        ->where('materia_id', $materia->id)
        ->firstOrFail();
    $otherBySubject = EstadisticaUsuario::query()
        ->where('user_id', $user->id)
        ->where('materia_id', $otherMateria->id)
        ->firstOrFail();

    expect($general->total_examenes)->toBe(2)
        ->and($general->total_preguntas_respondidas)->toBe(20)
        ->and($general->respuestas_correctas)->toBe(15)
        ->and($general->puntaje_promedio)->toBe('70.00')
        ->and($general->mejor_puntaje)->toBe('80.00')
        ->and($general->tiempo_total_gastado)->toBe(1140)
        ->and($bySubject->total_examenes)->toBe(2)
        ->and($bySubject->total_preguntas_respondidas)->toBe(15)
        ->and($bySubject->respuestas_correctas)->toBe(11)
        ->and($bySubject->puntaje_promedio)->toBe('70.00')
        ->and($bySubject->mejor_puntaje)->toBe('80.00')
        ->and($bySubject->tiempo_total_gastado)->toBe(900)
        ->and($otherBySubject->total_examenes)->toBe(1)
        ->and($otherBySubject->puntaje_promedio)->toBe('60.00');
});

it('serializes all API resources with loaded relationships', function (): void {
    $user = User::factory()->create([
        'name' => 'Resource User',
        'email' => 'resource@example.com',
        'grade_level' => 10,
        'current_level' => 3,
        'total_xp' => 1500,
    ]);
    $materia = Materia::factory()->create(['nombre' => 'Resource Materia']);
    $topico = Topico::factory()->create(['materia_id' => $materia->id, 'nombre' => 'Resource Topic']);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
        'nivel_dificultad' => NivelDificultad::Dificil,
    ]);
    $opcion = OpcionRespuesta::factory()->create(['pregunta_id' => $pregunta->id]);
    $examen = Examen::factory()->create(['user_id' => $user->id]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
    ]);
    $seccion->preguntas()->attach($pregunta);
    $respuesta = RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_seleccionada_id' => $opcion->id,
    ]);
    $estadistica = EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
    ]);

    $request = Request::create('/testing');

    $materiaArray = MateriaResource::make($materia->load('topicos'))->resolve($request);
    $preguntaArray = PreguntaResource::make($pregunta->load('opcionesRespuesta'))->resolve($request);
    $seccionArray = SeccionExamenResource::make($seccion->load(['materia', 'preguntas.opcionesRespuesta']))->resolve($request);
    $examenArray = ExamenResource::make($examen->load(['seccionesExamen.materia', 'seccionesExamen.preguntas.opcionesRespuesta']))->resolve($request);
    $respuestaArray = RespuestaUsuarioResource::make($respuesta->load('seccionExamen.examen'))->resolve($request);
    $estadisticaArray = EstadisticaUsuarioResource::make($estadistica->load('materia'))->resolve($request);

    expect(UserResource::make($user)->resolve($request))
        ->toMatchArray([
            'id' => $user->id,
            'name' => 'Resource User',
            'email' => 'resource@example.com',
            'grade_level' => 10,
            'current_level' => 3,
            'total_xp' => 1500,
        ])
        ->and(TopicoResource::make($topico)->resolve($request))->toMatchArray([
            'id' => $topico->id,
            'materia_id' => $materia->id,
            'nombre' => 'Resource Topic',
        ])
        ->and(OpcionRespuestaResource::make($opcion)->resolve($request))->toMatchArray([
            'id' => $opcion->id,
            'pregunta_id' => $pregunta->id,
        ])
        ->and($materiaArray['topicos'])->toHaveCount(1)
        ->and($preguntaArray['opciones'])->toHaveCount(1)
        ->and($seccionArray['materia']['id'])->toBe($materia->id)
        ->and($seccionArray['preguntas'])->toHaveCount(1)
        ->and($examenArray['secciones'])->toHaveCount(1)
        ->and($respuestaArray['user_id'])->toBe($user->id)
        ->and($estadisticaArray['materia']['id'])->toBe($materia->id);
});

it('omits API resource relationships that were not eager loaded', function (): void {
    $request = Request::create('/testing');
    $materia = Materia::factory()->create();
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);
    $examen = Examen::factory()->create();
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
    ]);
    $estadistica = EstadisticaUsuario::factory()->create([
        'materia_id' => $materia->id,
    ]);

    expect(MateriaResource::make($materia->withoutRelations())->resolve($request))
        ->not->toHaveKey('topicos')
        ->and(PreguntaResource::make($pregunta->withoutRelations())->resolve($request))
        ->not->toHaveKey('opciones')
        ->and(ExamenResource::make($examen->withoutRelations())->resolve($request))
        ->not->toHaveKey('secciones')
        ->and(SeccionExamenResource::make($seccion->withoutRelations())->resolve($request))
        ->not->toHaveKey('materia')
        ->not->toHaveKey('preguntas')
        ->and(EstadisticaUsuarioResource::make($estadistica->withoutRelations())->resolve($request))
        ->not->toHaveKey('materia');
});

it('serializes tutor responses from arrays and objects', function (): void {
    $request = Request::create('/testing');
    $object = new class
    {
        public function toArray(): array
        {
            return [
                'conversation_id' => 'conversation-object',
                'response' => 'object response',
                'created_at' => '2026-01-01T00:00:00+00:00',
            ];
        }
    };

    expect(TutorResponseResource::make([
        'conversation_id' => 'conversation-array',
        'response' => 'array response',
        'materia' => ['id' => 1],
        'topico' => ['id' => 2],
        'created_at' => '2026-01-01T00:00:00+00:00',
    ])->resolve($request))->toMatchArray([
        'conversation_id' => 'conversation-array',
        'response' => 'array response',
        'materia' => ['id' => 1],
        'topico' => ['id' => 2],
    ])->and(TutorResponseResource::make($object)->resolve($request))->toMatchArray([
        'conversation_id' => 'conversation-object',
        'response' => 'object response',
        'materia' => null,
        'topico' => null,
    ]);
});

it('exposes request authorization and validation rule contracts', function (string $requestClass, array $expectedRuleKeys): void {
    $request = new $requestClass;

    expect($request->authorize())->toBeTrue()
        ->and(array_keys($request->rules()))->toBe($expectedRuleKeys);
})->with([
    [FinalizarExamenRequest::class, []],
    [ForgotPasswordRequest::class, ['email']],
    [IniciarExamenRequest::class, ['tipo_examen', 'materia_id']],
    [LoginRequest::class, ['email', 'password']],
    [RegisterRequest::class, ['name', 'email', 'password', 'password_confirmation', 'grade_level']],
    [ResetPasswordRequest::class, ['email', 'token', 'password', 'password_confirmation']],
    [SubmitRespuestaRequest::class, ['seccion_examen_id', 'pregunta_id', 'opcion_id', 'tiempo_gastado']],
    [TutorAskRequest::class, ['question', 'materia_id', 'topico_id']],
    [TutorContinueRequest::class, ['question']],
]);

it('exposes custom tutor validation messages', function (): void {
    expect((new TutorAskRequest)->messages())
        ->toHaveKey('question.required', 'La pregunta es obligatoria.')
        ->toHaveKey('topico_id.exists', 'El tema seleccionado no existe o no pertenece a la materia indicada.')
        ->and((new TutorContinueRequest)->messages())
        ->toHaveKey('question.required', 'La pregunta es obligatoria.');
});

it('allows and denies exam policy abilities by owner', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $examen = Examen::factory()->create(['user_id' => $owner->id]);
    $policy = new ExamenPolicy;

    expect($policy->viewAny($owner))->toBeTrue()
        ->and($policy->create($owner))->toBeTrue()
        ->and($policy->view($owner, $examen)->allowed())->toBeTrue()
        ->and($policy->update($owner, $examen)->allowed())->toBeTrue()
        ->and($policy->delete($owner, $examen)->allowed())->toBeTrue()
        ->and($policy->view($otherUser, $examen)->denied())->toBeTrue()
        ->and($policy->update($otherUser, $examen)->denied())->toBeTrue()
        ->and($policy->delete($otherUser, $examen)->denied())->toBeTrue()
        ->and($policy->restore($owner, $examen))->toBeFalse()
        ->and($policy->forceDelete($owner, $examen))->toBeFalse();
});
