<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Http\Response;

class DashboardPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): Response
    {
        return $user->role === 'admin'
            ? Response::allow()
            : Response::deny("Non puoi visualizzare la dashboard amministrativa");
    }
}
