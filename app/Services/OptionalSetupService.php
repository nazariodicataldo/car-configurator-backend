<?php

namespace App\Services;

use App\Http\Requests\StoreOptionalSetupRequest;
use App\Http\Requests\UpdateOptionalSetupRequest;
use App\Http\Resources\CompatibilityRuleResource;
use App\Http\Resources\OptionalResource;
use App\Models\CompatibilityRule;
use App\Models\Optional;
use App\Models\Setup;
use App\Models\SetupVehicle;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OptionalSetupService
{
    use ApiResponse;

    public function __construct() {}

    private function getSetupVehicle(Vehicle $vehicle, Setup $setup): SetupVehicle
    {
        return SetupVehicle::where('setup_id', $setup->id)
            ->where('vehicle_id', $vehicle->id)
            ->firstOrFail();
    }

    private function filter(Request $request, Vehicle $vehicle, Setup $setup)
    {
        $setupVehicle = $this->getSetupVehicle($vehicle, $setup);

        $perPage = $request->query('perPage');
        $page = $request->query('page');

        $allowedColumns = ['created_at', 'id', 'price'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'price';

        $allowedOrders = ['asc', 'desc'];
        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = $setupVehicle
            ->optionals()
            ->withPivot(['price', 'is_included'])
            ->when($column, fn($q) => $q->orderBy($column, $order));

        return $perPage || $page
            ? $query->paginate($perPage ?? 12, ['*'], 'page', $page ?? 1)->withQueryString()
            : $query->get();
    }

    public function getAll(Request $request, Vehicle $vehicle, Setup $setup)
    {
        $optionals = $this->filter($request, $vehicle, $setup);

        $rules = CompatibilityRule::whereIn('optional_a_id', $optionals->pluck('id'))
            ->orWhereIn('optional_b_id', $optionals->pluck('id'))
            ->with(['optionalA', 'optionalB'])
            ->get();

        return $this->apiResponse(
            true,
            [
                'items' => OptionalResource::collection($optionals),
                'rules' => CompatibilityRuleResource::collection($rules),
            ],
            200,
            'Accessori recuperati con successo',
        );
    }

    public function getSingle(
        Vehicle $vehicle,
        Setup $setup,
        Optional $optional,
    ) {
        $setupVehicle = $this->getSetupVehicle($vehicle, $setup);

        $optional = $setupVehicle
            ->optionals()
            ->withPivot(['price', 'is_included'])
            ->where('optionals.id', $optional->id)
            ->firstOrFail();

        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            200,
            'Accessorio recuperato con successo',
        );
    }

    public function create(
        StoreOptionalSetupRequest $request,
        Vehicle $vehicle,
        Setup $setup,
    ) {
        $validated = $request->validated();

        $setupVehicle = $this->getSetupVehicle($vehicle, $setup);

        $setupVehicle->optionals()->attach($validated['optional_id'], [
            'price' => $validated['price'],
            'is_included' => $validated['is_included'] ?? false,
        ]);

        $optional = $setupVehicle
            ->optionals()
            ->withPivot(['price', 'is_included'])
            ->where('optionals.id', $validated['optional_id'])
            ->firstOrFail();

        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            201,
            'Accessorio creato con successo',
        );
    }

    public function update(
        UpdateOptionalSetupRequest $request,
        Vehicle $vehicle,
        Setup $setup,
        Optional $optional,
    ) {
        $validated = $request->validated();

        $setupVehicle = $this->getSetupVehicle($vehicle, $setup);

        $setupVehicle->optionals()->updateExistingPivot($optional->id, $validated);

        $updated = $setupVehicle
            ->optionals()
            ->withPivot(['price', 'is_included'])
            ->where('optionals.id', $optional->id)
            ->firstOrFail();

        return $this->apiResponse(
            true,
            new OptionalResource($updated),
            200,
            'Accessorio aggiornato con successo',
        );
    }

    public function delete(Vehicle $vehicle, Setup $setup, Optional $optional)
    {
        $setupVehicle = $this->getSetupVehicle($vehicle, $setup);

        $setupVehicle->optionals()->detach($optional->id);

        return $this->apiResponse(true, null, 200, 'Accessorio eliminato con successo');
    }
}