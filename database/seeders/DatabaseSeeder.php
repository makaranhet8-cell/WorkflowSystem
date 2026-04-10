<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // ១. លុប Cache
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // ២. បង្កើត Permissions (ប្រើ firstOrCreate ការពារ Error ស្ទួនឈ្មោះ)
    $permissions = [
        'create posts',
        'edit posts',
        'delete posts',
        'view dashboard'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    // ៣. បង្កើត Role
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $userRole  = Role::firstOrCreate(['name' => 'user']);

    $adminRole->syncPermissions(Permission::all());
    $userRole->syncPermissions(['view dashboard', 'create posts']);


    $admin = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),

        ]
    );
    $admin->assignRole($adminRole);

    $user = User::updateOrCreate(
        ['email' => 'user@example.com'],
        [
            'name' => 'User',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]
    );
    $user->assignRole($userRole);
}
}
