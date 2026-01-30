<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;

#[Group('Autenticación', weight: 0)]
class LogoutController extends Controller
{
    #[Endpoint(
        operationId: 'logout',
        title: 'Cerrar sesión',
        description: 'Revoca el token de autenticación del usuario actual.'
    )]
    #[Response(200, 'Sesión cerrada exitosamente')]
    public function destroy(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}
