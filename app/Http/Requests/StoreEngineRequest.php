<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEngineRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:engines,name',
            ],
            'transmission' => ['required', 'string', 'in:automatico,manuale'],
            'fuel' => ['required', 'string', 'in:benzina,diesel,elettrico,gpl,metano'],
            'power' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999'],
            'emissions' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999'],
            'consumption' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999'],
        ];
    }
}
