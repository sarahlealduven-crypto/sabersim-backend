<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EstadisticaUsuarioResource;
use App\Models\EstadisticaUsuario;
use App\Models\Materia;
use App\Services\EstadisticaService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Support\Facades\Auth;

#[Group('Estadísticas', weight: 2)]
class EstadisticaController extends Controller
{
    public function __construct(
        private EstadisticaService $estadisticaService
    ) {}

    #[Endpoint(
        operationId: 'getUserStatistics',
        title: 'Obtener estadísticas del usuario',
        description: 'Devuelve las estadísticas generales del usuario en todas las materias.'
    )]
    #[Response(200, 'Estadísticas del usuario')]
    #[Response(401, 'No autenticado')]
    public function index(): EstadisticaUsuarioResource
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->estadisticaService->actualizarEstadisticas($user);

        $estadisticas = $user->estadisticasUsuario()
            ->with('materia')
            ->get();

        return new EstadisticaUsuarioResource($estadisticas->whereNull('materia_id')->first() ?? $estadisticas->first());
    }

    #[Endpoint(
        operationId: 'getSubjectStatistics',
        title: 'Obtener estadísticas por materia',
        description: 'Devuelve las estadísticas del usuario para una materia específica.'
    )]
    #[PathParameter('materia', description: 'Slug de la materia', type: 'string', example: 'matematicas')]
    #[Response(200, 'Estadísticas de la materia')]
    #[Response(401, 'No autenticado')]
    #[Response(404, 'Estadísticas de materia no encontradas')]
    public function show(Materia $materia): EstadisticaUsuarioResource
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $this->estadisticaService->actualizarEstadisticas($user);

        $estadistica = EstadisticaUsuario::where('user_id', $user->id)
            ->where('materia_id', $materia->id)
            ->firstOrFail();

        return new EstadisticaUsuarioResource($estadistica);
    }
}
