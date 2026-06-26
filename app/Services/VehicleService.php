<?php

namespace App\Services;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleService
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
        $allowedColumns = ['created_at', 'id', 'seats', 'base_price', 'name'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'id';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = Vehicle::query()
            // Aggiungo il marchio
            ->when($request->boolean('withBrand'), function ($query, $value) {
                return $query->with('brand');
            })
            // Aggiungo l'allestimento
            ->when($request->boolean('withSetups'), function ($query, $value) {
                return $query->with('setups');
            })
            // Aggiungo il motore
            ->when($request->boolean('withEngines'), function ($query, $value) {
                return $query->with('engines');
            })
            /* Filtro per brand id */
            ->when($request->query('brandId'), function ($query, $brand_id) {
                return $query->where('brand_id', $brand_id);
            })
            /* Filtro per nome */
            ->when($request->query('name'), function ($query, $name) {
                return $query->where('name', 'ILIKE', '%' . $name . '%');
            })
            /* Filtro per prezzo minimo */
            ->when($request->query('minPrice'), function ($query, $value) {
                return $query->where('base_price', '>=', $value);
            })
            /* Filtro per prezzo massimo */
            ->when($request->query('maxPrice'), function ($query, $value) {
                return $query->where('base_price', '<=', $value);
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
        $vehicles = $this->filter($request);

        if ($vehicles instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return $this->apiResponse(
                true,
                $vehicles->setCollection(
                    VehicleResource::collection($vehicles->items())->collection,
                ),
                200,
                'Veicoli recuperati con successo',
            );
        }

        return $this->apiResponse(
            true,
            VehicleResource::collection($vehicles),
            200,
            'Veicoli recuperati con successo',
        );
    }

    public function getSingle(Request $request, Vehicle $vehicle)
    {
        if ($request->boolean('withBrand')) {
            $vehicle->load('brand');
        }

        if ($request->boolean('withEngines')) {
            $vehicle->load('engines');
        }

        if ($request->boolean('withSetups')) {
            $vehicle->load('setups');
        }

        if ($request->boolean('withColors')) {
            $vehicle->load('colors');
        }

        if ($request->boolean('withConfigurations')) {
            $vehicle->load('configurations');
        }

        return $this->apiResponse(
            true,
            new VehicleResource($vehicle),
            200,
            'Veicolo recuperato con successo',
        );
    }

    public function create(StoreVehicleRequest $request)
    {
        $data = $request->validated();

        $vehicle = DB::transaction(function () use ($data) {
            $vehicle = Vehicle::create($data);

            if (!empty($data['engines'])) {
                $syncData = [];
                foreach ($data['engines'] as $engine) {
                    $syncData[$engine['engine_id']] = [
                        'id' => (string) Str::uuid(),
                        'price' => $engine['price'],
                    ];
                }
                $vehicle->engines()->sync($syncData);
            }

            return $vehicle;
        });

        return $this->apiResponse(
            true,
            new VehicleResource($vehicle),
            201,
            'Veicolo creato con successo',
        );
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $vehicle) {
            $vehicle->update($data);

            if (isset($data['engines'])) {
                $syncData = [];
                foreach ($data['engines'] as $engine) {
                    $syncData[$engine['engine_id']] = [
                        'id' => (string) Str::uuid(),
                        'price' => $engine['price'],
                    ];
                }
                $vehicle->engines()->sync($syncData);
            }
        });

        $vehicle->refresh();

        return $this->apiResponse(
            true,
            new VehicleResource($vehicle),
            201,
            'Veicolo aggiornato con successo',
        );
    }

    public function delete(Vehicle $vehicle)
    {
        $vehicle->delete();

        return $this->apiResponse(
            true,
            null,
            200,
            'Veicolo eliminato con successo',
        );
    }
}
