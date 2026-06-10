<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOptionalSetupRequest;
use App\Http\Requests\UpdateOptionalSetupRequest;
use App\Models\Optional;
use App\Models\OptionalSetup;
use App\Models\Setup;
use App\Models\Vehicle;
use App\Services\OptionalSetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OptionalSetupController extends Controller
{
    public function __construct(
        private OptionalSetupService $optional_setup_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Vehicle $vehicle, Setup $setup)
    {
        return $this->optional_setup_service->getAll(
            $request,
            $vehicle,
            $setup,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreOptionalSetupRequest $request,
        Vehicle $vehicle,
        Setup $setup,
    ) {
        Gate::authorize('create', OptionalSetup::class);
        return $this->optional_setup_service->create(
            $request,
            $vehicle,
            $setup,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Request $request,
        Setup $setup,
        Vehicle $vehicle,
        Optional $optional,
    ) {
        return $this->optional_setup_service->getSingle(
            $request,
            $vehicle,
            $setup,
            $optional,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateOptionalSetupRequest $request,
        Vehicle $vehicle,
        Setup $setup,
        Optional $optional,
    ) {
        $optional = $setup
            ->optionals()
            ->where('optional_id', $optional->id)
            ->firstOrFail();

        Gate::authorize('update', $optional->pivot);

        return $this->optional_setup_service->update(
            $request,
            $vehicle,
            $setup,
            $optional,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle, Setup $setup, Optional $optional)
    {
        $optional = $setup
            ->optionals()
            ->where('optional_id', $optional->id)
            ->firstOrFail();

        Gate::authorize('delete', $optional->pivot);
        return $this->optional_setup_service->delete(
            $vehicle,
            $setup,
            $optional,
        );
    }
}
