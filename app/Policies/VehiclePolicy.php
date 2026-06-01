<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\Response;

class VehiclePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to create vehicles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vehicle $vehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to edit vehicles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vehicle $vehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to delete vehicles');
    }
}
