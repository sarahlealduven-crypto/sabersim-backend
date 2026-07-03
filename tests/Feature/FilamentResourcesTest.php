<?php

use App\Enums\EstadoExamen;
use App\Enums\NivelDificultad;
use App\Enums\TipoExamen;
use App\Filament\Resources\EstadisticaUsuarios\Pages\CreateEstadisticaUsuario;
use App\Filament\Resources\EstadisticaUsuarios\Pages\EditEstadisticaUsuario;
use App\Filament\Resources\EstadisticaUsuarios\Pages\ListEstadisticaUsuarios;
use App\Filament\Resources\Examens\Pages\CreateExamen;
use App\Filament\Resources\Examens\Pages\EditExamen;
use App\Filament\Resources\Examens\Pages\ListExamens;
use App\Filament\Resources\MaterialApoyos\Pages\CreateMaterialApoyo;
use App\Filament\Resources\MaterialApoyos\Pages\EditMaterialApoyo;
use App\Filament\Resources\MaterialApoyos\Pages\ListMaterialApoyos;
use App\Filament\Resources\Materias\Pages\CreateMateria;
use App\Filament\Resources\Materias\Pages\EditMateria;
use App\Filament\Resources\Materias\Pages\ListMaterias;
use App\Filament\Resources\OpcionRespuestas\Pages\CreateOpcionRespuesta;
use App\Filament\Resources\OpcionRespuestas\Pages\EditOpcionRespuesta;
use App\Filament\Resources\OpcionRespuestas\Pages\ListOpcionRespuestas;
use App\Filament\Resources\Preguntas\Pages\CreatePregunta;
use App\Filament\Resources\Preguntas\Pages\EditPregunta;
use App\Filament\Resources\Preguntas\Pages\ListPreguntas;
use App\Filament\Resources\RespuestaUsuarios\Pages\CreateRespuestaUsuario;
use App\Filament\Resources\RespuestaUsuarios\Pages\EditRespuestaUsuario;
use App\Filament\Resources\RespuestaUsuarios\Pages\ListRespuestaUsuarios;
use App\Filament\Resources\SeccionExamens\Pages\CreateSeccionExamen;
use App\Filament\Resources\SeccionExamens\Pages\EditSeccionExamen;
use App\Filament\Resources\SeccionExamens\Pages\ListSeccionExamens;
use App\Filament\Resources\Topicos\Pages\CreateTopico;
use App\Filament\Resources\Topicos\Pages\EditTopico;
use App\Filament\Resources\Topicos\Pages\ListTopicos;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\EstadisticaUsuario;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\MaterialApoyo;
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

function filamentResourceGraph(): array
{
    $user = User::factory()->create([
        'name' => 'Admin Student',
        'email' => 'admin-student@example.com',
    ]);
    $materia = Materia::factory()->create([
        'nombre' => 'Matemáticas',
        'slug' => 'matematicas-filament',
        'icono' => null,
    ]);
    $topico = Topico::factory()->create([
        'materia_id' => $materia->id,
        'nombre' => 'Álgebra',
        'slug' => 'algebra-filament',
    ]);
    $pregunta = Pregunta::factory()->create([
        'materia_id' => $materia->id,
        'topico_id' => $topico->id,
        'texto_pregunta' => '¿Cuánto es 2 + 2?',
        'nivel_dificultad' => NivelDificultad::Facil,
    ]);
    $opcion = OpcionRespuesta::factory()->create([
        'pregunta_id' => $pregunta->id,
        'letra_opcion' => 'A',
        'texto_opcion' => 'Cuatro',
        'es_correcta' => true,
    ]);
    $examen = Examen::factory()->create([
        'user_id' => $user->id,
        'tipo_examen' => TipoExamen::PorMateria,
        'estado' => EstadoExamen::Completado,
        'fecha_completado' => now(),
        'puntaje_total' => 90,
        'tiempo_gastado' => 3600,
    ]);
    $seccion = SeccionExamen::factory()->create([
        'examen_id' => $examen->id,
        'materia_id' => $materia->id,
        'total_preguntas' => 1,
        'respuestas_correctas' => 1,
        'puntaje' => 100,
    ]);
    $pregunta->seccionesExamen()->attach($seccion);
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
        'puntaje_promedio' => 100,
        'mejor_puntaje' => 100,
        'tiempo_total_gastado' => 3600,
        'fecha_ultimo_examen' => now(),
    ]);
    $material = MaterialApoyo::factory()->create([
        'materia_id' => $materia->id,
        'titulo' => 'Guía de funciones lineales',
        'slug' => 'guia-funciones-lineales',
        'source_url' => 'https://www.youtube.com/watch?v=jjO21znJdiE',
    ]);

    return compact('user', 'materia', 'topico', 'pregunta', 'opcion', 'examen', 'seccion', 'respuesta', 'estadistica', 'material');
}

it('loads every Filament resource list page with records', function (string $pageClass, string $visibleText): void {
    $records = filamentResourceGraph();
    actingAs($records['user']);

    Livewire::test($pageClass)
        ->assertOk()
        ->assertSee($visibleText);
})->with([
    'materias' => [ListMaterias::class, 'Matemáticas'],
    'materiales' => [ListMaterialApoyos::class, 'Guía de funciones lineales'],
    'topicos' => [ListTopicos::class, 'Álgebra'],
    'preguntas' => [ListPreguntas::class, '¿Cuánto es 2 + 2?'],
    'opciones' => [ListOpcionRespuestas::class, 'Cuatro'],
    'examenes' => [ListExamens::class, 'Admin Student'],
    'secciones' => [ListSeccionExamens::class, 'Matemáticas'],
    'respuestas' => [ListRespuestaUsuarios::class, 'Cuatro'],
    'estadisticas' => [ListEstadisticaUsuarios::class, 'Admin Student'],
    'usuarios' => [ListUsers::class, 'Admin Student'],
]);

