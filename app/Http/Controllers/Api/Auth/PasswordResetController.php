<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

#[Group('Autenticación', weight: 0)]
class PasswordResetController extends Controller
{
    #[Endpoint(
        operationId: 'forgotPassword',
        title: 'Solicitar enlace de restablecimiento',
        description: 'Envía un enlace de restablecimiento de contraseña al correo del usuario.'
    )]
    #[Response(200, 'Enlace de restablecimiento enviado (o correo no encontrado)')]
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(['email' => $request->validated('email')]);

        return response()->json([
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Si el correo existe, enviaremos un enlace de restablecimiento.'
                : 'No podemos encontrar el correo proporcionado.',
        ]);
    }

    #[Endpoint(
        operationId: 'resetPassword',
        title: 'Restablecer contraseña',
        description: 'Restablece la contraseña del usuario usando el token del correo.'
    )]
    #[Response(200, 'Contraseña restablecida exitosamente')]
    #[Response(400, 'Token inválido o expirado')]
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $status = Password::reset($credentials, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        });

        return response()->json([
            'message' => $status === Password::PASSWORD_RESET
                ? 'Contraseña restablecida exitosamente.'
                : 'El token de restablecimiento es inválido o ha expirado.',
        ]);
    }
}
