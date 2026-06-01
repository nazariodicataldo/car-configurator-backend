<?php

namespace App\Policies;

use App\Models\Optional;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OptionalPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to create optionals');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Optional $optional): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to edit optionals');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Optional $optional): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to delete optionals');
    }
}
