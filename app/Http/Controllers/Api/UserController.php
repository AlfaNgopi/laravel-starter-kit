<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use OpenApi\Attributes as OA;

class UserController extends Controller
{
    
    #[OA\Get(
        path: '/api/users',
        operationId: 'getUsers',
        summary: 'Get all users',
        description: 'Retrieve a list of all users with their associated roles and permissions',
        tags: ['Users']
    )]
    #[OA\Response(
        response: 200,
        description: 'List of users retrieved successfully',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    new OA\Property(
                        property: 'roles',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'name', type: 'string'),
                                new OA\Property(property: 'description', type: 'string', nullable: true),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: 'permissions',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'name', type: 'string'),
                                new OA\Property(property: 'description', type: 'string', nullable: true),
                            ]
                        )
                    ),
                ],
                type: 'object'
            )
        )
    )]
    public function index(): JsonResponse
    {
        $users = User::with('roles.permissions')->paginate(15);

        return response()->json($users);
    }

    #[OA\Get(
        path: '/api/users/me',
        operationId: 'getCurrentUser',
        summary: 'Get current user',
        description: 'Retrieve details of the currently authenticated user with their associated roles and permissions',
        tags: ['Users']
    )]
    #[OA\Response(
        response: 200,
        description: 'Current user retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                new OA\Property(
                    property: 'roles',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'description', type: 'string', nullable: true),
                        ]
                    )
                ),
                new OA\Property(
                    property: 'permissions',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'description', type: 'string', nullable: true),
                        ]
                    )
                ),
            ],
            type: 'object'
        )
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('roles.permissions'));
    }
}
