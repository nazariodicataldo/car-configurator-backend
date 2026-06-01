<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Models\Configuration;
use App\Services\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConfigurationController extends Controller
{
    public function __construct(private ConfigurationService $configuration_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Configuration::class);
        return $this->configuration_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConfigurationRequest $request)
    {
        return $this->configuration_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Configuration $configuration)
    {
        Gate::authorize('view', $configuration);
        return $this->configuration_service->getSingle($request, $configuration);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConfigurationRequest $request, Configuration $configuration)
    {
        Gate::authorize('update', $configuration);
        return $this->configuration_service->update($request, $configuration);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuration $configuration)
    {
        Gate::authorize('delete', $configuration);
        return $this->configuration_service->delete($configuration);
    }
}
