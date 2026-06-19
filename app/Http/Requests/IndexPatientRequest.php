<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasPortugueseValidationMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexPatientRequest extends FormRequest
{
    use HasPortugueseValidationMessages;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:id,name,cpf,cns,birth_date,gender,phone,address_id,created_at,updated_at',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'gender' => 'nullable|string|in:M,F,O',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'page' => 'página',
            'per_page' => 'itens por página',
            'search' => 'busca',
            'sort_by' => 'campo de ordenação',
            'sort_dir' => 'direção da ordenação',
            'gender' => 'gênero',
        ];
    }
}
