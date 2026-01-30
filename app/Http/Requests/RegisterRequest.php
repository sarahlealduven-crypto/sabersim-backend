<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
             * Nombre completo del usuario.
             *
             * @example "Juan Pérez"
             */
            'name' => ['required', 'string', 'max:255'],

            /**
             * Correo electrónico del usuario (debe ser único).
             *
             * @example "juan@example.com"
             */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

            /**
             * Contraseña del usuario (mínimo 8 caracteres).
             *
             * @example "password123"
             */
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            /**
             * Confirmación de contraseña (debe coincidir con password).
             *
             * @example "password123"
             */
            'password_confirmation' => ['required', 'string'],

            /**
             * Nivel de grado del estudiante (6-14, por defecto 11).
             *
             * @example 11
             */
            'grade_level' => ['nullable', 'integer', 'between:6,14'],
        ];
    }
}
