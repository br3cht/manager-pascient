<?php

namespace App\Http\Requests\Patient;

use App\Http\Requests\Concerns\HasPortugueseValidationMessages;
use App\Rules\Cpf;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PatientUpdateRequest extends FormRequest
{
    use HasPortugueseValidationMessages {
        messages as portugueseValidationMessages;
    }

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
            'name' => 'required|string',
            'cpf' => ['required', 'digits:11', new Cpf],
            'cns' => 'required|digits:15',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:M,F,O',
            'phone' => 'nullable|digits:11',
            'address_id' => 'required|integer|exists:addresses,id',
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
            'name' => 'nome',
            'cpf' => 'CPF',
            'cns' => 'CNS',
            'birth_date' => 'data de nascimento',
            'gender' => 'gênero',
            'phone' => 'telefone',
            'address_id' => 'endereço',
        ];
    }

    /**
     * Get custom validation messages in Portuguese.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->portugueseValidationMessages(), [
            'phone.digits' => 'O campo telefone deve conter 11 dígitos, incluindo o DDD.',
        ]);
    }
}
