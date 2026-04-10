<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // បោសសម្អាត Cache របស់ Spatie ការពារ Error រក Permission មិនឃើញ
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ១. បង្កើត Permissions (ប្រើ firstOrCreate ដើម្បីការពារ Error ពេលរត់ស្ទួន)
        $permissions = [
            'view requests',
            'approve requests',
            'manage roles',
            'view dashboard', // បន្ថែមសិទ្ធិសម្រាប់ Dashboard
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ២. បង្កើត Roles (រៀបចំជា Array ដើម្បីកុំឱ្យកូដវែងពេក)
        $roleNames = ['admin', 'user', 'approver', 'team_leader', 'hr_manager', 'ceo', 'cfo','admin_it','admin_sale'];
        $roles = [];

        foreach ($roleNames as $name) {
            $roles[$name] = Role::firstOrCreate(['name' => $name]);
        }

        $roles['admin']->syncPermissions(Permission::all());
        // $roles['admin']->syncPermissions(Permission::all());
        $roles['user']->syncPermissions(['view requests', 'view dashboard']);
        $roles['approver']->syncPermissions(['view requests', 'approve requests', 'view dashboard']);

        // ផ្តល់សិទ្ធិឱ្យ Role ផ្សេងៗទៀតតាមតម្រូវការ
        $roles['team_leader']->syncPermissions(['view requests', 'approve requests', 'view dashboard']);
        $roles['hr_manager']->syncPermissions(['view requests', 'approve requests', 'view dashboard']);
        $roles['ceo']->syncPermissions(['view requests', 'approve requests', 'view dashboard']);
        $roles['cfo']->syncPermissions(['view requests', 'approve requests', 'view dashboard']);

        // ៤. បង្កើត Users និង Assign Role (ប្រើ updateOrCreate ដើម្បី Update Password បើ User មានស្រាប់)

        $userData = [

            [
                'name'  => 'Admin',
                'email' => 'admin@example.com',
                'pass'  => '12345678',
                'role'  => 'admin'
            ],
            [
                'name'  => 'Admin_IT',
                'email' => 'adminit@example.com',
                'pass'  => '12345678',
                'role'  => 'admin_it'
            ],
            [
                'name'  => 'Admin_Sales',
                'email' => 'adminsales@example.com',
                'pass'  => '12345678',
                'role'  => 'admin_sale'
            ],
            [
                'name'  => 'CEO ',
                'email' => 'ceo@example.com',
                'pass'  => '123456',
                'role'  => 'ceo'
            ],
            [
                'name'  => 'CFO',
                'email' => 'cfo@example.com',
                'pass'  => '123456',
                'role'  => 'cfo'
            ],
            [
                'name'  => 'Team Leader',
                'email' => 'leader@example.com',
                'pass'  => '123456',
                'role'  => 'team_leader'
            ],
            [
                'name'  => 'HR Manager',
                'email' => 'hr@example.com',
                'pass'  => '123456',
                'role'  => 'hr_manager'
            ]
        ];

        foreach ($userData as $data) {
    $user = User::updateOrCreate(
        ['email' => $data['email']],
        [
            'name'              => $data['name'],
            'password'          => Hash::make($data['pass']),
            'email_verified_at' => now(),

        ]
    );


        $user->syncRoles([$data['role']]);
    }

    }
}
