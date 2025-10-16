<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LembagaPelatihan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Eager load relasi: 'roles' dan relasi lembaga (nama relasi: 'lembaga')
            $data = User::with('roles', 'lembaga')->select('users.*');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('roles', fn($user) => $user->roles->pluck('name')->implode(', '))
                // Gunakan nama relasi 'lembaga' dan akses properti 'name'
                ->addColumn('lembaga', fn($user) => $user->lembaga->name ?? '-')
                ->addColumn('id_number', fn($user) => $user->id_number ?? '-')
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . route('admin.users.edit', $row->id) . '" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>';
                    $deleteBtn =
                        '<form action="' .
                        route('admin.users.destroy', $row->id) .
                        '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Anda yakin ingin menghapus user ini?\');">
                            ' .
                        csrf_field() .
                        '
                            ' .
                        method_field('DELETE') .
                        '
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
        $lembagas = LembagaPelatihan::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'lembagas'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'roles' => 'required|array',
            'id_number' => 'required|string|max:255|unique:users,id_number',
            // Nama kolom input/request harus 'company' karena kolom DB adalah 'company'
            'company' => 'nullable|exists:lembaga_pelatihan,id',
        ];

        // Tambahkan aturan 'required' jika role 'lembagaPelatihan' dipilih.
        if (in_array('lembagaPelatihan', $request->input('roles', []))) {
            $rules['company'] = 'required|exists:lembaga_pelatihan,id';
        }

        $validated = $request->validate($rules);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'id_number' => $validated['id_number'],
                // Pastikan menggunakan nama kolom DB yang benar: 'company'
                'company' => $validated['company'] ?? null,
            ]);

            $user->syncRoles($validated['roles']);

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('User Store Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name')->all();
        $lembagas = LembagaPelatihan::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles', 'lembagas'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'id_number' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => 'required|array',
            'password' => 'nullable|string|min:8|confirmed',
            // Nama kolom input/request harus 'company' karena kolom DB adalah 'company'
            'company' => 'nullable|exists:lembaga_pelatihan,id',
        ];

        if (in_array('lembagaPelatihan', $request->input('roles', []))) {
            $rules['company'] = 'required|exists:lembaga_pelatihan,id';
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->id_number = $validated['id_number'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']); // Gunakan Hash::make() alih-alih bcrypt()
        }

        // Pastikan menggunakan nama kolom DB yang benar: 'company'
        $user->company = $validated['company'] ?? null;
        $user->save();

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return back()->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('User Destroy Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete user.');
        }
    }

    public function getUsersByRole(Request $request)
    {
        try {
            // Menerapkan prinsip DRY (Don't Repeat Yourself) untuk menghindari duplikasi kode.
            $rolesToFetch = ['verifikator', 'observer', 'asesor'];
            $usersByRole = [];

            foreach ($rolesToFetch as $role) {
                $users = User::role($role)->select('id', 'name', 'email', 'id_number')->where('status', 'active')->orderBy('name', 'asc')->get()->map(
                    fn($user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'id_number' => $user->id_number ?? 'N/A',
                    ],
                );
                $usersByRole[$role] = $users;
            }

            return response()->json([
                'success' => true,
                ...$usersByRole, // Spread operator untuk memasukkan hasil loop ke response
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users by roles: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Gagal memuat data pengguna',
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function getUserDetail($userId)
    {
        try {
            $user = User::select('id', 'name', 'email', 'id_number')->where('id', $userId)->where('status', 'active')->firstOrFail();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id_number' => $user->id_number ?? 'N/A',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user detail: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Pengguna tidak ditemukan',
                ],
                404,
            );
        }
    }
}
