<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    #[OA\Post(
        path: '/api/loginjwt',
        summary: 'User Login',
        tags: ['Authentication']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@mail.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Berhasil Login',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Kredensial Salah')]

    public function loginJWT(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        // auth('api')->attempt akan otomatis mengecek email/pass dan membuat JWT
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Login Gagal, Cek Email/Password'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',

            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }


    #[OA\Post(
        path: '/api/logoutjwt',
        summary: 'User Logout',
        tags: ['Authentication']
    )]
    #[OA\Response(
        response: 200,
        description: 'Berhasil Logout akan menghapus JWT',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Successfully logged out')
            ]
        )
    )]
    public function logoutJWT()
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
