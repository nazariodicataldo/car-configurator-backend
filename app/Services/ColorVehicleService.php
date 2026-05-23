<?php

namespace App\Services;

use App\Http\Requests\StoreColorVehicleRequest;
use App\Http\Requests\UpdateColorVehicleRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ColorVehicleService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    private function filter(Request $request, Vehicle $vehicle)
    {
        // Mi prendo i valori di perPage e page
        $perPage = $request->query('perPage');
        $page = $request->query('page');

        // Colonne ammesse
        $allowedColumns = ['created_at', 'id', 'price'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'price';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = $vehicle
            ->colors()
            /* Ordina */
            ->when($column, function ($query) use ($column, $order) {
                return $query->orderBy($column, $order);
            });

        /* Return condizionale */
        return $perPage || $page
            ? // Se l'utente passa perPage vuol dire che è interessato alla paginazione
            $query
                ->paginate($perPage ?? 12, ['*'], 'page', $page ?? 1)
                ->withQueryString()
            : $query->get();
    }

    public function getAll(Request $request, Vehicle $vehicle)
    {
        $data = $this->filter($request, $vehicle);
        return $this->apiResponse(
            true,
            ColorResource::collection($data),
            200,
            'Colors successfully fetched',
        );
    }

    public function getSingle(Vehicle $vehicle, Color $color)
    {
        return $this->apiResponse(
            true,
            new ColorResource($color),
            200,
            'Color successfully fetched',
        );
    }

    public function create(StoreColorVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();

        $vehicle
            ->colors()
            ->attach($data['color_id'], ['price' => $data['price']]);

        // Mi ritorno il record appena collegato
        $data = $vehicle
            ->colors()
            ->wherePivot('color_id', $data['color_id'])
            ->first();

        return $this->apiResponse(
            true,
            new ColorResource($data),
            201,
            'Color successfully created',
        );
    }

    public function update(
        UpdateColorVehicleRequest $request,
        Vehicle $vehicle,
        Color $color,
    ) {
        $data = $request->validated();

        $vehicle->colors()->updateExistingPivot($color->id, $data);

        return $this->apiResponse(
            true,
            new ColorResource($color),
            201,
            'Color successfully updated',
        );
    }

    public function delete(Vehicle $vehicle, Color $color)
    {
        $vehicle->colors()->detach($color->id);

        return $this->apiResponse(
            true,
            null,
            204,
            'Color successfully deleted',
        );
    }
}
