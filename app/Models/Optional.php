<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'category', 'description'])]
class Optional extends Model
{
    use HasUuids;

    public function setupVehicles(): BelongsToMany
    {
        return $this->belongsToMany(
            SetupVehicle::class,
            'optional_setups',
            'optional_id',
            'setup_vehicle_id',
        )
            ->using(OptionalSetup::class)
            ->withPivot(['price', 'is_included']);
    }

    public function configurations(): BelongsToMany
    {
        return $this->belongsToMany(
            Configuration::class,
            'configuration_optionals',
        );
    }

    public function compatibilyRules(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'compatibility_rules',
            'optional_a_id',
            'optional_b_id',
        );
    }

    // Relazione inversa speculare
    public function compatibleWithMe(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'compatibility_rules',
            'optional_b_id',
            'optional_a_id',
        );
    }

    // Union delle regole di validazione
    public function allCompatibilityRules()
    {
        return $this->compatibilyRules->merge($this->compatibleWithMe);
    }
}
