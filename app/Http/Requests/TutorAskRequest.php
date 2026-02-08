<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TutorAskRequest extends FormRequest
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
            'question' => ['required', 'string', 'max:2000'],
            'materia_id' => ['nullable', 'integer', 'exists:materias,id'],
            'topico_id' => [
                'nullable',
                'integer',
                'exists:topicos,id',
                Rule::when($this->filled('materia_id'), [
                    Rule::exists('topicos', 'id')->where('materia_id', $this->input('materia_id')),
                ]),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'La pregunta es obligatoria.',
            'question.max' => 'La pregunta no puede superar los 2000 caracteres.',
            'topico_id.exists' => 'El tema seleccionado no existe o no pertenece a la materia indicada.',
        ];
    }
}
