<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompatibilityRuleRequest;
use App\Models\CompatibilityRule;
use App\Services\CompatibilityRuleService;
use Illuminate\Http\Request;

class CompatibilityRuleController extends Controller
{
    public function __construct(
        private CompatibilityRuleService $rule_service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->rule_service->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompatibilityRuleRequest $request)
    {
        return $this->rule_service->create($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompatibilityRule $rule)
    {
        return $this->rule_service->delete($rule);
    }
}
