<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOptionalSetupRequest;
use App\Http\Requests\UpdateOptionalSetupRequest;
use App\Models\Optional;
use App\Models\Setup;
use App\Services\OptionalSetupService;
use Illuminate\Http\Request;

class OptionalSetupController extends Controller
{
    public function __construct(
        private OptionalSetupService $optional_setup_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Setup $setup)
    {
        return $this->optional_setup_service->getAll($request, $setup);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOptionalSetupRequest $request, Setup $setup)
    {
        return $this->optional_setup_service->create($request, $setup);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Setup $setup, Optional $optional)
    {
        return $this->optional_setup_service->getSingle(
            $request,
            $setup,
            $optional,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateOptionalSetupRequest $request,
        Setup $setup,
        Optional $optional,
    ) {
        return $this->optional_setup_service->update(
            $request,
            $setup,
            $optional,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setup $setup, Optional $optional)
    {
        return $this->optional_setup_service->delete($setup, $optional);
    }
}
