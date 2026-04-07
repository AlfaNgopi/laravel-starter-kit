<?php

namespace App\Http\Controllers;
// 
/*
* @var \App\Models\User $user 

*/
/** @var \App\Models\User $auth()->user() */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;


use App\Exports\SuppliersExport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            $suppliers = Supplier::join('users', 'suppliers.user_id', '=', 'users.id')
                ->select([
                    'users.name',
                    'suppliers.notelp',
                    'suppliers.created_at',
                    'suppliers.id'
                ]);



            return DataTables::of($suppliers)
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where('users.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('notelp', function ($query, $keyword) {
                    $query->where('suppliers.notelp', 'like', "%{$keyword}%");
                })

                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i');
                })


                ->addColumn('actions', function ($supplier) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-suppliers')) {
                        $actions .= '<a href="' . route('suppliers.show', $supplier) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-suppliers')) {
                        $actions .= '<a href="' . route('suppliers.edit', $supplier) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-suppliers')) {
                        $actions .= '<form action="' . route('suppliers.destroy', $supplier) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->editColumn('created_at', function ($supplier) {
                    return $supplier->created_at->format('M d, Y');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    public function export()
    {
        return Excel::download(new SuppliersExport, 'suppliers-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('suppliers.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'confirmed', Password::defaults()],
            'notelp' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        //     /protected $fillable = [
        //     'user_id',
        //     'notelp',
        // ];

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'notelp' => $validated['notelp'],

        ]);



        return to_route('suppliers.index')->with('status', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        // $supplier->load('roles.permissions');

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        $user = User::orderBy('name')->get();
        $supplier->load('user');

        return view('suppliers.edit', compact('supplier', 'user'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
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



        return to_route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {

        $supplier->user->delete();
        $supplier->delete();



        return to_route('suppliers.index')->with('status', 'Supplier deleted successfully.');
    }
}
