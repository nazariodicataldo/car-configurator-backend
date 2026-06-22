<?php

namespace App\Services;

use App\Http\Resources\ConfigurationResource;
use App\Http\Resources\VehicleResource;
use App\Models\Configuration;
use App\Traits\ApiResponse;

class DashboardService
{
    use ApiResponse;
    public function __construct() {}

    public function index()
    {
        $count_records = Configuration::count();

        $max_prev = Configuration::orderBy('total_price', 'desc')
            ->with('user')
            ->first();

        $latests_three = Configuration::orderBy('created_at', 'desc')
            ->with('user')
            ->take(3)
            ->get();

        $top_5_vehicles = Configuration::selectRaw('vehicle_id, count(*) ')
            ->groupBy('vehicle_id')
            ->orderByRaw('count(*) DESC')
            ->with('vehicle')
            ->take(5)
            ->get();

        $top_5_vehicles = collect($top_5_vehicles)->map(function ($elem) {
            return [
                'count' => $elem->count,
                'vehicle' => new VehicleResource($elem->vehicle),
            ];
        });

        return $this->apiResponse(
            true,
            [
                'count' => $count_records,
                'max' => new ConfigurationResource($max_prev),
                'latests' => ConfigurationResource::collection($latests_three),
                'topVehicles' => $top_5_vehicles,
            ],
            200,
            'Dashboard caricata con successo',
        );
    }
}
