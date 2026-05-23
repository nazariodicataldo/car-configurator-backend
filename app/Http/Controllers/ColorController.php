<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;
use App\Services\ColorService;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function __construct(private ColorService $color_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->color_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreColorRequest $request)
    {
        return $this->color_service->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Color $color)
    {
        return $this->color_service->getSingle($request, $color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateColorRequest $request, Color $color)
    {
        return $this->color_service->update($request, $color);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        return $this->color_service->delete($color);
    }
}
