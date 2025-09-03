<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role jika belum ada
        $role = Role::firstOrCreate(['name' => 'superadmin']);

        // Buat user
        $user = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'admin',
            'password' => Hash::make('qwe')
        ]);

        // Assign role
        $user->assignRole($role);
    }
}
