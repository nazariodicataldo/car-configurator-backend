<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(private UserService $user_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);
        return $this->user_service->getAll($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        Gate::authorize('view', $user);
        return $this->user_service->getSingle($request, $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        return $this->user_service->update($request, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        return $this->user_service->delete($user);
    }
}
