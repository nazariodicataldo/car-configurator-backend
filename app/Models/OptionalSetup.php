<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['price', 'is_included', 'optional_id', 'setup_id'])]
class OptionalSetup extends Model
{
    use HasUuids;
    protected $table = 'optional_setups';

    public function optional(): BelongsTo
    {
        return $this->belongsTo(Optional::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }

    public function configurations(): BelongsToMany
    {
        return $this->belongsToMany(
            Configuration::class,
            'configuration_optionals',
        );
    }
}
