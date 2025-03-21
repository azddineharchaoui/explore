<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        $user = User::where('email', $request->email)->first(); 

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }
        $token = $user->createToken($user->name.'Auth-token')->plainTextToken;
        return response()->json([
            'message' => 'Login Successful',
            'token_type' => 'Bearer',
            'token' => $token
        ],200);
    }

    public function register(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user)
        {
            $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
            return response()->json([
                'message' => 'Registration Successful',
                'token_type' => 'Bearer', 
                'token' => $token
            ],200);
        } 
        else 
        {
            return response()->json([
                'message' => 'Registration Failed',
            ],500);
        }
    }

    public function logout(Request $request) : JsonResponse
    {
        auth()->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function profile(Request $request) : JsonResponse
    {
        return response()->json([
            'message' => 'User profile',
            'user' => auth()->user()
        ], 200);
    }
}