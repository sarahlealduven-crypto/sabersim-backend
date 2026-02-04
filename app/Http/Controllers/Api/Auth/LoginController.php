<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

#[Group('Autenticación', weight: 0)]
class LoginController extends Controller
{
    #[Endpoint(
        operationId: 'login',
        title: 'Iniciar sesión',
        description: 'Autentica un usuario y devuelve un token de acceso.'
    )]
    #[Response(200, 'Usuario autenticado exitosamente')]
    #[Response(422, 'Credenciales inválidas')]
    /**
     * @unauthenticated
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('auth-token', ['*'], now()->addDays(30));

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token->plainTextToken,
        ]);
    }
}
