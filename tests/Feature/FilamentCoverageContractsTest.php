<?php

use App\Enums\EstadoExamen;
use App\Enums\NivelDificultad;
use App\Filament\Resources\EstadisticaUsuarios\EstadisticaUsuarioResource;
use App\Filament\Resources\EstadisticaUsuarios\Infolists\EstadisticaUsuarioInfolist;
use App\Filament\Resources\EstadisticaUsuarios\Schemas\EstadisticaUsuarioForm;
use App\Filament\Resources\EstadisticaUsuarios\Tables\EstadisticaUsuariosTable;
use App\Filament\Resources\Examens\ExamenResource;
use App\Filament\Resources\Examens\Infolists\ExamenInfolist;
use App\Filament\Resources\Examens\Pages\EditExamen;
use App\Filament\Resources\Examens\Pages\ListExamens;
use App\Filament\Resources\Examens\RelationManagers\SeccionesExamenRelationManager;
use App\Filament\Resources\Examens\Tables\ExamensTable;
use App\Filament\Resources\MaterialApoyos\MaterialApoyoResource;
use App\Filament\Resources\MaterialApoyos\Pages\ListMaterialApoyos;
use App\Filament\Resources\MaterialApoyos\Tables\MaterialApoyosTable;
use App\Filament\Resources\Materias\Pages\EditMateria;
use App\Filament\Resources\Materias\RelationManagers\PreguntasRelationManager as MateriaPreguntasRelationManager;
use App\Filament\Resources\Materias\RelationManagers\TopicosRelationManager;
use App\Filament\Resources\Preguntas\Pages\EditPregunta;
use App\Filament\Resources\Preguntas\Pages\ListPreguntas;
use App\Filament\Resources\Preguntas\RelationManagers\OpcionesRespuestaRelationManager;
use App\Filament\Resources\Preguntas\Tables\PreguntasTable;
use App\Filament\Resources\RespuestaUsuarios\Tables\RespuestaUsuariosTable;
use App\Filament\Resources\SeccionExamens\Pages\EditSeccionExamen;
use App\Filament\Resources\SeccionExamens\RelationManagers\PreguntasRelationManager as SeccionPreguntasRelationManager;
use App\Filament\Resources\SeccionExamens\RelationManagers\RespuestasUsuarioRelationManager;
use App\Filament\Resources\SeccionExamens\Tables\SeccionExamensTable;
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
use App\Providers\AppServiceProvider;
use App\Services\ExamenService;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create());
});

function filamentCoverageGraph(): array
{
    $user = User::factory()->create(['name' => 'Coverage Student']);
    $materia = Materia::factory()->create(['nombre' => 'Coverage Materia']);
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);
    $opcion = OpcionRespuesta::factory()->create([
        'pregunta_id' => $pregunta->id,
        'es_correcta' => true,
    ]);
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'estado' => EstadoExamen::Abandonado,
        'puntaje_total' => 55,
        'tiempo_gastado' => 3661,
    ]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'puntaje' => 55,
        'tiempo_gastado' => 125,
    ]);
    $pregunta->seccionesExamen()->attach($seccion);
    $respuesta = RespuestaUsuario::factory()->create([
        'seccion_examen_id' => $seccion->id,
        'pregunta_id' => $pregunta->id,
        'opcion_seleccionada_id' => $opcion->id,
    ]);
    $estadistica = EstadisticaUsuario::factory()->create([
        'user_id' => $user->id,
        'materia_id' => $materia->id,
        'puntaje_promedio' => 55,
        'tiempo_total_gastado' => 3661,
    ]);

    return compact('user', 'materia', 'topico', 'pregunta', 'opcion', 'examen', 'seccion', 'respuesta', 'estadistica');
}

function invokeFilamentProtected(object|string $target, string $method, array $arguments = []): mixed
{
    $reflection = new ReflectionMethod($target, $method);
    $reflection->setAccessible(true);

    return $reflection->invokeArgs(is_string($target) ? null : $target, $arguments);
}

