<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialApoyoResource;
use App\Models\MaterialApoyo;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group('Materiales de apoyo', weight: 4)]
class MaterialApoyoController extends Controller
{
    #[Endpoint(
        operationId: 'listMaterialesApoyo',
        title: 'Listar materiales de apoyo',
        description: 'Devuelve materiales activos basados en embeds externos de YouTube o Google Drive.'
    )]
    #[QueryParameter('materia', description: 'ID o slug de la materia', type: 'string', example: 'matematicas')]
    #[QueryParameter('tipo', description: 'Tipo de embed: youtube o google_drive', type: 'string', example: 'youtube')]
    #[QueryParameter('q', description: 'Texto para buscar en título o descripción', type: 'string', example: 'lectura')]
    #[Response(200, 'Lista de materiales activos')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $materials = MaterialApoyo::query()
            ->with('materia')
            ->activo()
            ->when($request->string('materia')->toString(), function ($query, string $materia): void {
                $query->whereHas('materia', function ($materiaQuery) use ($materia): void {
                    $materiaQuery->where('slug', $materia);

                    if (is_numeric($materia)) {
                        $materiaQuery->orWhere('id', (int) $materia);
                    }
                });
            })
            ->when($request->string('tipo')->toString(), fn ($query, string $tipo) => $query->where('tipo', $tipo))
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('titulo', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->orderBy('orden_visualizacion')
            ->orderBy('titulo')
            ->get();

        return MaterialApoyoResource::collection($materials);
    }

    #[Endpoint(
        operationId: 'showMaterialApoyo',
        title: 'Obtener material de apoyo',
        description: 'Devuelve un material activo con su información de embed.'
    )]
    #[PathParameter('material', description: 'Slug del material de apoyo', type: 'string', example: 'guia-lectura-critica')]
    #[Response(200, 'Detalle del material')]
    #[Response(404, 'Material no encontrado')]
    public function show(MaterialApoyo $material): MaterialApoyoResource
    {
        if (! $material->activo) {
            abort(404, 'Material no encontrado.');
        }

        return new MaterialApoyoResource($material->load('materia'));
    }
}
