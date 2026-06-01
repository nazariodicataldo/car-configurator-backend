<?php

namespace App\Policies;

use App\Models\ConfigurationOptional;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConfigurationOptionalPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach optional to a configuration',
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        ConfigurationOptional $configurationOptional,
    ): Response {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to update optional for a configuration',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        ConfigurationOptional $configurationOptional,
    ): Response {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach optional from a configuration',
            );
    }
}
