<?php

namespace App\Services;

use App\Http\Requests\StoreOptionalSetupRequest;
use App\Http\Requests\UpdateOptionalSetupRequest;
use App\Http\Resources\CompatibilityRuleResource;
use App\Http\Resources\OptionalResource;
use App\Models\CompatibilityRule;
use App\Models\Optional;
use App\Models\Setup;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OptionalSetupService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    private function filter(Request $request, Setup $setup)
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

        $query = $setup
            ->optionals()
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

    public function getAll(Request $request, Setup $setup)
    {
        $optionals = $this->filter($request, $setup);

        // Ritorno le regole di compatibilità
        $rules = CompatibilityRule::whereIn(
            'optional_a_id',
            $optionals->pluck('id'),
        )
            ->orWhereIn('optional_b_id', $optionals->pluck('id'))
            ->with(['optionalA', 'optionalB'])
            ->get();

        $data = [
            'items' => OptionalResource::collection($optionals),
            'rules' => CompatibilityRuleResource::collection($rules),
        ];

        return $this->apiResponse(
            true,
            $data,
            200,
            'Optionals successfully fetched',
        );
    }

    public function getSingle(
        Request $request,
        Setup $setup,
        Optional $optional,
    ) {
        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            200,
            'Optional successfully fetched',
        );
    }

    public function create(StoreOptionalSetupRequest $request, Setup $setup)
    {
        $data = $request->validated();

        $setup
            ->optionals()
            ->attach($data['optional_id'], ['price' => $data['price']]);

        // Mi ritorno il record appena collegato
        $data = $setup
            ->optionals()
            ->wherePivot('optional_id', $data['optional_id'])
            ->first();

        return $this->apiResponse(
            true,
            new OptionalResource($data),
            201,
            'Optional successfully created',
        );
    }

    public function update(
        UpdateOptionalSetupRequest $request,
        Setup $setup,
        Optional $optional,
    ) {
        $data = $request->validated();

        $setup->optionals()->updateExistingPivot($optional->id, $data);

        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            201,
            'Optional successfully updated',
        );
    }

    public function delete(Setup $setup, Optional $optional)
    {
        $setup->optionals()->detach($optional->id);

        return $this->apiResponse(
            true,
            null,
            204,
            'Optional successfully deleted',
        );
    }
}
