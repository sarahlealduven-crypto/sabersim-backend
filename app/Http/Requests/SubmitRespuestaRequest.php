<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRespuestaRequest extends FormRequest
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
             * ID de la sección del examen donde pertenece la pregunta.
             *
             * @example 1
             */
            'seccion_examen_id' => ['required', 'integer', 'exists:secciones_examen,id'],

            /**
             * ID de la pregunta que se está respondiendo.
             *
             * @example 5
             */
            'pregunta_id' => ['required', 'integer', 'exists:preguntas,id'],

            /**
             * ID de la opción de respuesta seleccionada.
             *
             * @example 12
             */
            'opcion_id' => ['required', 'integer', 'exists:opciones_respuesta,id'],

            /**
             * Tiempo gastado en esta pregunta en segundos (opcional).
             *
             * @example 15
             */
            'tiempo_gastado' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
