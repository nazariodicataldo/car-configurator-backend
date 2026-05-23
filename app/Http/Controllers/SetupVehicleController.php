<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetupVehicleRequest;
use App\Http\Requests\UpdateSetupVehicleRequest;
use App\Models\Setup;
use App\Models\Vehicle;
use App\Services\SetupVehicleService;
use Illuminate\Http\Request;

class SetupVehicleController extends Controller
{
    public function __construct(
        private SetupVehicleService $setup_vehicle_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Vehicle $vehicle)
    {
        return $this->setup_vehicle_service->getAll($request, $vehicle);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSetupVehicleRequest $request, Vehicle $vehicle)
    {
        return $this->setup_vehicle_service->create($request, $vehicle);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Vehicle $vehicle, Setup $setup)
    {
        return $this->setup_vehicle_service->getSingle(
            $request,
            $vehicle,
            $setup,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateSetupVehicleRequest $request,
        Vehicle $vehicle,
        Setup $setup,
    ) {
        return $this->setup_vehicle_service->update(
            $request,
            $vehicle,
            $setup,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle, Setup $setup)
    {
        return $this->setup_vehicle_service->delete($vehicle, $setup);
    }
}
