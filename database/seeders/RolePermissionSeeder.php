<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Permissions from Config
        $permissionsConfig = config('Permission');
        $allPermissionIds = [];

        foreach ($permissionsConfig as $group => $items) {
            foreach ($items as $name => $slug) {
                $permission = Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => Str::headline($name) . ' ' . Str::headline($group),
                        'group' => $group
                    ]
                );
                $allPermissionIds[] = $permission->id;
            }
        }

        // 2. Create Super Admin Role
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'is_active' => true,
            ]
        );

        // Attach ALL permissions to Super Admin
        $superAdminRole->permissions()->sync($allPermissionIds);

        // 3. Create Staff Role (Contoh role dengan akses terbatas)
        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'is_active' => true,
            ]
        );

        // Hanya beri akses VIEW
        $viewPermissions = Permission::where('slug', 'like', '%.view')->pluck('id');
        $staffRole->permissions()->sync($viewPermissions);

        // 4. Create Default Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        // Attach Super Admin Role to Admin User (Many-to-Many)
        $adminUser->roles()->sync([$superAdminRole->id]);

        // 5. Create Default Staff User
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Regular Staff',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $staffUser->roles()->sync([$staffRole->id]);

        $this->command->info('Default roles, permissions, and users seeded successfully!');
        $this->command->info('Admin: admin@test.com | password123');
        $this->command->info('Staff: staff@test.com | password123');
    }
}
