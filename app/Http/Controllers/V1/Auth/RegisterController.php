<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            Log::info('Registration attempt started', ['email' => $request->email]);
            
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'phone_no' => 'required|string|max:15|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            
            Log::info('Validation passed', ['email' => $request->email]);
            
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'password' => Hash::make($request->password),
            ]);
            
            Log::info('User created successfully', ['user_id' => $user->id]);
            
            // Create token
            $token = $user->createToken('auth-token')->plainTextToken;
            
            Log::info('Token created successfully', ['user_id' => $user->id]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'email' => $request->email ?? 'not provided'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'email' => $request->email ?? 'not provided'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 