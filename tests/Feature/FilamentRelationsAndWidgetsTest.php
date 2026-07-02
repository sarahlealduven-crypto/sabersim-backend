<?php

use App\Enums\EstadoExamen;
use App\Filament\Resources\Examens\Pages\EditExamen;
use App\Filament\Resources\Examens\RelationManagers\SeccionesExamenRelationManager;
use App\Filament\Resources\Materias\Pages\EditMateria;
use App\Filament\Resources\Materias\RelationManagers\PreguntasRelationManager as MateriaPreguntasRelationManager;
use App\Filament\Resources\Materias\RelationManagers\TopicosRelationManager;
use App\Filament\Resources\Preguntas\Pages\EditPregunta;
use App\Filament\Resources\Preguntas\RelationManagers\OpcionesRespuestaRelationManager;
use App\Filament\Resources\SeccionExamens\Pages\EditSeccionExamen;
use App\Filament\Resources\SeccionExamens\RelationManagers\PreguntasRelationManager as SeccionPreguntasRelationManager;
use App\Filament\Resources\SeccionExamens\RelationManagers\RespuestasUsuarioRelationManager;
use App\Filament\Resources\Topicos\Pages\EditTopico;
use App\Filament\Resources\Topicos\RelationManagers\PreguntasRelationManager as TopicoPreguntasRelationManager;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\EstadisticasRelationManager;
use App\Filament\Resources\Users\RelationManagers\ExamenesRelationManager;
use App\Filament\Widgets\AverageScoreWidget;
use App\Filament\Widgets\TotalExamsWidget;
use App\Filament\Widgets\TotalQuestionsWidget;
use App\Filament\Widgets\TotalStudentsWidget;
use App\Models\EstadisticaUsuario;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\RespuestaUsuario;
use App\Models\SeccionExamen;
use App\Models\Topico;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create());
});

function filamentRelationGraph(): array
{
    $user = User::factory()->create(['name' => 'Relation Student']);
    $materia = Materia::factory()->create(['nombre' => 'Lectura', 'slug' => 'lectura-relaciones']);
    $topico = Topico::factory()->create([
        'materia_id' => $materia->id,
        'nombre' => 'Comprensión',
        'slug' => 'comprension-relaciones',
    ]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
        'texto_pregunta' => 'Pregunta relacionada',
    ]);
    $opcion = OpcionRespuesta::factory()->create([
        'pregunta_id' => $pregunta->id,
        'letra_opcion' => 'A',
        'texto_opcion' => 'Opción relacionada',
        'es_correcta' => true,
    ]);
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Completado,
        'fecha_completado' => now(),
        'puntaje_total' => 80,
        'tiempo_gastado' => 1800,
    ]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
        'respuestas_correctas' => 1,
    ]);
    $seccion->preguntas()->attach($pregunta);
    $respuesta = RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_seleccionada_id' => $opcion->id,
        'es_correcta' => true,
    ]);
    $estadistica = EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
        'total_examenes' => 1,
        'total_preguntas_respondidas' => 1,
        'respuestas_correctas' => 1,
        'puntaje_promedio' => 80,
        'mejor_puntaje' => 80,
        'tiempo_total_gastado' => 1800,
        'fecha_ultimo_examen' => now(),
    ]);

    return compact('user', 'materia', 'topico', 'pregunta', 'opcion', 'examen', 'seccion', 'respuesta', 'estadistica');
}

it('loads Filament relation managers and shows related records', function (string $managerClass, string $pageClass, string $ownerKey, string $visibleText): void {
    $records = filamentRelationGraph();

    Livewire::test($managerClass, [
        'ownerRecord' => $records[$ownerKey],
        'pageClass' => $pageClass,
    ])
        ->assertOk()
        ->assertSee($visibleText);
})->with([
    'materia topicos' => [TopicosRelationManager::class, EditMateria::class, 'materia', 'Comprensión'],
    'materia preguntas' => [MateriaPreguntasRelationManager::class, EditMateria::class, 'materia', 'Pregunta relacionada'],
    'topico preguntas' => [TopicoPreguntasRelationManager::class, EditTopico::class, 'topico', 'Pregunta relacionada'],
    'pregunta opciones' => [OpcionesRespuestaRelationManager::class, EditPregunta::class, 'pregunta', 'Opción relacionada'],
    'examen secciones' => [SeccionesExamenRelationManager::class, EditExamen::class, 'examen', 'Lectura'],
    'seccion preguntas' => [SeccionPreguntasRelationManager::class, EditSeccionExamen::class, 'seccion', 'Pregunta relacionada'],
    'seccion respuestas' => [RespuestasUsuarioRelationManager::class, EditSeccionExamen::class, 'seccion', 'Opción relacionada'],
    'user examenes' => [ExamenesRelationManager::class, EditUser::class, 'user', 'completado'],
    'user estadisticas' => [EstadisticasRelationManager::class, EditUser::class, 'user', 'Lectura'],
]);

it('renders Filament dashboard widgets with aggregate data', function (string $widgetClass, string $label): void {
    filamentRelationGraph();
    User::factory()->create(['created_at' => now()->subDays(3)]);
    Pregunta::factory()->create(['activo' => true, 'created_at' => now()->subDays(3)]);
    Examen::factory()->create([
        'estado' => EstadoExamen::Completado,
        'fecha_completado' => now()->subDays(2),
        'puntaje_total' => 70,
    ]);

    Livewire::test($widgetClass)
        ->assertOk()
        ->assertSee($label);
})->with([
    [TotalStudentsWidget::class, 'Total Estudiantes'],
    [TotalQuestionsWidget::class, 'Total Preguntas'],
    [TotalExamsWidget::class, 'Total Exámenes'],
    [AverageScoreWidget::class, 'Puntaje Promedio'],
]);

it('keeps statistics relation manager aligned with existing database columns', function (): void {
    $records = filamentRelationGraph();

    Livewire::test(EstadisticasRelationManager::class, [
        'ownerRecord' => $records['user'],
        'pageClass' => EditUser::class,
    ])
        ->assertOk()
        ->assertSee('Lectura')
        ->assertSee('80.00%');
});
