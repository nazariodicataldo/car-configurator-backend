<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationOptionalRequest extends FormRequest
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
            'optional_price' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'is_included' => ['nullable', 'boolean'],
        ];
    }
}
