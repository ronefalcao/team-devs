<?php

namespace App\Http\Requests\Prd;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'origin' => ['required', 'string', 'in:human,ai'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'O projeto é obrigatório.',
            'project_id.exists' => 'Projeto não encontrado.',
            'module_id.exists' => 'Módulo não encontrado.',
            'title.required' => 'O título é obrigatório.',
            'content.required' => 'O conteúdo é obrigatório.',
            'origin.required' => 'A origem é obrigatória.',
            'origin.in' => 'A origem deve ser "human" ou "ai".',
        ];
    }
}
