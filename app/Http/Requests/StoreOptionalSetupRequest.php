<?php

namespace App\Http\Requests;

use App\Models\SetupVehicle;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOptionalSetupRequest extends FormRequest
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
        $setup = $this->route('setup');
        $vehicle = $this->route('vehicle');

        return [
            'optional_id' => [
                'required',
                'uuid',
                'exists:optionals,id',
                function (string $attribute, mixed $value, \Closure $fail) use (
                    $setup,
                    $vehicle,
                ) {
                    // Verifico se esiste nella pivot un record con lo stesso optional_id e setup_id
                    $existing_optional_pivot = SetupVehicle::where(
                        'setup_id',
                        $setup->id,
                    )
                        ->where('vehicle_id', $vehicle->id)
                        ->whereHas('optionals', function ($query) use ($value) {
                            $query->where('optional_id', $value);
                        })
                        ->exists();

                    if ($existing_optional_pivot) {
                        $fail(
                            'This optional already exists in the pivot table',
                        );
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
            'is_included' => ['nullable', 'boolean'],
        ];
    }
}
