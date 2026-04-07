<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Json;

use OpenApi\Attributes as OA;

class SuppliersAPI extends Controller
{
    // CRUD API

    #[OA\Get(
        path: '/api/suppliers',
        operationId: 'getSuppliers',
        summary: 'Get all suppliers',
        description: 'Retrieve a list of all suppliers with their associated users',
        tags: ['Suppliers']
    )]
    #[OA\Response(
        response: 200,
        description: 'List of suppliers retrieved successfully',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'user_id', type: 'integer'),
                    new OA\Property(property: 'notelp', type: 'string', nullable: true),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'email', type: 'string', format: 'email'),
                        ]
                    ),
                ],
                type: 'object'
            )
        )
    )]
    public function index(): JsonResponse
    {
        $suppliers = Supplier::with('user')->get();

        return response()->json($suppliers);
    }

    #[OA\Post(
        path: '/api/suppliers',
        operationId: 'createSupplier',
        summary: 'Create a new supplier',
        description: 'Create a new supplier with associated user account',
        tags: ['Suppliers']
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Supplier data',
        content: new OA\JsonContent(
            required: ['name', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'John Doe'),
                new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'notelp', type: 'string', maxLength: 20, nullable: true, example: '081234567890'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Supplier created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Supplier created successfully.'),
                new OA\Property(
                    property: 'supplier',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'user_id', type: 'integer'),
                        new OA\Property(property: 'notelp', type: 'string', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'name', type: 'string'),
                                new OA\Property(property: 'email', type: 'string', format: 'email'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error'
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'notelp' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);


        $supplier = Supplier::create([
            'user_id' => $user->id,
            'notelp' => $validated['notelp'],

        ]);


        return response()->json([
            'message' => 'Supplier created successfully.',
            'supplier' => $supplier->load('user'),
        ]);
    }

    #[OA\Get(
        path: '/api/suppliers/{supplier_id}',
        operationId: 'getSupplier',
        summary: 'Get a specific supplier',
        description: 'Retrieve details of a specific supplier by ID',
        tags: ['Suppliers']
    )]
    #[OA\Parameter(
        name: 'supplier_id',
        in: 'path',
        required: true,
        description: 'Supplier ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Supplier retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'supplier',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'user_id', type: 'integer'),
                        new OA\Property(property: 'notelp', type: 'string', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                ),
                new OA\Property(
                    property: 'users',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Supplier not found'
    )]
    public function show(int $supplier_id): JsonResponse
    {
        // $supplier->load('roles.permissions');
        $supplier = Supplier::where('id', $supplier_id)->first();
        $user = User::where('id', $supplier->user_id)->first();

        return response()->json([
            'supplier' => $supplier,
            'users' => $user
        ]);
    }

    #[OA\Put(
        path: '/api/suppliers/{supplier_id}',
        operationId: 'updateSupplier',
        summary: 'Update a supplier',
        description: 'Update supplier details and associated user account',
        tags: ['Suppliers']
    )]
    #[OA\Parameter(
        name: 'supplier_id',
        in: 'path',
        required: true,
        description: 'Supplier ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Supplier data',
        content: new OA\JsonContent(
            required: ['name', 'email'],
            properties: [
                new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'John Doe'),
                new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'john@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', nullable: true, example: 'newpassword123'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', nullable: true, example: 'newpassword123'),
                new OA\Property(property: 'notelp', type: 'string', maxLength: 20, nullable: true, example: '081234567890'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Supplier updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Supplier updated successfully.'),
                new OA\Property(
                    property: 'supplier',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'user_id', type: 'integer'),
                        new OA\Property(property: 'notelp', type: 'string', nullable: true),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'name', type: 'string'),
                                new OA\Property(property: 'email', type: 'string', format: 'email'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Supplier not found')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function update(Request $request, int $supplier_id): JsonResponse
    {
        $supplier = Supplier::where('id', $supplier_id)->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'notelp' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $supplier->user;
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => ! empty($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        $supplier->update([
            'user_id' => $user->id,
            'notelp' => $validated['notelp'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $supplier->update([
                'password' => Hash::make($validated['password']),
            ]);
        }



        return response()->json([
            'message' => 'Supplier updated successfully.',
            'supplier' => $supplier->load('user'),
        ]);
    }

    #[OA\Delete(
        path: '/api/suppliers/{supplier_id}',
        operationId: 'destroySupplier',
        summary: 'Delete a supplier',
        description: 'Delete a specific supplier and their associated user account',
        tags: ['Suppliers']
    )]
    #[OA\Parameter(
        name: 'supplier_id',
        in: 'path',
        required: true,
        description: 'Supplier ID',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Supplier deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Supplier deleted successfully.')
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Supplier not found'
    )]
    public function destroy(int $supplier_id): JsonResponse
    {
        $supplier = Supplier::where('id', $supplier_id)->first();

        $supplier->user->delete();
        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully.',
        ]);
    }
}
