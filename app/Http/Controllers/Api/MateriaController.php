<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MateriaResource;
use App\Models\Materia;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group('Materias', weight: 3)]
class MateriaController extends Controller
{
    #[Endpoint(
        operationId: 'listMaterias',
        title: 'Listar todas las materias',
        description: 'Devuelve una lista de todas las materias activas ordenadas por orden de visualización.'
    )]
    #[Response(200, 'Lista de materias activas')]
    public function index(): AnonymousResourceCollection
    {
        $materias = Materia::where('activo', true)
            ->orderBy('orden_visualizacion')
            ->get();

        return MateriaResource::collection($materias);
    }

    #[Endpoint(
        operationId: 'showMateria',
        title: 'Obtener detalles de la materia',
        description: 'Devuelve información detallada sobre una materia específica, incluyendo sus temas.'
    )]
    #[PathParameter('materia', description: 'Slug de la materia', type: 'string', example: 'matematicas')]
    #[Response(200, 'Detalles de la materia')]
    #[Response(404, 'Materia no encontrada')]
    public function show(Materia $materia): MateriaResource
    {
        $materia->load('topicos');

        return new MateriaResource($materia);
    }
}
