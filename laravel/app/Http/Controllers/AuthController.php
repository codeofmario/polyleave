<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, JwtService $jwt): JsonResponse
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'provider'    => null,
            'provider_id' => null,
        ]);

        $user->roles()->attach(Role::where('name', 'user')->value('id'));

        $tokens = $jwt->issue($user->id);

        return response()->json([
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ], 201);
    }

    public function login(Request $request, JwtService $jwt): JsonResponse
    {
        $creds = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $creds['email'])->first();

        if (!$user || !Hash::check($creds['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->roles()->where('name', 'user')->exists()) {
            $user->roles()->attach(Role::where('name', 'user')->value('id'));
        }

        $tokens = $jwt->issue($user->id);

        return response()->json([
            'access_token'  => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ]);
    }

    public function refresh(Request $request, JwtService $jwt): JsonResponse
    {
        $request->validate(['refresh_token' => 'required|string']);

        return response()->json($jwt->refresh($request->refresh_token));
    }

    public function logout(Request $request, JwtService $jwt): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            $jwt->logout($token);
        }

        return response()->json(['msg' => 'Logged out'], 200);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'roles' => $user->roles()->pluck('name'),
        ]);
    }
}
