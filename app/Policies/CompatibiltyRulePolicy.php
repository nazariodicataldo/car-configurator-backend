<?php

namespace App\Policies;

use App\Models\CompatibilityRule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompatibiltyRulePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach compatibility rule between optionals',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        CompatibilityRule $compatibilityRule,
    ): Response {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach compatibility rule between optionals',
            );
    }
}
