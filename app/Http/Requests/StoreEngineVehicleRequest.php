<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEngineVehicleRequest extends FormRequest
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
            'engine_id' => [
                'required',
                'uuid',
                'exists:engines,id',
                function (string $attribute, mixed $value, \Closure $fail) use (
                    $vehicle,
                ) {
                    // Verifico se esiste nella pivot un record con lo stesso engine_id e vehicle_id
                    $existing_engine_pivot = $vehicle
                        ->engines()
                        ->wherePivot('engine_id', $value)
                        ->first();

                    if ($existing_engine_pivot !== null) {
                        $fail('This engine already exists in the pivot table');
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
        ];
    }
}
