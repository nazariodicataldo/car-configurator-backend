<?php

namespace App\Policies;

use App\Models\EngineVehicle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EngineVehiclePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach engine to a vehicle',
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EngineVehicle $engineVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to update engine for a vehicle',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EngineVehicle $engineVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach engine from a vehicle',
            );
    }
}
