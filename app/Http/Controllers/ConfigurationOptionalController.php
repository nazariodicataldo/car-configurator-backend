<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationOptionalRequest;
use App\Http\Requests\UpdateConfigurationOptionalRequest;
use App\Models\Configuration;
use App\Models\Optional;
use App\Services\ConfigurationOptionalService;
use Illuminate\Http\Request;

class ConfigurationOptionalController extends Controller
{
    public function __construct(
        private ConfigurationOptionalService $configuration_optional_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Configuration $configuration)
    {
        return $this->configuration_optional_service->getAll(
            $request,
            $configuration,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreConfigurationOptionalRequest $request,
        Configuration $configuration,
    ) {
        return $this->configuration_optional_service->create(
            $request,
            $configuration,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Request $request,
        Configuration $configuration,
        Optional $optional,
    ) {
        return $this->configuration_optional_service->getSingle(
            $request,
            $configuration,
            $optional,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateConfigurationOptionalRequest $request,
        Configuration $configuration,
        Optional $optional,
    ) {
        return $this->configuration_optional_service->update(
            $request,
            $configuration,
            $optional,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuration $configuration, Optional $optional)
    {
        return $this->configuration_optional_service->delete(
            $configuration,
            $optional,
        );
    }
}
