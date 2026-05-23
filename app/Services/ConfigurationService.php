<?php

namespace App\Services;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationService
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

        $query = Configuration::query()
            // Carico l'utente
            ->when($request->boolean('withUser'), function ($query) {
                return $query->with('user');
            })
            // Carico il veicolo
            ->when($request->boolean('withVehicle'), function ($query) {
                return $query->with('vehicle');
            })
            // Carico il motore
            ->when($request->boolean('withEngine'), function ($query) {
                return $query->with('engine');
            })
            // Carico gli allestimenti
            ->when($request->boolean('withSetup'), function ($query) {
                return $query->with('setup');
            })
            // Carico il colore
            ->when($request->boolean('withColor'), function ($query) {
                return $query->with('color');
            })
            // Carico gli optional
            ->when($request->boolean('withOptionals'), function ($query) {
                return $query->with('optionals');
            })
            // Calcolo il prezzo degli optional
            ->withSum(
                [
                    'optionals as total_optional_price' => function ($query) {
                        $query->where('is_included', false); // Somma solo gli optional non inclusi nell'allestimento
                    },
                ],
                'configuration_optionals.optional_price',
            )
            /* Filtro per user id */
            ->when($request->query('userId'), function ($query, $user_id) {
                return $query->where('user_id', $user_id);
            })
            /* Filtro per vehicle id */
            ->when($request->query('vehicleId'), function (
                $query,
                $vehicle_id,
            ) {
                return $query->where('vehicle_id', $vehicle_id);
            })
            /* Filtro per engine id */
            ->when($request->query('engineId'), function ($query, $engine_id) {
                return $query->where('engine_id', $engine_id);
            })
            /* Filtro per setup id */
            ->when($request->query('setupId'), function ($query, $setup_id) {
                return $query->where('setup_id', $setup_id);
            })
            /* Filtro per color id */
            ->when($request->query('colorId'), function ($query, $color_id) {
                return $query->where('color_id', $color_id);
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
        $configurations = $this->filter($request);
        return $this->apiResponse(
            true,
            ConfigurationResource::collection($configurations),
            200,
            'Configurations successfully fetched',
        );
    }

    public function getSingle(Request $request, Configuration $configuration)
    {
        // Carico tutte le relazioni
        $configuration->load([
            'user',
            'vehicle',
            'engine',
            'setup',
            'color',
            'optionals',
        ]);

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            200,
            'Configuration successfully fetched',
        );
    }

    public function create(StoreConfigurationRequest $request)
    {
        $data = $request->validated();

        $configuration = Configuration::create($data);

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            201,
            'Configuration successfully created',
        );
    }

    public function update(
        UpdateConfigurationRequest $request,
        Configuration $configuration,
    ) {
        $data = $request->validated();

        $configuration = $configuration->update($data);

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            201,
            'Configuration successfully updated',
        );
    }

    public function delete(Configuration $configuration)
    {
        $configuration->delete();

        return $this->apiResponse(
            true,
            null,
            204,
            'Configuration successfully deleted',
        );
    }
}
