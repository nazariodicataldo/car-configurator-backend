<?php

namespace App\Services;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ColorService
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

        $query = Color::query()
            // Carico il veicolo
            ->when($request->boolean('withVehicles'), function ($query) {
                return $query->with('vehicles');
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
        $colors = $this->filter($request);
        return $this->apiResponse(
            true,
            ColorResource::collection($colors),
            200,
            'Colors successfully fetched',
        );
    }

    public function getSingle(Request $request, Color $color)
    {
        if ($request->boolean('withVehicles')) {
            $color->load('vehicles');
        }

        return $this->apiResponse(
            true,
            new ColorResource($color),
            200,
            'Color successfully fetched',
        );
    }

    public function create(StoreColorRequest $request)
    {
        $data = $request->validated();

        $color = Color::create($data);

        return $this->apiResponse(
            true,
            new ColorResource($color),
            201,
            'Color successfully created',
        );
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        $data = $request->validated();

        $color = $color->update($data);

        return $this->apiResponse(
            true,
            new ColorResource($color),
            201,
            'Color successfully updated',
        );
    }

    public function delete(Color $color)
    {
        $color->delete();

        return $this->apiResponse(
            true,
            null,
            204,
            'Color successfully deleted',
        );
    }
}
