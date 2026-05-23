<?php

namespace App\Services;

use App\Http\Requests\StoreEngineRequest;
use App\Http\Requests\UpdateEngineRequest;
use App\Http\Resources\EngineResource;
use App\Models\Engine;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EngineService
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
        $allowedColumns = ['created_at', 'id', 'power', 'name'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'id';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = Engine::query()
            // Aggiungo il fornitore
            ->when($request->boolean('withSupplier'), function (
                $query,
                $value,
            ) {
                if ($value) {
                    return $query->with('supplier');
                }
            })
            // Carico i veicoli compatibili con il motore
            ->when($request->boolean('withEngines'), function ($query, $value) {
                if ($value) {
                    return $query->with('engines');
                }
            })
            /* Filtro per carburante */
            ->when($request->query('fuel'), function ($query, $fuel) {
                return $query->where('fuel', $fuel);
            })
            /* Filtro per cambio */
            ->when($request->query('transmission'), function (
                $query,
                $transmission,
            ) {
                return $query->where('transmission', $transmission);
            })
            /* Filtro per nome */
            ->when($request->query('name'), function ($query, $name) {
                return $query->where('name', 'ILIKE', '%' . $name . '%');
            })
            /* Filtro per potenza minima */
            ->when($request->query('minPower'), function ($query, $value) {
                return $query->where('power', '>=', $value);
            })
            /* Filtro per prezzo massimo */
            ->when($request->query('maxPower'), function ($query, $value) {
                return $query->where('power', '<=', $value);
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
        $engines = $this->filter($request);
        return $this->apiResponse(
            true,
            EngineResource::collection($engines),
            200,
            'Engines successfully fetched',
        );
    }

    public function getSingle(Request $request, Engine $engine)
    {
        if ($request->boolean('withVehicles')) {
            $engine->load('vehicles');
        }

        return $this->apiResponse(
            true,
            new EngineResource($engine),
            200,
            'Engine successfully fetched',
        );
    }

    public function create(StoreEngineRequest $request)
    {
        $data = $request->validated();

        $engine = Engine::create($data);

        return $this->apiResponse(
            true,
            new EngineResource($engine),
            201,
            'Engine successfully created',
        );
    }

    public function update(UpdateEngineRequest $request, Engine $engine)
    {
        $data = $request->validated();

        $engine = $engine->update($data);

        return $this->apiResponse(
            true,
            new EngineResource($engine),
            201,
            'Engine successfully updated',
        );
    }

    public function delete(Engine $engine)
    {
        $engine->delete();

        return $this->apiResponse(
            true,
            null,
            204,
            'Engine successfully deleted',
        );
    }
}
