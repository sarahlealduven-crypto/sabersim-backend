<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

#[Group('Autenticación', weight: 0)]
class RegisterController extends Controller
{
    #[Endpoint(
        operationId: 'register',
        title: 'Registrar nuevo usuario',
        description: 'Crea una nueva cuenta de usuario y devuelve el token de autenticación.'
    )]
    #[Response(201, 'Usuario registrado exitosamente')]
    #[Response(422, 'Validación fallida')]
    /**
     * @unauthenticated
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'grade_level' => $validated['grade_level'] ?? 11,
            'current_level' => 1,
            'total_xp' => 0,
        ]);

        $token = $user->createToken('auth-token', ['*'], now()->addDays(30));

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token->plainTextToken,
        ], 201);
    }
}
