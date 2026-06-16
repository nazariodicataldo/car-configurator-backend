<?php

namespace App\Services;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserService
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
        $allowedColumns = ['created_at', 'id', 'first_name'];
        $column = in_array($request->query('orderBy'), $allowedColumns)
            ? $request->query('orderBy')
            : 'id';

        $allowedOrders = ['asc', 'desc'];

        $order = in_array(strtolower($request->query('order')), $allowedOrders)
            ? $request->query('order')
            : 'asc';

        $query = User::query()
            // Carico le configurazioni
            ->when($request->boolean('withConfigurations'), function ($query) {
                return $query->with('configurations');
            })
            /* Filtro per nome */
            ->when($request->query('first_name'), function ($query, $name) {
                return $query->where('name', 'ILIKE', '%' . $name . '%');
            })
            /* Filtro per email */
            ->when($request->query('email'), function ($query, $email) {
                return $query->where('email', 'ILIKE', '%' . $email . '%');
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
        $users = $this->filter($request);
        return $this->apiResponse(
            true,
            UserResource::collection($users),
            200,
            'Utenti caricati con successo',
        );
    }

    public function getSingle(Request $request, User $user)
    {
        if ($request->boolean('withConfigurations')) {
            $user->load('configurations');
        }

        return $this->apiResponse(
            true,
            new UserResource($user),
            200,
            'Utente caricato con successo',
        );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update($data);

        $user->refresh();

        return $this->apiResponse(
            true,
            new UserResource($user),
            201,
            'Utente modificato con successo',
        );
    }

    public function delete(User $user)
    {
        $user->delete();

        return $this->apiResponse(true, null, 200, 'Utente cancellato con successo');
    }
}
