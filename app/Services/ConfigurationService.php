<?php

namespace App\Services;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;
use App\Models\Optional;
use App\Models\Setupconfiguration;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->when($request->boolean('withconfiguration'), function ($query) {
                return $query->with('configuration');
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
            /* Filtro per configuration id */
            ->when($request->query('configurationId'), function (
                $query,
                $configuration_id,
            ) {
                return $query->where('configuration_id', $configuration_id);
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

        /* Se l'utente non è admin, ritorna solo i suoi post */
        $auth_user = Auth::user();
        if ($auth_user && $auth_user->role !== 'admin') {
            $query->where('user_id', $auth_user->id);
        }

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

        if (
            $configurations instanceof
            \Illuminate\Pagination\LengthAwarePaginator
        ) {
            return $this->apiResponse(
                true,
                $configurations->setCollection(
                    ConfigurationResource::collection($configurations->items())
                        ->collection,
                ),
                200,
                'Veicoli recuperati con successo',
            );
        }

        return $this->apiResponse(
            true,
            ConfigurationResource::collection($configurations),
            200,
            'Configurazioni recuperate con successo',
        );
    }

    public function getSingle(Request $request, Configuration $configuration)
    {
        // Carico tutte le relazioni
        $configuration->load([
            'user',
            'configuration',
            'engine',
            'setup',
            'color',
            'optionals',
        ]);

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            200,
            'Configurazione recuperata con successo',
        );
    }

    public function create(StoreConfigurationRequest $request)
    {
        $data = $request->validated();

        // Transaction per creare la configurazione e popolare la tabella pivot con gli optionals
        $configuration = DB::transaction(function () use ($data) {
            $configuration = Configuration::create([
                ...$data,
                'user_id' => Auth::user()->id,
            ]);

            if (!empty($data['optionals_id'])) {
                $optionals = $data['optionals_id'];
                $syncData = []; // Array temporaneo per accumulare i dati

                // Recupero il Setupconfiguration una volta sola fuori dal loop
                $setupconfiguration = Setupconfiguration::where(
                    'setup_id',
                    $configuration->setup_id,
                )
                    ->where(
                        'configuration_id',
                        $configuration->configuration_id,
                    )
                    ->firstOrFail();

                foreach ($optionals as $opt => $opt_id) {
                    $optional = Optional::findOrFail($opt_id);

                    // Mi prendo il record dalla pivot tra setup e optional per ottenere prezzo e is_included
                    $optionalSetup = $setupconfiguration
                        ->optionals()
                        ->withPivot(['price', 'is_included'])
                        ->where('optionals.id', $opt_id)
                        ->firstOrFail();

                    $syncData[$opt_id] = [
                        'id' => (string) Str::uuid(),
                        'optional_price' => $optionalSetup->pivot->price,
                        'is_included' => $optionalSetup->pivot->is_included,
                    ];
                }

                // Eseguo una query unica
                $configuration->optionals()->sync($syncData);
            }

            return $configuration;
        });

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            201,
            'Configurazione creata con successo',
        );
    }

    public function update(
        UpdateConfigurationRequest $request,
        Configuration $configuration,
    ) {
        $data = $request->validated();

        $configuration = DB::transaction(function () use (
            $data,
            $configuration,
        ) {
            $configuration->update($data);
            $configuration->refresh();

            if (!empty($data['optionals'])) {
                $optionals = $data['optionals'];
                $syncData = []; // Array temporaneo per accumulare i dati

                foreach ($optionals as $opt => $opt_id) {
                    $optional = Optional::findOrFail($opt_id);

                    // Mi prendo il record dalla pivot tra setup e optional
                    $setup = $optional
                        ->setups()
                        ->wherePivot('setup_id', $configuration->setup_id)
                        ->firstOrFail();

                    $syncData[$opt_id] = [
                        'id' => (string) Str::uuid(),
                        'optional_price' => $setup->pivot->price,
                        'is_included' => $setup->pivot->is_included,
                    ];
                }

                // Eseguo una query unica
                $configuration->optionals()->sync($syncData);
            }

            return $configuration;
        });

        return $this->apiResponse(
            true,
            new ConfigurationResource($configuration),
            201,
            'Configurazione aggiornata con successo',
        );
    }

    public function delete(Configuration $configuration)
    {
        $configuration->delete();

        return $this->apiResponse(
            true,
            null,
            200,
            'Configurazione eliminata con successo',
        );
    }
}
