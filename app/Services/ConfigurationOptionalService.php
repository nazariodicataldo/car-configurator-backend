<?php

namespace App\Services;

use App\Http\Requests\StoreConfigurationOptionalRequest;
use App\Http\Requests\UpdateConfigurationOptionalRequest;
use App\Http\Resources\OptionalResource;
use App\Models\Configuration;
use App\Models\Optional;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ConfigurationOptionalService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    private function filter(Request $request, Configuration $configuration)
    {
        // Mi prendo i valori di perPage e page
        $perPage = $request->query('perPage');
        $page = $request->query('page');

        // Colonne ammesse
        $allowedColumns = ['created_at', 'id', 'optional_price'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'optional_price';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = $configuration
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

    public function getAll(Request $request, Configuration $configuration)
    {
        $data = $this->filter($request, $configuration);
        return $this->apiResponse(
            true,
            OptionalResource::collection($data),
            200,
            'Optionals successfully fetched',
        );
    }

    public function getSingle(
        Request $request,
        Configuration $configuration,
        Optional $optional,
    ) {
        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            200,
            'Optional successfully fetched',
        );
    }

    public function create(
        StoreConfigurationOptionalRequest $request,
        Configuration $configuration,
    ) {
        $data = $request->validated();

        $optional = Optional::findOrFail($data['optional_id']);

        // Mi prendo il record dalla pivot tra setup e optional
        $setup = $optional
            ->setups()
            ->wherePivot('setup_id', $configuration->setup_id)
            ->firstOrFail();

        // Verifico se l'optional da aggiungere sia compatibile con quelli già aggiunti
        $conflicts = [];
        // Mi prendo tutte gli optional salvati nella tabella di configurazione salvati per ora
        $existing_optionals = $configuration->optionals()->get()->keyBy('id');

        // Mi prendo le loro chiavi
        $optional_ids = $existing_optionals->keys();

        //Itero sull'array di optional_ids
        foreach ($optional_ids as $id) {
            //Verifico se esiste una record tra i due optional e quindi se c'è un conflito
            if (
                CompatibilityRuleService::checkExistsRule(
                    $id,
                    $data['optional_id'],
                )
            ) {
                $conflicts[] = new OptionalResource(
                    $existing_optionals->get($id),
                ); // mi salvo l'optional non compatibile
            }
        }

        if (!empty($not_compatibile)) {
            return $this->apiResponse(
                false,
                $conflicts,
                422,
                'Some optionals are not compatibile',
            );
        }

        $configuration->optionals()->attach($data['optional_id'], [
            'id' => Str::uuid(), //creo l'uuid manualmente
            'optional_price' => $setup->pivot->price,
            'is_included' => $setup->pivot->is_included,
        ]);

        // Mi ritorno il record appena collegato
        $data = $configuration
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
        UpdateConfigurationOptionalRequest $request,
        Configuration $configuration,
        Optional $optional,
    ) {
        $data = $request->validated();

        $configuration->optionals()->updateExistingPivot($optional->id, $data);

        return $this->apiResponse(
            true,
            new OptionalResource($optional),
            201,
            'Optional successfully updated',
        );
    }

    public function delete(Configuration $configuration, Optional $optional)
    {
        $configuration->optionals()->detach($optional->id);

        return $this->apiResponse(
            true,
            null,
            204,
            'Optional successfully deleted',
        );
    }
}
