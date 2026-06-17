<?php

namespace App\Http\Requests\Concerns;

trait HasPortugueseValidationMessages
{
    /**
     * Get custom validation messages in Portuguese.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'O campo :attribute deve ser um e-mail válido.',
            'string' => 'O campo :attribute deve ser um texto.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'date' => 'O campo :attribute deve ser uma data válida.',
            'digits' => 'O campo :attribute deve conter exatamente :digits dígitos.',
            'exists' => 'O campo :attribute selecionado é inválido.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'max.string' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'in' => 'O campo :attribute selecionado é inválido.',
        ];
    }
}
