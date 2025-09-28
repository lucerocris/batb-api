<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validate = $request->toSnakeCase();

        $user = User::create([
            'first_name' => $validate['first_name'],
            'last_name'  => $validate['last_name'],
            'password'   => bcrypt($validate['password']),
            'email'      => $validate['email'],
            'role'       => $validate['role'],
        ]);

        $token = auth()->login($user);

        return response()->json([
            'user'  => $user,
            'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user'  => auth()->user(),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        try {
            auth()->logout();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to logout, token invalid'], 500);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        return response()->json([
            'user'  => auth()->user(),
            'token' => auth()->refresh(),
        ]);
    }
}
