<?php

namespace App\Policies;

use App\Models\Engine;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnginePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to create engines');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Engine $engine): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to edit engines');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Engine $engine): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to delete engines');
    }
}
