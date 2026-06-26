<?php

namespace App\Services;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Traits\ApiResponse;
use App\Traits\HasFileUpload;
use Illuminate\Http\Request;

class ColorService
{
    use ApiResponse, HasFileUpload;
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

        if ($colors instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return $this->apiResponse(
                true,
                $colors->setCollection(
                    ColorResource::collection($colors->items())->collection,
                ),
                200,
                'Colori recuperati con successo',
            );
        }

        return $this->apiResponse(
            true,
            ColorResource::collection($colors),
            200,
            'Colori recuperati con successo',
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
            'Colore recuperato con successo',
        );
    }

    public function create(StoreColorRequest $request)
    {
        $validated = $request->validated();
        $uploadedPath = null;

        try {
            if ($request->hasFile('img')) {
                $uploadedPath = $this->uploadImage($request, 'colors');
                $validated['img_url'] = $uploadedPath;
            }

            $color = Color::create($validated);

            return $this->apiResponse(
                true,
                new ColorResource($color),
                201,
                'Colore creato con successo',
            );
        } catch (\Exception $e) {
            // Se il DB fallisce dopo l'upload, cancella il file
            if ($uploadedPath) {
                $this->deleteImage($uploadedPath);
            }
            return $this->apiResponse(
                false,
                null,
                500,
                'Errore durante la creazione',
            );
        }
    }

    public function update(UpdateColorRequest $request, Color $color)
    {
        $validated = $request->validated();
        $uploadedPath = null;

        try {
            // Caso 1 — l'utente ha caricato un file nuovo
            if ($request->hasFile('img')) {
                // Salvo il vecchio path prima di sovrascrivere
                $oldPath = $color->img_url;

                $uploadedPath = $this->uploadImage(
                    $request,
                    'colors',
                    $oldPath,
                );
                $validated['img_url'] = $uploadedPath;
                // Caso 2 — l'utente non ha caricato nulla MA ha esplicitamente azzerato il campo
            } elseif (
                array_key_exists('img', $validated) &&
                !$validated['img']
            ) {
                $validated['img_url'] = null;
            }

            $color->update($validated);

            $color->refresh();

            // Solo dopo che il DB ha confermato, cancello il vecchio file
            if ($request->hasFile('img') && isset($oldPath)) {
                $this->deleteImage($oldPath);
            } elseif (
                array_key_exists('img_url', $validated) &&
                !$validated['img_url']
            ) {
                $this->deleteImage($color->getOriginal('img_url'));
            }

            return $this->apiResponse(
                true,
                new ColorResource($color),
                201,
                'Colore modificato con successo',
            );
        } catch (\Exception $e) {
            // Il DB è fallito — cancella solo il file appena caricato, non quello vecchio
            if ($uploadedPath) {
                $this->deleteImage($uploadedPath);
            }
            return $this->apiResponse(
                false,
                null,
                500,
                "Errore durante l'aggiornamento",
            );
        }
    }

    public function delete(Color $color)
    {
        $color->delete();

        return $this->apiResponse(
            true,
            null,
            200,
            'Colore eliminato con successo',
        );
    }
}
