<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEngineVehicleRequest;
use App\Http\Requests\UpdateEngineVehicleRequest;
use App\Models\Engine;
use App\Models\Vehicle;
use App\Services\EngineVehicleService;
use Illuminate\Http\Request;

class EngineVehicleController extends Controller
{
    public function __construct(
        private EngineVehicleService $engine_vehicle_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Vehicle $vehicle)
    {
        return $this->engine_vehicle_service->getAll($request, $vehicle);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEngineVehicleRequest $request, Vehicle $vehicle)
    {
        return $this->engine_vehicle_service->create($request, $vehicle);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle, Engine $engine)
    {
        return $this->engine_vehicle_service->getSingle($vehicle, $engine);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateEngineVehicleRequest $request,
        Vehicle $vehicle,
        Engine $engine,
    ) {
        return $this->engine_vehicle_service->update(
            $request,
            $vehicle,
            $engine,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle, Engine $engine)
    {
        return $this->engine_vehicle_service->delete($vehicle, $engine);
    }
}
