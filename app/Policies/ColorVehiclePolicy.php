<?php

namespace App\Policies;

use App\Models\ColorVehicle;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ColorVehiclePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to attach color to a vehicle',
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ColorVehicle $colorVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to edit color for a vehicle',
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ColorVehicle $colorVehicle): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny(
                'You do not have permission to detach color from a vehicle',
            );
    }
}
