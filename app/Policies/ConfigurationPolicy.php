<?php

namespace App\Policies;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConfigurationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny("You cannot view other users' configurations");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Configuration $configuration): Response
    {
        return $user->role === 'admin' ||
            ($user->id === $configuration->user_id && $user->role === 'user')
            ? Response::allow()
            : Response::deny("You cannot view other users' configurations");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Configuration $configuration): Response
    {
        return $user->role === 'admin' ||
            ($user->id === $configuration->user_id && $user->role === 'user')
            ? Response::allow()
            : Response::deny("You cannot edit other users' configurations");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Configuration $configuration): Response
    {
        return $user->role === 'admin' ||
            ($user->id === $configuration->user_id && $user->role === 'user')
            ? Response::allow()
            : Response::deny("You cannot edit other users' configurations");
    }
}
