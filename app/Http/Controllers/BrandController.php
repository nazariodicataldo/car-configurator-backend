<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    public function __construct(private BrandService $brand_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->brand_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        Gate::authorize('create', Brand::class);
        return $this->brand_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Brand $brand)
    {
        return $this->brand_service->getSingle($request, $brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        Gate::authorize('update', $brand);
        return $this->brand_service->update($request, $brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        Gate::authorize('delete', $brand);
        return $this->brand_service->delete($brand);
    }
}
