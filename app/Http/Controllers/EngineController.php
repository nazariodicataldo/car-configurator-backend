<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEngineRequest;
use App\Http\Requests\UpdateEngineRequest;
use App\Models\Engine;
use App\Services\EngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EngineController extends Controller
{
    public function __construct(private EngineService $engine_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->engine_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEngineRequest $request)
    {
        Gate::authorize('create', Engine::class);
        return $this->engine_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Engine $engine)
    {
        return $this->engine_service->getSingle($request, $engine);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEngineRequest $request, Engine $engine)
    {
        Gate::authorize('update', $engine);
        return $this->engine_service->update($request, $engine);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Engine $engine)
    {
        Gate::authorize('delete', $engine);
        return $this->engine_service->delete($engine);
    }
}
