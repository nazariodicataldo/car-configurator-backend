<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'min:3', 'max:50'],
            'vehicle_id' => ['nullable', 'uuid', 'exists:vehicles,id'],
            'vehicle_price' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'engine_id' => ['nullable', 'uuid', 'exists:engines,id'],
            'engine_price' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'setup_id' => ['nullable', 'uuid', 'exists:setups,id'],
            'setup_price' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'color_id' => ['nullable', 'uuid', 'exists:colors,id'],
            'color_price' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'optionals' => ['nullable', 'array'],
            'optionals.*.id' => ['nullable', 'uuid', 'exists:optionals,id'],
        ];
    }
}
