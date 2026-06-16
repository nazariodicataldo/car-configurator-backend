<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreColorVehicleRequest extends FormRequest
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
        $vehicle = $this->route('vehicle');

        return [
            'color_id' => [
                'required',
                'uuid',
                'exists:colors,id',
                function (string $attribute, mixed $value, \Closure $fail) use (
                    $vehicle,
                ) {
                    // Verifico se esiste nella pivot un record con lo stesso color_id e vehicle_id
                    $existing_color_pivot = $vehicle
                        ->colors()
                        ->wherePivot('color_id', $value)
                        ->first();

                    if ($existing_color_pivot !== null) {
                        $fail('Questo colore esiste già nella tabella pivot');
                    }
                },
            ],
            'price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:9999999',
            ],
            'front_image' => ['nullable', 'file', 'image', 'max:5120'],
            'side_image' => ['nullable', 'file', 'image', 'max:5120'],
            'back_image' => ['nullable', 'file', 'image', 'max:5120'],
            'is_default' => [
                'nullable',
                'boolean',
                function ($attribute, $value, $fail) use ($vehicle) {
                    // Verifico se vehicle ha già un colore default
                    /* if ($value === 'true' && $vehicle->default_color_id) {
                        $fail('Il veicolo ha già un colore predefinito');
                    } */
                },
            ],
        ];
    }
}
