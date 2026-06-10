<?php

namespace App\Services;

use App\Http\Requests\StoreEngineVehicleRequest;
use App\Http\Requests\UpdateEngineVehicleRequest;
use App\Http\Resources\EngineResource;
use App\Models\Engine;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EngineVehicleService
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
            ->engines()
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
            EngineResource::collection($data),
            200,
            'Motori recuperati con successo',
        );
    }

    public function getSingle(Vehicle $vehicle, Engine $engine)
    {
        return $this->apiResponse(
            true,
            new EngineResource($engine),
            200,
            'Motore recuperato con successo',
        );
    }

    public function create(StoreEngineVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();

        $vehicle
            ->engines()
            ->attach($data['engine_id'], ['price' => $data['price']]);

        $engine = $vehicle
            ->engines()
            ->wherePivot('engine_id', $data['engine_id'])
            ->first();

        return $this->apiResponse(
            true,
            new EngineResource($engine),
            201,
            'Motore creato con successo',
        );
    }

    public function update(
        UpdateEngineVehicleRequest $request,
        Vehicle $vehicle,
        Engine $engine,
    ) {
        $data = $request->validated();

        $vehicle->engines()->updateExistingPivot($engine->id, $data);

        $engine->refresh();

        return $this->apiResponse(
            true,
            new EngineResource($engine),
            201,
            'Motore aggiornato con successo',
        );
    }

    public function delete(Vehicle $vehicle, Engine $engine)
    {
        $vehicle->engines()->detach($engine->id);

        return $this->apiResponse(
            true,
            null,
            200,
            'Motore eliminato con successo',
        );
    }
}
