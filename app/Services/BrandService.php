<?php

namespace App\Services;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BrandService
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

        $query = Brand::query()
            // Aggiungo i veicoli
            ->when($request->boolean('withVehicles'), function (
                $query,
                $value,
            ) {
                if ($value) {
                    return $query->with('vehicles');
                }
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
        $brands = $this->filter($request);
        return $this->apiResponse(
            true,
            BrandResource::collection($brands),
            200,
            'Brands successfully fetched',
        );
    }

    public function getSingle(Request $request, Brand $brand)
    {
        if ($request->boolean('withVehicles')) {
            $brand->load('vehicles');
        }

        return $this->apiResponse(
            true,
            new BrandResource($brand),
            200,
            'Brand successfully fetched',
        );
    }

    public function create(StoreBrandRequest $request)
    {
        $data = $request->validated();

        $brand = Brand::create($data);

        return $this->apiResponse(
            true,
            new BrandResource($brand),
            201,
            'Brand successfully created',
        );
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $data = $request->validated();

        $brand = $brand->update($data);

        return $this->apiResponse(
            true,
            new BrandResource($brand),
            201,
            'Brand successfully updated',
        );
    }

    public function delete(Brand $brand)
    {
        $brand->delete();

        return $this->apiResponse(
            true,
            null,
            204,
            'Brand successfully deleted',
        );
    }
}
