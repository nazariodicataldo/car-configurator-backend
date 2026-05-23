<?php

namespace App\Services;

use App\Http\Requests\StoreCompatibilityRuleRequest;
use App\Http\Resources\CompatibilityRuleResource;
use App\Models\CompatibilityRule;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CompatibilityRuleService
{
    use ApiResponse;
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    // Verifica se esista un record di incompatibilità tra due optionals
    public static function checkExistsRule(
        string $optional_a_id,
        string $optional_b_id,
    ): bool {
        return CompatibilityRule::where(function ($q) use ($optional_a_id, $optional_b_id) {
            $q->where('optional_a_id', $optional_a_id)
              ->where('optional_b_id', $optional_b_id);
        })->orWhere(function ($q) use ($optional_a_id, $optional_b_id) {
            $q->where('optional_a_id', $optional_b_id)
              ->where('optional_b_id', $optional_a_id);
        })->exists();
    }

    private function filter(Request $request)
    {
        // Mi prendo i valori di perPage e page
        $perPage = $request->query('perPage');
        $page = $request->query('page');

        // Colonne ammesse
        $allowedColumns = ['created_at', 'id'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'id';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = CompatibilityRule::query()
            /* Filtro per nome */
            ->when($request->query('optional_id'), function (
                $query,
                $optional_id,
            ) {
                return $query
                    ->where('optional_a_id', $optional_id)
                    ->orWhere('optional_b_id', $optional_id);
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
        $rules = $this->filter($request);

        return $this->apiResponse(
            true,
            CompatibilityRuleResource::collection($rules),
            200,
            'Rules successfully fetched',
        );
    }

    

    public function create(StoreCompatibilityRuleRequest $request)
    {
        $data = $request->validated();

        
        // Verifico se l'utente ha passato due optional_id diversi
        if ($data['optional_a_id'] === $data['optional_b_id']) {
            return $this->apiResponse(
                false,
                null,
                422,
                'Optional ids must be different',
            );
        }

        // Validazione: i due optional non devono avere già una regola
        if (
            static::checkExistsRule(
                $data['optional_a_id'],
                $data['optional_b_id'],
            )
        ) {
            return $this->apiResponse(false, null, 422, 'Rule already exists');
        }

        $rule = CompatibilityRule::create($data);

        return $this->apiResponse(
            true,
            new CompatibilityRuleResource($rule),
            201,
            'Rule successfully created',
        );
    }

    public function delete(CompatibilityRule $rule)
    {
        $rule->delete();

        return $this->apiResponse(true, null, 204, 'Rule successfully deleted');
    }
}
