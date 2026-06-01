<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
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

// Rotte di autenticazione
// Disponibile anche per utenti non registrati
Route::prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('register', 'register')->name('register');
    });

// Rotte di /password-reset
Route::controller(PasswordResetController::class)->group(function () {
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
});

// Rotte di verifica email
Route::get('/email/verify/{id}/{hash}', [
    EmailVerificationController::class,
    'verify',
])->name('api.verification.verify');

// Rotte di GET disponibili anche per utenti non registrati
Route::apiResource('vehicles', VehicleController::class)->only([
    'index',
    'show',
]);
Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
Route::apiResource('engines', EngineController::class)->only(['index', 'show']);
Route::apiResource('setups', SetupController::class)->only(['index', 'show']);
Route::apiResource('optionals', OptionalController::class)->only([
    'index',
    'show',
]);
Route::apiResource('colors', ColorController::class)->only(['index', 'show']);
Route::apiResource('configurations', ConfigurationController::class)->only([
    'index',
    'show',
]);

Route::controller(EngineVehicleController::class)->group(function () {
    Route::get('vehicles/{vehicle}/engines', 'index');
    Route::get('vehicles/{vehicle}/engines/{engine}', 'show')->scopeBindings();
});

Route::controller(CompatibilityRuleController::class)->group(function () {
    Route::get('optionals/rules', 'index');
});

Route::controller(OptionalSetupController::class)->group(function () {
    Route::get('setups/{setup}/optionals', 'index');
    Route::get('setups/{setup}/optionals/{optional}', 'show')->scopeBindings();
});

Route::controller(SetupVehicleController::class)->group(function () {
    Route::get('vehicles/{vehicle}/setups', 'index');
    Route::get('vehicles/{vehicle}/setups/{setup}', 'show')->scopeBindings();
});

Route::controller(ColorVehicleController::class)->group(function () {
    Route::get('vehicles/{vehicle}/colors', 'index');
    Route::get('vehicles/{vehicle}/colors/{color}', 'show')->scopeBindings();
});

Route::controller(ConfigurationOptionalController::class)->group(function () {
    Route::get('configurations/{configuration}/optionals', 'index');
    Route::get(
        'configurations/{configuration}/optionals/{optional}',
        'show',
    )->scopeBindings();
});

// Middleware di autenticazione e verifica email
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::apiResource('vehicles', VehicleController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource('brands', BrandController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource('engines', EngineController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource('setups', SetupController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource('optionals', OptionalController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource('colors', ColorController::class)->except([
        'index',
        'show',
    ]);
    Route::apiResource(
        'configurations',
        ConfigurationController::class,
    )->except(['index', 'show']);
    Route::apiResource('users', UserController::class)->except(['store']);

    Route::controller(EngineVehicleController::class)->group(function () {
        Route::post('vehicles/{vehicle}/engines', 'store')->scopeBindings();
        Route::patch(
            'vehicles/{vehicle}/engines/{engine}',
            'update',
        )->scopeBindings();
        Route::delete('vehicles/{vehicle}/engines/{engine}', 'destroy');
    });

    Route::controller(CompatibilityRuleController::class)->group(function () {
        Route::post('optionals/rules', 'store');
        Route::delete('optionals/rules/{rule}', 'destroy');
    });

    Route::controller(OptionalSetupController::class)->group(function () {
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

    Route::controller(SetupVehicleController::class)->group(function () {
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

    Route::controller(ColorVehicleController::class)->group(function () {
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

    Route::controller(ConfigurationOptionalController::class)->group(
        function () {
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

    Route::prefix('auth')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('logout', 'logout')->name('logout');
            Route::get('me', 'me')->name('me');
        });
});
