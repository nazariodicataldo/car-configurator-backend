<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
                'min:2',
                'max:50',
                'unique:vehicles,name',
            ],
            'body_type' => ['required', 'string', 'in:berlina,due volumi,suv,monovolume,coupe,cabriolet,furgone,autobus,camion'],
            'seats' => ['required', 'string', 'in:2,4,5,6,7,8,9'],
            'base_img' => ['nullable', 'image', 'max:2048'],
            'base_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999'],
            'brand_id' => ['required', 'uuid', 'exists:brands,id']
        ];
    }
}
