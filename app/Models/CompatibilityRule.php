<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['optional_a_id', 'optional_b_id'])]
class CompatibilityRule extends Model
{
    use HasUuids;

    public function optionalA(): BelongsTo
    {
        return $this->belongsTo(Optional::class);
    }

    public function optionalB(): BelongsTo
    {
        return $this->belongsTo(Optional::class);
    }
}
