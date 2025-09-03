<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role-role utama
        $roles = ['superadmin', 'admin', 'asesor', 'asesi'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Buat user superadmin contoh
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Ganti password sesuai kebutuhan
            ],
        );

        $superadmin->assignRole('superadmin');

        // Buat user lain jika perlu
        $asesor = User::firstOrCreate(
            ['email' => 'asesor@example.com'],
            [
                'name' => 'Asesor Satu',
                'password' => bcrypt('password'),
            ],
        );
        $asesor->assignRole('asesor');

        $asesi = User::firstOrCreate(
            ['email' => 'asesi@example.com'],
            [
                'name' => 'Asesi Satu',
                'password' => bcrypt('password'),
            ],
        );
        $asesi->assignRole('asesi');
    }
}