it('configures Filament resource infolists and tables directly', function (): void {
    $records = filamentCoverageGraph();

    expect(ExamenResource::infolist(Schema::make()))
        ->toBeInstanceOf(Schema::class)
        ->and(EstadisticaUsuarioResource::infolist(Schema::make()))
        ->toBeInstanceOf(Schema::class)
        ->and(ExamenInfolist::configure(Schema::make()->record($records['examen'])))
        ->toBeInstanceOf(Schema::class)
        ->and(EstadisticaUsuarioInfolist::configure(Schema::make()->record($records['estadistica'])))
        ->toBeInstanceOf(Schema::class);

    $listExamens = Livewire::test(App\Filament\Resources\Examens\Pages\ListExamens::class)->instance();
    $listStats = Livewire::test(App\Filament\Resources\EstadisticaUsuarios\Pages\ListEstadisticaUsuarios::class)->instance();
    $listQuestions = Livewire::test(App\Filament\Resources\Preguntas\Pages\ListPreguntas::class)->instance();
    $listMaterials = Livewire::test(ListMaterialApoyos::class)->instance();

    expect(ExamenResource::table(Table::make($listExamens)))
        ->toBeInstanceOf(Table::class)
        ->and(MaterialApoyoResource::table(Table::make($listMaterials)))
        ->toBeInstanceOf(Table::class)
        ->and(MaterialApoyosTable::configure(Table::make($listMaterials)))
        ->toBeInstanceOf(Table::class)
        ->and(ExamensTable::configure(Table::make($listExamens)))
        ->toBeInstanceOf(Table::class)
        ->and(EstadisticaUsuarioResource::table(Table::make($listStats)))
        ->toBeInstanceOf(Table::class)
        ->and(EstadisticaUsuariosTable::configure(Table::make($listStats)))
        ->toBeInstanceOf(Table::class)
        ->and(PreguntasTable::configure(Table::make($listQuestions)))
        ->toBeInstanceOf(Table::class);
});

it('configures Filament relation manager forms and tables directly', function (string $managerClass, string $pageClass, string $ownerKey): void {
    $records = filamentCoverageGraph();
    $instance = Livewire::test($managerClass, [
        'ownerRecord' => $records[$ownerKey],
        'pageClass' => $pageClass,
    ])->instance();

    expect($instance->form(Schema::make($instance)))
        ->toBeInstanceOf(Schema::class)
        ->and($instance->table(Table::make($instance)))
        ->toBeInstanceOf(Table::class)
        ->and(invokeFilamentProtected($instance, 'getTableEmptyStateHeading'))
        ->toBeString()
        ->and(invokeFilamentProtected($instance, 'getTableEmptyStateDescription'))
        ->toBeString()
        ->and(invokeFilamentProtected($instance, 'getTableEmptyStateIcon'))
        ->toBeString();
})->with([
    'materia topicos' => [TopicosRelationManager::class, EditMateria::class, 'materia'],
    'materia preguntas' => [MateriaPreguntasRelationManager::class, EditMateria::class, 'materia'],
    'topico preguntas' => [TopicoPreguntasRelationManager::class, EditTopico::class, 'topico'],
    'pregunta opciones' => [OpcionesRespuestaRelationManager::class, EditPregunta::class, 'pregunta'],
    'examen secciones' => [SeccionesExamenRelationManager::class, EditExamen::class, 'examen'],
    'seccion preguntas' => [SeccionPreguntasRelationManager::class, EditSeccionExamen::class, 'seccion'],
    'seccion respuestas' => [RespuestasUsuarioRelationManager::class, EditSeccionExamen::class, 'seccion'],
    'user examenes' => [ExamenesRelationManager::class, EditUser::class, 'user'],
    'user estadisticas' => [EstadisticasRelationManager::class, EditUser::class, 'user'],
]);