it('has the subject placeholder image used by the Filament table', function (): void {
    expect(public_path('images/placeholder-subject.svg'))
        ->toBeFile();
});

it('loads every Filament resource create page', function (string $pageClass): void {
    $records = filamentResourceGraph();
    actingAs($records['user']);

    Livewire::test($pageClass)
        ->assertOk();
})->with([
    CreateMateria::class,
    CreateMaterialApoyo::class,
    CreateTopico::class,
    CreatePregunta::class,
    CreateOpcionRespuesta::class,
    CreateExamen::class,
    CreateSeccionExamen::class,
    CreateRespuestaUsuario::class,
    CreateEstadisticaUsuario::class,
    CreateUser::class,
]);

it('loads every Filament resource edit page with form state', function (string $pageClass, string $recordKey, array $expectedState): void {
    $records = filamentResourceGraph();
    actingAs($records['user']);

    Livewire::test($pageClass, ['record' => $records[$recordKey]->getRouteKey()])
        ->assertOk()
        ->assertSchemaStateSet($expectedState);
})->with([
    'materia' => [EditMateria::class, 'materia', ['nombre' => 'Matemáticas']],
    'material' => [EditMaterialApoyo::class, 'material', ['titulo' => 'Guía de funciones lineales']],
    'topico' => [EditTopico::class, 'topico', ['nombre' => 'Álgebra']],
    'pregunta' => [EditPregunta::class, 'pregunta', ['texto_pregunta' => '¿Cuánto es 2 + 2?']],
    'opcion' => [EditOpcionRespuesta::class, 'opcion', ['texto_opcion' => 'Cuatro']],
    'examen' => [EditExamen::class, 'examen', ['puntaje_total' => '90.00']],
    'seccion' => [EditSeccionExamen::class, 'seccion', ['total_preguntas' => 1]],
    'respuesta' => [EditRespuestaUsuario::class, 'respuesta', ['es_correcta' => true]],
    'estadistica' => [EditEstadisticaUsuario::class, 'estadistica', ['total_preguntas_respondidas' => 1]],
    'user' => [EditUser::class, 'user', ['name' => 'Admin Student']],
]);

it('creates and edits a support material through Filament forms', function (): void {
    $materia = Materia::factory()->create([
        'nombre' => 'Lectura Crítica',
        'slug' => 'lectura-critica-admin',
    ]);

    actingAs(User::factory()->create());

    Livewire::test(CreateMaterialApoyo::class)
        ->fillForm([
            'materia_id' => $materia->id,
            'titulo' => 'Lectura crítica desde YouTube',
            'slug' => 'lectura-critica-youtube-admin',
            'descripcion' => 'Video de apoyo para practicar lectura crítica.',
            'tipo' => MaterialApoyo::TIPO_YOUTUBE,
            'source_url' => 'https://youtu.be/jjO21znJdiE',
            'thumbnail_url' => 'https://img.youtube.com/vi/jjO21znJdiE/hqdefault.jpg',
            'duracion' => 'Video',
            'orden_visualizacion' => 8,
            'activo' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $material = MaterialApoyo::query()->where('slug', 'lectura-critica-youtube-admin')->firstOrFail();

    expect($material)
        ->embed_url->toBe('https://www.youtube.com/embed/jjO21znJdiE')
        ->activo->toBeTrue();

    Livewire::test(EditMaterialApoyo::class, ['record' => $material->getRouteKey()])
        ->fillForm([
            'materia_id' => $materia->id,
            'titulo' => 'Cuadernillo Drive de lectura crítica',
            'slug' => 'cuadernillo-drive-lectura-critica-admin',
            'descripcion' => 'Documento de apoyo externo.',
            'tipo' => MaterialApoyo::TIPO_GOOGLE_DRIVE,
            'source_url' => 'https://drive.google.com/file/d/148MvqVppDg7w0x9HJQnadS2OaS2ymiOx/view?usp=sharing',
            'thumbnail_url' => null,
            'duracion' => 'Guía',
            'orden_visualizacion' => 9,
            'activo' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($material->fresh())
        ->titulo->toBe('Cuadernillo Drive de lectura crítica')
        ->slug->toBe('cuadernillo-drive-lectura-critica-admin')
        ->tipo->toBe(MaterialApoyo::TIPO_GOOGLE_DRIVE)
        ->embed_url->toBe('https://drive.google.com/file/d/148MvqVppDg7w0x9HJQnadS2OaS2ymiOx/preview')
        ->activo->toBeFalse();
});

it('creates and edits a subject through Filament forms', function (): void {
    actingAs(User::factory()->create());

    Livewire::test(CreateMateria::class)
        ->fillForm([
            'nombre' => 'Ciencias',
            'slug' => 'ciencias',
            'descripcion' => 'Banco de ciencias',
            'icono' => null,
            'cantidad_preguntas' => 25,
            'tiempo_limite' => 45,
            'orden_visualizacion' => 4,
            'activo' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $materia = Materia::query()->where('slug', 'ciencias')->firstOrFail();

    Livewire::test(EditMateria::class, ['record' => $materia->getRouteKey()])
        ->fillForm([
            'nombre' => 'Ciencias Naturales',
            'slug' => 'ciencias-naturales-admin',
            'descripcion' => 'Banco actualizado',
            'icono' => null,
            'cantidad_preguntas' => 30,
            'tiempo_limite' => 50,
            'orden_visualizacion' => 5,
            'activo' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($materia->fresh())
        ->nombre->toBe('Ciencias Naturales')
        ->slug->toBe('ciencias-naturales-admin')
        ->activo->toBeFalse();
});
