<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateColorVehicleRequest extends FormRequest
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
        $color = $this->route('color');

        return [
            'price' => [
                'sometimes',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:99999999',
            ],
            'front_image' => ['nullable', 'file', 'image', 'max:5120'],
            'side_image' => ['nullable', 'file', 'image', 'max:5120'],
            'back_image' => ['nullable', 'file', 'image', 'max:5120'],
            'is_default' => [
                'nullable',
                'boolean',
                /* function ($attribute, $value, $fail) use ($vehicle, $color) {
                    // Verifico se vehicle ha già un colore default o se questo è diverso dal colore in questione
                    if (
                        $value === 'true' &&
                        $vehicle->default_color_id &&
                        $vehicle->default_color_id !== $color->id
                    ) {
                        $fail('Il veicolo ha già un colore predefinito');
                    }
                }, */
            ],
        ];
    }
}
