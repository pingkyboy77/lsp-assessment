<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LembagaPelatihan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('roles', 'lembaga')->select('users.*');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('roles', fn($user) => $user->roles->pluck('name')->implode(', '))
                ->addColumn('lembaga', fn($user) => $user->lembaga ? (is_iterable($user->lembaga) ? $user->lembaga->pluck('name')->implode(', ') : $user->lembaga->name) : '')
                ->addColumn('id_number', fn($user) => $user->id_number ?? '-')
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . route('admin.users.edit', $row->id) . '" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>';
                    $deleteBtn =
                        '<form action="' .
                        route('admin.users.destroy', $row->id) .
                        '" method="POST" style="display:inline-block;">
                    ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this User?\')"><i class="bi bi-trash"></i></button>
                </form>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action']) 
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::pluck('name')->all();
        $lembagas = LembagaPelatihan::all();
        return view('admin.users.create', compact('roles', 'lembagas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'roles' => 'required|array',
            'company' => 'nullable|string|max:255',
            'id_number' => 'required|string|max:255|unique:users',
        ]);

        // Additional validation based on selected roles
        $roles = $request->roles ?? [];
        if (in_array('lembagaPelatihan', $roles)) {
            $request->validate(['company' => 'required|string|max:255']);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company' => in_array('lembagaPelatihan', $roles) ? $request->company : null,
                'id_number' => $request->id_number,
            ]);

            $user->syncRoles($request->roles);

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('User Store Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name');
        $lembagas = LembagaPelatihan::all();
        $userRole = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRole', 'lembagas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Get all available roles
        $roles = Role::pluck('name')->toArray();

        // Basic validation
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'roles.*' => 'in:' . implode(',', $roles),
            'password' => 'nullable|string|min:6|confirmed',
            'company' => 'nullable|string|max:255',
            'id_number' => 'required|string|max:255|unique:users',
        ];

        // Additional validation based on selected roles
        $selectedRoles = $request->roles ?? [];
        if (in_array('lembagaPelatihan', $selectedRoles)) {
            $rules['company'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Update data
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->id_number = $validated['id_number'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // Save company and no_met based on roles
        $user->company = in_array('lembagaPelatihan', $selectedRoles) ? $validated['company'] : null;

        $user->save();

        // Sync roles
        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('User Delete Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete user.']);
        }
    }
}