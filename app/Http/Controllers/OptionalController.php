<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOptionalRequest;
use App\Http\Requests\UpdateOptionalRequest;
use App\Models\Optional;
use App\Services\OptionalService;
use Illuminate\Http\Request;

class OptionalController extends Controller
{
    public function __construct(private OptionalService $optional_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->optional_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOptionalRequest $request)
    {
        return $this->optional_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Optional $optional)
    {
        return $this->optional_service->getSingle($request, $optional);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOptionalRequest $request, Optional $optional)
    {
        return $this->optional_service->update($request, $optional);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Optional $optional)
    {
        return $this->optional_service->delete($optional);
    }
}
