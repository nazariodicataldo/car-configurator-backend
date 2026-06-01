<?php

namespace App\Policies;

use App\Models\SetupVehicle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SetupVehiclePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach setup to a vehicle',
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SetupVehicle $setupVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to update setup for a vehicle',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SetupVehicle $setupVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach setup from a vehicle',
            );
    }
}
