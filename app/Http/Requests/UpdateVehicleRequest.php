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
        $vehicle_id = $this->route('vehicle')->id;
        return [
            'name' => [
                'sometimes',
                'string',
                'min:2',
                'max:50',
                'unique:vehicles,name,' . $vehicle_id,
            ],
            'body_type' => [
                'sometimes',
                'string',
                'in:berlina,due volumi,suv,monovolume,coupe,cabriolet,furgone,autobus,camion',
            ],
            'seats' => ['sometimes', 'numeric', 'min:2', 'max:9'],
            'base_price' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'brand_id' => ['sometimes', 'uuid', 'exists:brands,id'],
        ];
    }
}
