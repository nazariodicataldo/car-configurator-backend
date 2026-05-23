<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[
    Fillable([
        'id',
        'optional_id',
        'optional_price',
        'is_included',
        'configuration_id',
    ]),
]
class ConfigurationOptional extends Pivot
{
    use HasUuids;

    protected $table = 'configuration_optionals';
}
