<?php

namespace App\Services;

use App\Http\Requests\StoreSetupRequest;
use App\Http\Requests\UpdateSetupRequest;
use App\Http\Resources\SetupResource;
use App\Models\Setup;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SetupService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    private function filter(Request $request)
    {
        // Mi prendo i valori di perPage e page
        $perPage = $request->query('perPage');
        $page = $request->query('page');

        // Colonne ammesse
        $allowedColumns = ['created_at', 'id', 'name'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'id';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = Setup::query()
            // Carico i veicoli
            ->when($request->boolean('withVehicles'), function ($query) {
                return $query->with('vehicles');
            })
            // Carico gli optionals
            ->when($request->boolean('withOptionals'), function ($query) {
                return $query->with('optionals');
            })
            /* Filtro per nome */
            ->when($request->query('name'), function ($query, $name) {
                return $query->where('name', 'ILIKE', '%' . $name . '%');
            })
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

    public function getAll(Request $request)
    {
        $setups = $this->filter($request);
        return $this->apiResponse(
            true,
            SetupResource::collection($setups),
            200,
            'Setups successfully fetched',
        );
    }

    public function getSingle(Request $request, Setup $setup)
    {
        if ($request->boolean('withVehicles')) {
            $setup->load('vehicles');
        }

        if ($request->boolean('withOptionals')) {
            $setup->load('optionals');
        }

        return $this->apiResponse(
            true,
            new SetupResource($setup),
            200,
            'Setup successfully fetched',
        );
    }

    public function create(StoreSetupRequest $request)
    {
        $data = $request->validated();

        $setup = Setup::create($data);

        return $this->apiResponse(
            true,
            new SetupResource($setup),
            201,
            'Setup successfully created',
        );
    }

    public function update(UpdateSetupRequest $request, Setup $setup)
    {
        $data = $request->validated();

        $setup = $setup->update($data);

        return $this->apiResponse(
            true,
            new SetupResource($setup),
            201,
            'Setup successfully updated',
        );
    }

    public function delete(Setup $setup)
    {
        $setup->delete();

        return $this->apiResponse(
            true,
            null,
            204,
            'Setup successfully deleted',
        );
    }
}
