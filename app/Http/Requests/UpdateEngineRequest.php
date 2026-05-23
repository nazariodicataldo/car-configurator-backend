<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEngineRequest extends FormRequest
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
        $engine_id = $this->route('engine');

        return [
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                'unique:engines,name,' . $engine_id,
            ],
            'transmission' => ['sometimes', 'string', 'in:automatico,manuale'],
            'fuel' => [
                'sometimes',
                'string',
                'in:benzina,diesel,elettrico,gpl,metano',
            ],
            'power' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:999',
            ],
            'emissions' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:999',
            ],
            'consumption' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:999',
            ],
        ];
    }
}
