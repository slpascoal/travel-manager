<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Silas Lopes Pascoal"
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Registers a new user and returns a token for authentication.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Tester"),
     *             @OA\Property(property="email", type="string", format="email", example="tester@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error."
     *     )
     * )
     */

    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        return response()->json(['user' => $user, 'token' => $token->plainTextToken], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login an existing user",
     *     description="Logs in a user with email and password and returns a token for authentication.",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="tester@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials."
     *     )
     * )
     */

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'message' => 'The provided credentials are incorrect.'
            ];
        }

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     description="Logs out the authenticated user by deleting their tokens.",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are logged out")
     *         )
     *     )
     * )
     */

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out'
        ];
    }
}
