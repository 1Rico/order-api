<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $response = [
            'token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer'
        ];

        return $this->sendResponse($response, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        if ($request->authenticate()) {
            return $this->sendResponse([
                'token' => Auth::user()->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer'
            ]);
        }

        return $this->sendError('Unauthorised.', [],401);
    }
}
