<?php

namespace App\Http\Requests\Address;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddressIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'required|integer',
            'per_page' => 'required|integer',
            'search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:id,street,zip_code,neighborhood,city,state,created_at,updated_at',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'state' => 'nullable|string|max:2',
        ];
    }
}
