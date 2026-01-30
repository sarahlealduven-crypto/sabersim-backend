<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IniciarExamenRequest extends FormRequest
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
             * Tipo de examen a crear.
             *
             * @example "completo"
             */
            'tipo_examen' => ['required', 'in:completo,por_materia'],

            /**
             * ID de la materia (requerido cuando tipo_examen es 'por_materia').
             *
             * @example 1
             */
            'materia_id' => ['required_if:tipo_examen,por_materia', 'integer', 'exists:materias,id'],
        ];
    }
}
