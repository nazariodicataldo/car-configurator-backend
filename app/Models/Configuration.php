<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[
    Fillable([
        'name',
        'user_id',
        'vehicle_id',
        'vehicle_price',
        'engine_id',
        'engine_price',
        'setup_id',
        'setup_price',
        'color_id',
        'color_price',
        'total_price',
    ]),
]
class Configuration extends Model
{
    use HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function engine(): BelongsTo
    {
        return $this->belongsTo(Engine::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function optionals(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'configuration_optionals',
        )->withPivot(['optional_price', 'is_included']);
    }
}
