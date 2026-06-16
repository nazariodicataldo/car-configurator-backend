<?php

namespace App\Services;

use App\Http\Requests\StoreOptionalRequest;
use App\Http\Requests\UpdateOptionalRequest;
use App\Http\Resources\OptionalResource;
use App\Models\Optional;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OptionalService
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

        $query = Optional::query()
            // Carico gli allestimenti
            ->when($request->boolean('withSetups'), function ($query) {
                return $query->with('setups');
            })
            // Carico gli optionals
            ->when($request->boolean('withCompatibilityRules'), function (
                $query,
            ) {
                return $query->allCompatibilityRules();
            })
            /* Filtro per nome */
            ->when($request->query('name'), function ($query, $name) {
                return $query->where('name', 'ILIKE', '%' . $name . '%');
            })
            /* Filtra per categoria */
            ->when($request->query('category'), function ($query, $category) {
                return $query->where('category', $category);
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
        $optional = $this->filter($request);
        return $this->apiResponse(
            true,
            OptionalResource::collection($optional),
            200,
            'Optionals caricati con successo',
        );
    }

    public function getSingle(Request $request, Optional $setup)
    {
        if ($request->boolean('withSetups')) {
            $setup->load('setups');
        }

        if ($request->boolean('withCompatibilityRules')) {
            $setup->allCompatibilityRules();
        }

        return $this->apiResponse(
            true,
            new OptionalResource($setup),
            200,
            'Optional caricato con successo',
        );
    }

    public function create(StoreOptionalRequest $request)
    {
        $data = $request->validated();

        $setup = Optional::create($data);

        return $this->apiResponse(
            true,
            new OptionalResource($setup),
            201,
            'Optional creato con successo',
        );
    }

    public function update(UpdateOptionalRequest $request, Optional $setup)
    {
        $data = $request->validated();

        $setup->update($data);

        $setup->refresh();

        return $this->apiResponse(
            true,
            new OptionalResource($setup),
            201,
            'Optional modificato con successo',
        );
    }

    public function delete(Optional $setup)
    {
        $setup->delete();

        return $this->apiResponse(
            true,
            null,
            200,
            'Optional eliminato con successo',
        );
    }
}
