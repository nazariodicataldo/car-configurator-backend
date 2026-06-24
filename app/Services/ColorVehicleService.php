<?php

namespace App\Services;

use App\Http\Requests\StoreColorVehicleRequest;
use App\Http\Requests\UpdateColorVehicleRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Models\Vehicle;
use App\Traits\ApiResponse;
use App\Traits\HasFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColorVehicleService
{
    use ApiResponse, HasFileUpload;
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
            ->colors()
            ->withPivot([
                'price',
                'front_image_url',
                'side_image_url',
                'back_image_url',
            ])
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

        $data->transform(function ($color) use ($vehicle) {
            $color->is_default = $color->id === $vehicle->default_color_id;

            return $color;
        });

        return $this->apiResponse(
            true,
            ColorResource::collection($data),
            200,
            'Colors successfully fetched',
        );
    }

    public function getSingle(Vehicle $vehicle, Color $color)
    {
        $colorWithPivot = $vehicle->colorWithPivot($color->id);

        return $this->apiResponse(
            true,
            new ColorResource($colorWithPivot),
            200,
            'Color successfully fetched',
        );
    }

    public function create(StoreColorVehicleRequest $request, Vehicle $vehicle)
    {
        $validated = $request->validated();

        $pivotData = ['price' => $validated['price']];

        if ($request->hasFile('front_image')) {
            $pivotData['front_image_url'] = $this->uploadImage(
                $request,
                'colors',
                null,
                'front_image',
            );
        }
        if ($request->hasFile('side_image')) {
            $pivotData['side_image_url'] = $this->uploadImage(
                $request,
                'colors',
                null,
                'side_image',
            );
        }
        if ($request->hasFile('back_image')) {
            $pivotData['back_image_url'] = $this->uploadImage(
                $request,
                'colors',
                null,
                'back_image',
            );
        }

        $color = DB::transaction(function () use (
            $vehicle,
            $pivotData,
            $validated,
        ) {
            $vehicle->colors()->attach($validated['color_id'], $pivotData);

            $is_default =
                isset($validated['is_default']) && $validated['is_default'];

            // imposto come colore default al veicolo
            if (!$vehicle->default_color_id && $is_default) {
                $vehicle->update([
                    'default_color_id' => $validated['color_id'],
                ]);

                $vehicle->refresh();
            }

            return $vehicle
                ->colors()
                ->withPivot([
                    'price',
                    'front_image_url',
                    'side_image_url',
                    'back_image_url',
                ])
                ->wherePivot('color_id', $validated['color_id'])
                ->first();
        });

        return $this->apiResponse(
            true,
            new ColorResource($color),
            201,
            'Color successfully created',
        );
    }

    public function update(
        UpdateColorVehicleRequest $request,
        Vehicle $vehicle,
        Color $color,
    ) {
        $validated = $request->validated();
        $pivotData = [];

        $currentPivot = $vehicle
            ->colors()
            ->withPivot([
                'price',
                'front_image_url',
                'side_image_url',
                'back_image_url',
            ])
            ->wherePivot('color_id', $color->id)
            ->firstOrFail();

        if ($request->hasFile('front_image')) {
            $pivotData['front_image_url'] = $this->uploadImage(
                $request,
                'colors',
                $currentPivot->pivot->front_image_url,
                'front_image',
            );
        }
        if ($request->hasFile('side_image')) {
            $pivotData['side_image_url'] = $this->uploadImage(
                $request,
                'colors',
                $currentPivot->pivot->side_image_url,
                'side_image',
            );
        }
        if ($request->hasFile('back_image')) {
            $pivotData['back_image_url'] = $this->uploadImage(
                $request,
                'colors',
                $currentPivot->pivot->back_image_url,
                'back_image',
            );
        }

        $pivotData['price'] = $validated['price'];

        $updated = DB::transaction(function () use (
            $vehicle,
            $pivotData,
            $color,
            $validated,
        ) {
            $vehicle->colors()->updateExistingPivot($color->id, $pivotData);

            if (isset($validated['is_default'])) {
                if ($validated['is_default']) {
                    // Questo colore diventa il default
                    $vehicle->update(['default_color_id' => $color->id]);
                } elseif ($vehicle->default_color_id === $color->id) {
                    // Se l'utente passa false, l'auto non ha più colori di default
                    $vehicle->update(['default_color_id' => null]);
                }
            }

            $vehicle->refresh();

            return $vehicle
                ->colors()
                ->withPivot([
                    'price',
                    'front_image_url',
                    'side_image_url',
                    'back_image_url',
                ])
                ->wherePivot('color_id', $color->id)
                ->first();
        });

        return $this->apiResponse(
            true,
            new ColorResource($updated),
            200,
            'Color successfully updated',
        );
    }

    public function delete(Vehicle $vehicle, Color $color)
    {
        $vehicle->colors()->detach($color->id);

        return $this->apiResponse(
            true,
            null,
            200,
            'Colore eliminato con successo',
        );
    }
}
