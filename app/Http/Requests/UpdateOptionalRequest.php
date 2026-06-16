<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOptionalRequest extends FormRequest
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
        $optional_id = $this->route('optional')->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                'unique:optionals,name,' . $optional_id,
            ],
            'category' => [
                'sometimes',
                'string',
                'in:alimentazione,sicurezza,comodità,accessori',
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:300'],
        ];
    }
}
