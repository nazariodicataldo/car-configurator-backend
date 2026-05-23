<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ColorVehicleController;
use App\Http\Controllers\CompatibilityRuleController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ConfigurationOptionalController;
use App\Http\Controllers\EngineController;
use App\Http\Controllers\EngineVehicleController;
use App\Http\Controllers\OptionalController;
use App\Http\Controllers\OptionalSetupController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SetupVehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Middleware di autenticazione
Route::middleware('auth:sanctum')->group(function () {
    // Rotte di /vehicles
    Route::apiResource('vehicles', VehicleController::class);

    // Rotte di /brands
    Route::apiResource('brands', BrandController::class);

    // Rotte di /engines
    Route::apiResource('engines', EngineController::class);

    // Rotte di /vehicles/{vehicle}/engines
    Route::controller(EngineVehicleController::class)->group(function () {
        Route::get('vehicles/{vehicle}/engines', 'index');
        Route::get(
            'vehicles/{vehicle}/engines/{engine}',
            'show',
        )->scopeBindings();
        Route::post('vehicles/{vehicle}/engines', 'store')->scopeBindings();
        Route::patch(
            'vehicles/{vehicle}/engines/{engine}',
            'update',
        )->scopeBindings();
        Route::delete('vehicles/{vehicle}/engines/{engine}', 'destroy');
    });

    // Rotte di /optionals/rules
    Route::controller(CompatibilityRuleController::class)->group(function () {
        Route::get('optionals/rules', 'index');
        Route::post('optionals/rules', 'store');
        Route::delete('optionals/rules/{rule}', 'destroy');
    });

    // Rotte di /setups
    Route::apiResource('setups', SetupController::class);

    // Rotte di /optionals
    Route::apiResource('optionals', OptionalController::class);

    // Rotte di /setups/{setup}/optionals
    Route::controller(OptionalSetupController::class)->group(function () {
        Route::get('setups/{setup}/optionals', 'index');
        Route::get(
            'setups/{setup}/optionals/{optional}',
            'show',
        )->scopeBindings();
        Route::post('setups/{setup}/optionals', 'store')->scopeBindings();
        Route::patch(
            'setups/{setup}/optionals/{optional}',
            'update',
        )->scopeBindings();
        Route::delete(
            'setups/{setup}/optionals/{optional}',
            'destroy',
        )->scopeBindings();
    });

    // Rotte di /vehicles/{vehicle}/setups
    Route::controller(SetupVehicleController::class)->group(function () {
        Route::get('vehicles/{vehicle}/setups', 'index');
        Route::get(
            'vehicles/{vehicle}/setups/{setup}',
            'show',
        )->scopeBindings();
        Route::post('vehicles/{vehicle}/setups', 'store')->scopeBindings();
        Route::patch(
            'vehicles/{vehicle}/setups/{setup}',
            'update',
        )->scopeBindings();
        Route::delete(
            'vehicles/{vehicle}/setups/{setup}',
            'destroy',
        )->scopeBindings();
    });

    // Rotte di /colors
    Route::apiResource('colors', ColorController::class);

    // Rotte di /vehicles/{vehicle}/colors
    Route::controller(ColorVehicleController::class)->group(function () {
        Route::get('vehicles/{vehicle}/colors', 'index');
        Route::get(
            'vehicles/{vehicle}/colors/{color}',
            'show',
        )->scopeBindings();
        Route::post('vehicles/{vehicle}/colors', 'store')->scopeBindings();
        Route::patch(
            'vehicles/{vehicle}/colors/{color}',
            'update',
        )->scopeBindings();
        Route::delete(
            'vehicles/{vehicle}/colors/{color}',
            'destroy',
        )->scopeBindings();
    });

    // Rotte di /configurations
    Route::apiResource('configurations', ConfigurationController::class);

    // Rotte di /configurations/{configuration}/optionals
    Route::controller(ConfigurationOptionalController::class)->group(
        function () {
            Route::get('configurations/{configuration}/optionals', 'index');
            Route::get(
                'configurations/{configuration}/optionals/{optional}',
                'show',
            )->scopeBindings();
            Route::post(
                'configurations/{configuration}/optionals',
                'store',
            )->scopeBindings();
            Route::delete(
                'configurations/{configuration}/optionals/{optional}',
                'destroy',
            )->scopeBindings();
        },
    );

    // Rotte di /users
    Route::apiResource('users', UserController::class)->except('store');
});
