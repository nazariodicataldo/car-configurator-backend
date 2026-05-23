<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSetupVehicleRequest extends FormRequest
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
            'setup_id' => [
                'required',
                'uuid',
                'exists:setups,id',
                function (string $attribute, mixed $value, \Closure $fail) use (
                    $vehicle,
                ) {
                    // Verifico se esiste nella pivot un record con lo stesso setup_id e vehicle_id
                    $existing_setup_pivot = $vehicle
                        ->setups()
                        ->wherePivot('setup_id', $value)
                        ->first();

                    if ($existing_setup_pivot !== null) {
                        $fail('This setup already exists in the pivot table');
                    }
                },
            ],
            'price' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
        ];
    }
}
