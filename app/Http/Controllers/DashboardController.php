<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard_service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->dashboard_service->index();
    }
}
