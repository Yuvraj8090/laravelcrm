<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        /** @var User $user */
        $user = $request->user();
        $plainTextToken = Str::random(60);
        $user->update(['api_token' => hash('sha256', $plainTextToken)]);

        return response()->json([
            'token' => $plainTextToken,
            'user' => $user->only('id', 'name', 'email'),
        ]);
    }
}
