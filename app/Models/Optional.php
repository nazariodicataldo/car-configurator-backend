<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'category', 'description'])]
class Optional extends Model
{
    public function setups(): BelongsToMany
    {
        return $this->belongsToMany(Setup::class)->using(OptionalSetup::class)->withPivot([
            'price',
            'is_included',
        ]);
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
}
