<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

/**
 * @group Authentication
 * 
 * APIs for managing user authentication
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * Create a new user account and return an authentication token.
     * 
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email address of the user. Example: john@example.com
     * @bodyParam password string required The password for the account. Must be at least 8 characters. Example: password123
     * 
     * @response 201 {
     *  "user": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "john@example.com",
     *    "created_at": "2024-03-19T12:00:00.000000Z",
     *    "updated_at": "2024-03-19T12:00:00.000000Z"
     *  },
     *  "token": "1|abcdef123456..."
     * }
     * @response 422 {
     *  "message": "The given data was invalid.",
     *  "errors": {
     *    "email": ["The email has already been taken."]
     *  }
     * }
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Return response with user data and generated token
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Login user
     * 
     * Authenticate a user and return an authentication token.
     * 
     * @bodyParam email string required The email address of the user. Example: john@example.com
     * @bodyParam password string required The password for the account. Example: password123
     * 
     * @response 200 {
     *  "user": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "john@example.com",
     *    "created_at": "2024-03-19T12:00:00.000000Z",
     *    "updated_at": "2024-03-19T12:00:00.000000Z"
     *  },
     *  "token": "1|abcdef123456..."
     * }
     * @response 401 {
     *  "message": "Unauthorized"
     * }
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        // If authentication fails
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Logout user
     * 
     * Revoke the current user's authentication token.
     * 
     * @authenticated
     * 
     * @response 200 {
     *  "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        // Revoke the user's current token
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }
}
