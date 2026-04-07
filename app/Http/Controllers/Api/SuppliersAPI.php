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

class SuppliersAPI extends Controller
{
    // CRUD API

    public function index(): JsonResponse
    {
        $suppliers = Supplier::with('user')->get();

        return response()->json($suppliers);
    }

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
