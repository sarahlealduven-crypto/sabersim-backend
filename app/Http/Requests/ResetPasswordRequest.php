<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * Correo electrónico del usuario (requerido para olvidar contraseña).
             *
             * @example "juan@example.com"
             */
            'email' => ['required', 'email'],

            /**
             * Token de restablecimiento de contraseña del enlace del correo (requerido para restablecer).
             *
             * @example "a1b2c3d4e5f6..."
             */
            'token' => ['required', 'string'],

            /**
             * Nueva contraseña (requerido para restablecer, mínimo 8 caracteres).
             *
             * @example "nuevacontraseña123"
             */
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            /**
             * Confirmación de nueva contraseña (requerido para restablecer).
             *
             * @example "nuevacontraseña123"
             */
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
