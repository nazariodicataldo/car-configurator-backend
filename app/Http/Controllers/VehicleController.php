<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(private VehicleService $vehicle_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->vehicle_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request)
    {
        return $this->vehicle_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Vehicle $vehicle)
    {
        return $this->vehicle_service->getSingle($request, $vehicle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        return $this->vehicle_service->update($request, $vehicle);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        return $this->vehicle_service->delete($vehicle);
    }
}
