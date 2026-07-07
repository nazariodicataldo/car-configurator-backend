<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'registered_from_mobile' =>
                $data['registered_from_mobile'] ?? false,
        ]);

        event(new Registered($user));

        return $this->apiResponse(
            true,
            new UserResource($user),
            201,
            'Registrazione completata con successo',
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->apiResponse(
                false,
                null,
                401,
                'Credenziali non valide',
            );
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            return $this->apiResponse(false, null, 422, 'Email non verificata');
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(
            true,
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            200,
            'Accesso effettuato con successo',
        );
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->apiResponse(
            true,
            null,
            200,
            'Logout effettuato con successo',
        );
    }

    public function me()
    {
        return $this->apiResponse(
            true,
            new UserResource(Auth::user()->load('configurations')),
            200,
            'Utente caricato con successo',
        );
    }
}
