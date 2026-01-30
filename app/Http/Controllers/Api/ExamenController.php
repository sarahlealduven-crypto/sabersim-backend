<?php

namespace App\Http\Controllers\Api;

use App\Enums\EstadoExamen;
use App\Enums\TipoExamen;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinalizarExamenRequest;
use App\Http\Requests\IniciarExamenRequest;
use App\Http\Requests\SubmitRespuestaRequest;
use App\Http\Resources\ExamenResource;
use App\Models\Examen;
use App\Models\OpcionRespuesta;
use App\Models\RespuestaUsuario;
use App\Services\ExamenService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group('Exámenes', weight: 1)]
class ExamenController extends Controller
{
    public function __construct(
        private ExamenService $examenService
    ) {}

    #[Endpoint(
        operationId: 'createExam',
        title: 'Crear nuevo examen',
        description: 'Crea e inicia un nuevo examen (completo o por materia).'
    )]
    #[Response(201, 'Examen creado exitosamente')]
    #[Response(422, 'Validación fallida')]
    public function store(IniciarExamenRequest $request): ExamenResource
    {
        $user = request()->user();

        $tipoExamen = $request->validated('tipo_examen');

        $examen = $this->examenService->generarExamen(
            $user,
            match ($tipoExamen) {
                'completo' => TipoExamen::Completo,
                'por_materia' => TipoExamen::PorMateria,
            },
            $request->validated('materia_id')
        );

        $examen->load(['seccionesExamen.materia', 'seccionesExamen.preguntas.opcionesRespuesta']);

        return new ExamenResource($examen);
    }

    #[Endpoint(
        operationId: 'listExams',
        title: 'Listar exámenes del usuario',
        description: 'Devuelve una lista de todos los exámenes realizados por el usuario autenticado, ordenados por fecha de inicio.'
    )]
    #[Response(200, 'Lista de exámenes del usuario')]
    public function index(): AnonymousResourceCollection
    {
        $examenes = request()->user()
            ->examenes()
            ->with(['seccionesExamen.materia'])
            ->latest('fecha_inicio')
            ->get();

        return ExamenResource::collection($examenes);
    }

    #[Endpoint(
        operationId: 'showExam',
        title: 'Obtener detalles del examen',
        description: 'Devuelve información detallada sobre un examen específico, incluyendo todas las secciones y preguntas.'
    )]
    #[PathParameter('examen', description: 'ID del examen', type: 'int', example: 1)]
    #[Response(200, 'Detalles del examen')]
    #[Response(403, 'No autorizado para acceder a este examen')]
    #[Response(404, 'Examen no encontrado')]
    public function show(Examen $examen): ExamenResource
    {
        $this->authorize('view', $examen);

        $examen->load(['seccionesExamen.materia', 'seccionesExamen.preguntas.opcionesRespuesta']);

        return new ExamenResource($examen);
    }

    #[Endpoint(
        operationId: 'submitAnswer',
        title: 'Enviar respuesta',
        description: 'Envía una respuesta para una pregunta del examen y devuelve si es correcta.'
    )]
    #[PathParameter('examen', description: 'ID del examen', type: 'int', example: 1)]
    #[Response(200, 'Respuesta enviada exitosamente')]
    #[Response(403, 'No autorizado para acceder a este examen')]
    #[Response(404, 'Pregunta u opción no encontrada')]
    #[Response(422, 'Validación fallida')]
    public function submitRespuesta(Examen $examen, SubmitRespuestaRequest $request): JsonResponse
    {
        $this->authorize('update', $examen);

        if ($examen->estado !== EstadoExamen::EnProgreso) {
            return response()->json([
                'message' => 'El examen ya ha sido completado o abandonado.',
            ], 400);
        }

        $seccionExamen = $examen->seccionesExamen()->findOrFail($request->validated('seccion_examen_id'));
        $opcion = OpcionRespuesta::findOrFail($request->validated('opcion_id'));

        $esCorrecta = $opcion->es_correcta;

        RespuestaUsuario::updateOrCreate([
            'seccion_examen_id' => $seccionExamen->id,
            'pregunta_id' => $request->validated('pregunta_id'),
        ], [
            'opcion_seleccionada_id' => $opcion->id,
            'es_correcta' => $esCorrecta,
            'tiempo_gastado' => $request->validated('tiempo_gastado') ?? now()->diffInSeconds($examen->fecha_inicio),
        ]);

        return response()->json([
            'es_correcta' => $esCorrecta,
        ]);
    }

    #[Endpoint(
        operationId: 'finishExam',
        title: 'Finalizar examen',
        description: 'Finaliza el examen, calcula el puntaje y devuelve los resultados.'
    )]
    #[PathParameter('examen', description: 'ID del examen', type: 'int', example: 1)]
    #[Response(200, 'Examen finalizado y calificado exitosamente')]
    #[Response(400, 'Examen ya completado o abandonado')]
    #[Response(403, 'No autorizado para acceder a este examen')]
    #[Response(404, 'Examen no encontrado')]
    public function finalizar(Examen $examen, FinalizarExamenRequest $request): JsonResponse
    {
        $this->authorize('update', $examen);

        if ($examen->estado !== EstadoExamen::EnProgreso) {
            return response()->json([
                'message' => 'El examen ya ha sido completado o abandonado.',
            ], 400);
        }

        $this->examenService->calcularPuntaje($examen);

        $examen->load(['seccionesExamen.materia', 'seccionesExamen.preguntas.opcionesRespuesta']);

        return response()->json(new ExamenResource($examen));
    }

    #[Endpoint(
        operationId: 'abandonExam',
        title: 'Abandonar examen',
        description: 'Marca el examen como abandonado sin completarlo.'
    )]
    #[PathParameter('examen', description: 'ID del examen', type: 'int', example: 1)]
    #[Response(200, 'Examen abandonado exitosamente')]
    #[Response(403, 'No autorizado para acceder a este examen')]
    #[Response(404, 'Examen no encontrado')]
    public function abandonar(Examen $examen): JsonResponse
    {
        $this->authorize('delete', $examen);

        $this->examenService->abandonarExamen($examen);

        return response()->json([
            'message' => 'Examen abandonado exitosamente.',
        ]);
    }
}
