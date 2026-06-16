<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
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
        $color_id = $this->route('color')->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:colors,name,'. $color_id,
            ],
            'hex_code' => ['required', 'hex_color'],
            'img' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
