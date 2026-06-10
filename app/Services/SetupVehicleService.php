<?php

namespace App\Services;

use App\Http\Requests\StoreSetupVehicleRequest;
use App\Http\Requests\UpdateSetupVehicleRequest;
use App\Http\Resources\SetupResource;
use App\Models\Setup;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SetupVehicleService
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
            ->setups()
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
            SetupResource::collection($data),
            200,
            'Allestimenti recuperati con successo',
        );
    }

    public function getSingle(Request $request, Vehicle $vehicle, Setup $setup)
    {
        return $this->apiResponse(
            true,
            new SetupResource($setup),
            200,
            'Allestimento recuperato con successo',
        );
    }

    public function create(StoreSetupVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();

        $vehicle
            ->setups()
            ->attach($data['setup_id'], ['price' => $data['price']]);

        // Mi ritorno il record appena collegato
        $data = $vehicle
            ->setups()
            ->wherePivot('setup_id', $data['setup_id'])
            ->first();

        return $this->apiResponse(
            true,
            new SetupResource($data),
            201,
            'Allestimento creato con successo',
        );
    }

    public function update(
        UpdateSetupVehicleRequest $request,
        Vehicle $vehicle,
        Setup $setup,
    ) {
        $data = $request->validated();

        $vehicle->setups()->updateExistingPivot($setup->id, $data);

        $setup->refresh();

        return $this->apiResponse(
            true,
            new SetupResource($setup),
            201,
            'Allestimento aggiornato con successo',
        );
    }

    public function delete(Vehicle $vehicle, Setup $setup)
    {
        $vehicle->setups()->detach($setup->id);

        return $this->apiResponse(
            true,
            null,
            200,
            'Allestimento eliminato con successo',
        );
    }
}
