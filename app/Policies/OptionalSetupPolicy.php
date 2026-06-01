<?php

namespace App\Policies;

use App\Models\OptionalSetup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OptionalSetupPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach optional to a setup',
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OptionalSetup $optionalSetup): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to update optional for a setup',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OptionalSetup $optionalSetup): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach optional from a setup',
            );
    }
}
