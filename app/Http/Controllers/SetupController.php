<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetupRequest;
use App\Http\Requests\UpdateSetupRequest;
use App\Models\Setup;
use App\Services\SetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SetupController extends Controller
{
    public function __construct(private SetupService $setup_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->setup_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSetupRequest $request)
    {
        Gate::authorize('create', Setup::class);
        return $this->setup_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Setup $setup)
    {
        return $this->setup_service->getSingle($request, $setup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSetupRequest $request, Setup $setup)
    {
        Gate::authorize('update', $setup);
        return $this->setup_service->update($request, $setup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setup $setup)
    {
        Gate::authorize('delete', $setup);
        return $this->setup_service->delete($setup);
    }
}
