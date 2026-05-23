<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationOptionalRequest extends FormRequest
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
        $configuration = $this->route('configuration');

        return [
            'optional_id' => [
                'required',
                'uuid',
                'exists:optionals,id',
                function (string $attribute, mixed $value, \Closure $fail) use (
                    $configuration,
                ) {
                    // Verifico se esiste nella pivot un record con lo stesso optional_id e configuration_id
                    $existing_optional_pivot = $configuration
                        ->optionals()
                        ->wherePivot('optional_id', $value)
                        ->first();

                    if ($existing_optional_pivot !== null) {
                        $fail(
                            'This optional already exists in the pivot table',
                        );
                    }
                },
            ],
        ];
    }
}