it('covers Filament formatting helpers and widget change branches', function (): void {
    $records = filamentCoverageGraph();

    User::factory()->create(['created_at' => now()->subDays(10)]);
    Pregunta::factory()->create([
        'materia_id' => $records['materia']->id,
        'topico_id' => $records['topico']->id,
        'texto_pregunta' => 'Pregunta media coverage',
        'nivel_dificultad' => NivelDificultad::Medio,
        'created_at' => now()->subDays(40),
    ]);
    Examen::factory()->create([
        'estado' => EstadoExamen::Completado,
        'fecha_completado' => now()->subMonths(2),
        'puntaje_total' => 10,
    ]);
    Examen::factory()->create([
        'estado' => EstadoExamen::Completado,
        'fecha_completado' => now()->subDays(10),
        'puntaje_total' => 20,
    ]);

    expect(invokeFilamentProtected(ExamenInfolist::class, 'formatTime', [0]))->toBe('00:00:00')
        ->and(invokeFilamentProtected(ExamenInfolist::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(ExamenInfolist::class, 'estadoColor', [EstadoExamen::EnProgreso]))->toBe('info')
        ->and(invokeFilamentProtected(ExamenInfolist::class, 'estadoColor', [EstadoExamen::Completado]))->toBe('success')
        ->and(invokeFilamentProtected(ExamenInfolist::class, 'estadoColor', [EstadoExamen::Abandonado]))->toBe('danger')
        ->and(invokeFilamentProtected(EstadisticaUsuarioForm::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(EstadisticaUsuarioForm::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(EstadisticaUsuarioForm::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(EstadisticasRelationManager::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(EstadisticaUsuarioInfolist::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(EstadisticaUsuarioInfolist::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(EstadisticaUsuarioInfolist::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(EstadisticaUsuariosTable::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(EstadisticaUsuariosTable::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(EstadisticaUsuariosTable::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(ExamensTable::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(ExamensTable::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(ExamensTable::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(SeccionesExamenRelationManager::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(SeccionesExamenRelationManager::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(ExamenesRelationManager::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(ExamenesRelationManager::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(ExamenesRelationManager::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(EstadisticasRelationManager::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(EstadisticasRelationManager::class, 'formatTime', [3661]))->toBe('01:01:01')
        ->and(invokeFilamentProtected(RespuestaUsuariosTable::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(RespuestaUsuariosTable::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(RespuestasUsuarioRelationManager::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(RespuestasUsuarioRelationManager::class, 'formatTime', [125]))->toBe('02:05')
        ->and(invokeFilamentProtected(SeccionExamensTable::class, 'formatTime', [0]))->toBe('00:00')
        ->and(invokeFilamentProtected(SeccionExamensTable::class, 'formatTime', [125]))->toBe('02:05')
        ->and(Livewire::test(TotalStudentsWidget::class))->assertOk()
        ->and(Livewire::test(TotalQuestionsWidget::class))->assertOk()
        ->and(Livewire::test(TotalExamsWidget::class))->assertOk()
        ->and(Livewire::test(AverageScoreWidget::class))->assertOk()
        ->and(Livewire::test(ListPreguntas::class)->assertSee('Pregunta media coverage'))->assertOk();
});

it('executes Filament abandoned exam bulk actions', function (): void {
    $records = filamentCoverageGraph();

    $keptExam = Examen::factory()->create([
        'user_id' => $records['user']->id,
        'estado' => EstadoExamen::Completado,
    ]);
    $abandonedExam = Examen::factory()->create([
        'user_id' => $records['user']->id,
        'estado' => EstadoExamen::Abandonado,
    ]);

    Livewire::test(ExamenesRelationManager::class, [
        'ownerRecord' => $records['user'],
        'pageClass' => EditUser::class,
    ])->callTableBulkAction(['delete_abandoned'], collect([$keptExam, $abandonedExam]));

    expect($keptExam->fresh())->not->toBeNull()
        ->and($abandonedExam->fresh())->toBeNull();

    $keptGlobalExam = Examen::factory()->create(['estado' => EstadoExamen::Completado]);
    $abandonedGlobalExam = Examen::factory()->create(['estado' => EstadoExamen::Abandonado]);

    Livewire::test(ListExamens::class)
        ->callTableBulkAction(['delete_abandoned'], collect([$keptGlobalExam, $abandonedGlobalExam]));

    expect($keptGlobalExam->fresh())->not->toBeNull()
        ->and($abandonedGlobalExam->fresh())->toBeNull();
});

it('covers remaining domain edge branches', function (): void {
    $materia = Materia::factory()->create(['activo' => true]);
    $topico = Topico::factory()->create(['materia_id' => $materia->id]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
    ]);

    foreach (OpcionRespuesta::LETRAS_DISPONIBLES as $letter) {
        OpcionRespuesta::factory()->create([
            'pregunta_id' => $pregunta->id,
            'letra_opcion' => $letter,
        ]);
    }

    $overflowOption = OpcionRespuesta::factory()->create([
        'pregunta_id' => $pregunta->id,
        'letra_opcion' => null,
    ]);

    expect($overflowOption->letra_opcion)->toBe('G');

    app(ExamenService::class)->generarExamen(new User, App\Enums\TipoExamen::Completo);
})->throws(QueryException::class);

it('configures the OpenAPI bearer security scheme', function (): void {
    $openApi = OpenApi::make('3.1.0');

    AppServiceProvider::secureOpenApi($openApi);
    foreach (Scramble::configure()->documentTransformers->all() as $transformer) {
        if ($transformer instanceof Closure) {
            $transformer($openApi);
        }
    }

    expect($openApi->security)->not->toBeEmpty()
        ->and($openApi->components->securitySchemes)->toHaveCount(1);
});

it('rolls back score calculation when a section response query fails', function (): void {
    $examen = Examen::factory()->make();
    $throwingSection = new class extends SeccionExamen
    {
        public function respuestasUsuario(): HasMany
        {
            throw new RuntimeException('No se pudieron leer las respuestas.');
        }
    };

    $examen->setRelation('seccionesExamen', collect([$throwingSection]));

    app(ExamenService::class)->calcularPuntaje($examen);
})->throws(RuntimeException::class, 'No se pudieron leer las respuestas.');
