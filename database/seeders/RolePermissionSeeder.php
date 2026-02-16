<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'view_users', 'group' => 'user_management'],
            ['name' => 'create_users', 'group' => 'user_management'],
            ['name' => 'edit_users', 'group' => 'user_management'],
            ['name' => 'delete_users', 'group' => 'user_management'],
            ['name' => 'activate_users', 'group' => 'user_management'],

            // Academic Management
            ['name' => 'manage_grades', 'group' => 'academic'],
            ['name' => 'manage_subjects', 'group' => 'academic'],
            ['name' => 'manage_timetable', 'group' => 'academic'],
            ['name' => 'manage_attendance', 'group' => 'academic'],
            ['name' => 'manage_exams', 'group' => 'academic'],
            ['name' => 'manage_results', 'group' => 'academic'],

            // Financial Management
            ['name' => 'manage_fees', 'group' => 'financial'],
            ['name' => 'view_reports', 'group' => 'financial'],
            ['name' => 'process_payments', 'group' => 'financial'],

            // Hostel Management
            ['name' => 'manage_hostels', 'group' => 'hostel'],
            ['name' => 'allocate_rooms', 'group' => 'hostel'],
            ['name' => 'manage_hostel_staff', 'group' => 'hostel'],
            ['name' => 'view_hostel_reports', 'group' => 'hostel'],

            // Library Management
            ['name' => 'manage_books', 'group' => 'library'],
            ['name' => 'issue_books', 'group' => 'library'],
            ['name' => 'manage_members', 'group' => 'library'],

            // System
            ['name' => 'manage_settings', 'group' => 'system'],
            ['name' => 'view_audit_logs', 'group' => 'system'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['group' => $perm['group']]
            );
        }

        // Create Roles with Hierarchy
        $roles = [
            [
                'name' => 'superadmin',
                'hierarchy_level' => 1,
                'permissions' => ['all']
            ],
            [
                'name' => 'admin',
                'hierarchy_level' => 2,
                'permissions' => ['view_users', 'create_users', 'edit_users', 'manage_grades']
            ],
            [
                'name' => 'teacher',
                'hierarchy_level' => 3,
                'permissions' => ['manage_attendance', 'manage_exams', 'manage_results']
            ],
            [
                'name' => 'student',
                'hierarchy_level' => 10,
                'permissions' => ['view_attendance', 'view_results']
            ],
            [
                'name' => 'hostel_manager',
                'hierarchy_level' => 4,
                'permissions' => ['manage_hostels', 'allocate_rooms', 'view_hostel_reports']
            ],
            [
                'name' => 'accountant',
                'hierarchy_level' => 5,
                'permissions' => ['manage_fees', 'process_payments', 'view_reports']
            ],
            [
                'name' => 'librarian',
                'hierarchy_level' => 6,
                'permissions' => ['manage_books', 'issue_books', 'manage_members']
            ],
            [
                'name' => 'parent',
                'hierarchy_level' => 11,
                'permissions' => ['view_child_attendance', 'view_child_results', 'pay_fees']
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['hierarchy_level' => $roleData['hierarchy_level']]
            );

            // Assign all permissions to superadmin using sync (avoids duplicates)
            if ($roleData['name'] === 'superadmin') {
                $role->permissions()->sync(Permission::all()->pluck('id'));
            }
        }

        // Create Super Admin User – use firstOrCreate to avoid duplicates
        \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@school.edu'],
            [
                'username' => 'superadmin',
                'password' => bcrypt('Admin@123'),
                'role_id' => Role::where('name', 'superadmin')->first()->id,
            ]
        );
    }
}
