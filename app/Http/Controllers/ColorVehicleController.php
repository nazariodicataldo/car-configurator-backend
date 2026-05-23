<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColorVehicleRequest;
use App\Http\Requests\UpdateColorVehicleRequest;
use App\Models\Color;
use App\Models\Vehicle;
use App\Services\ColorVehicleService;
use Illuminate\Http\Request;

class ColorVehicleController extends Controller
{
    public function __construct(
        private ColorVehicleService $color_vehicle_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Vehicle $vehicle)
    {
        return $this->color_vehicle_service->getAll($request, $vehicle);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreColorVehicleRequest $request, Vehicle $vehicle)
    {
        return $this->color_vehicle_service->create($request, $vehicle);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle, Color $color)
    {
        return $this->color_vehicle_service->getSingle($vehicle, $color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateColorVehicleRequest $request,
        Vehicle $vehicle,
        Color $color,
    ) {
        return $this->color_vehicle_service->update($request, $vehicle, $color);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle, Color $color)
    {
        return $this->color_vehicle_service->delete($vehicle, $color);
    }
}
