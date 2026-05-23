<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
                'sometimes',
                'string',
                'min:2',
                'max:50',
                'unique:vehicles,name',
            ],
            'body_type' => ['sometimes', 'string', 'in:berlina,due volumi,suv,monovolume,coupe,cabriolet,furgone,autobus,camion'],
            'seats' => ['sometimes', 'string', 'in:2,4,5,6,7,8,9'],
            'base_img' => ['nullable', 'image', 'max:2048'],
            'base_price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999'],
            'brand_id' => ['sometimes', 'uuid', 'exists:brands,id']
        ];
    }
}
