<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BrandPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to create brands');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Brand $brand): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to edit brands');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Brand $brand): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny('You do not have permission to delete brands');
    }
}
