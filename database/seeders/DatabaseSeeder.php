<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'user'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => config('auth.defaults.guard', 'web'),
            ]);
        }

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'nim' => '000000000001',
                'kelas' => 'ADM-01',
                'no_hp' => '081200000001',
            ],
        );
        $superAdmin->syncRoles(['super_admin']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nim' => '000000000002',
                'kelas' => 'ADM-02',
                'no_hp' => '081200000002',
            ],
        );
        $admin->syncRoles(['admin']);

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Demo',
                'password' => Hash::make('password'),
                'role' => 'user',
                'nim' => '241011400256',
                'kelas' => '03TPLP006',
                'no_hp' => '081234567890',
            ],
        );
        $user->syncRoles(['user']);
    }
}
