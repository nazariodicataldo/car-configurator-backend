<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'vehicle_id' => ['required', 'uuid', 'exists:vehicles,id'],
            'vehicle_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'engine_id' => ['required', 'uuid', 'exists:engines,id'],
            'engine_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'setup_id' => ['required', 'uuid', 'exists:setups,id'],
            'setup_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'color_id' => ['required', 'uuid', 'exists:colors,id'],
            'color_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'optionals_id' => ['nullable', 'array'],
            'optionals_id.*' => ['uuid', 'exists:optionals,id'],
            'total_price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
        ];
    }
}
