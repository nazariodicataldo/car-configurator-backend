<?php

namespace App\Policies;

use App\Models\Setup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SetupPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to create setups');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Setup $setup): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to edit setups');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Setup $setup): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to delete setups');
    }
}
