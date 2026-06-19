<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Roles
        $adminRole = Role::create([
            'nama_role' => 'admin',
            'display_role' => 'Administrator',
        ]);

        $guruRole = Role::create([
            'nama_role' => 'guru',
            'display_role' => 'Teacher',
        ]);

        $siswaRole = Role::create([
            'nama_role' => 'siswa',
            'display_role' => 'Student',
        ]);

        // Buat Permissions
        $permissions = [
            // User Management
            ['nama_permission' => 'view-users', 'group_permission' => 'user-management', 'display_permission' => 'View Users'],
            ['nama_permission' => 'create-users', 'group_permission' => 'user-management', 'display_permission' => 'Create Users'],
            ['nama_permission' => 'edit-users', 'group_permission' => 'user-management', 'display_permission' => 'Edit Users'],
            ['nama_permission' => 'delete-users', 'group_permission' => 'user-management', 'display_permission' => 'Delete Users'],

            // Exam Management
            ['nama_permission' => 'view-exams', 'group_permission' => 'exam-management', 'display_permission' => 'View Exams'],
            ['nama_permission' => 'create-exams', 'group_permission' => 'exam-management', 'display_permission' => 'Create Exams'],
            ['nama_permission' => 'edit-exams', 'group_permission' => 'exam-management', 'display_permission' => 'Edit Exams'],
            ['nama_permission' => 'delete-exams', 'group_permission' => 'exam-management', 'display_permission' => 'Delete Exams'],

            // Question Management
            ['nama_permission' => 'view-questions', 'group_permission' => 'question-management', 'display_permission' => 'View Questions'],
            ['nama_permission' => 'create-questions', 'group_permission' => 'question-management', 'display_permission' => 'Create Questions'],
            ['nama_permission' => 'edit-questions', 'group_permission' => 'question-management', 'display_permission' => 'Edit Questions'],
            ['nama_permission' => 'delete-questions', 'group_permission' => 'question-management', 'display_permission' => 'Delete Questions'],

            // Results & Grading
            ['nama_permission' => 'view-results', 'group_permission' => 'results', 'display_permission' => 'View Results'],
            ['nama_permission' => 'grade-exams', 'group_permission' => 'results', 'display_permission' => 'Grade Exams'],

            // Exam Taking
            ['nama_permission' => 'take-exams', 'group_permission' => 'exam-taking', 'display_permission' => 'Take Exams'],
            ['nama_permission' => 'submit-exams', 'group_permission' => 'exam-taking', 'display_permission' => 'Submit Exams'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Get all permissions
        $allPermissions = Permission::all();
        $userMgmt = Permission::whereIn('nama_permission', [
            'view-users', 'create-users', 'edit-users', 'delete-users'
        ])->get();
        $examMgmt = Permission::whereIn('nama_permission', [
            'view-exams', 'create-exams', 'edit-exams', 'delete-exams'
        ])->get();
        $questionMgmt = Permission::whereIn('nama_permission', [
            'view-questions', 'create-questions', 'edit-questions', 'delete-questions'
        ])->get();
        $results = Permission::whereIn('nama_permission', [
            'view-results', 'grade-exams'
        ])->get();
        $examTaking = Permission::whereIn('nama_permission', [
            'take-exams', 'submit-exams'
        ])->get();

        // Admin has ALL permissions (tidak perlu attach, karena di trait sudah auto-allow)
        // Admin role doesn't need explicit permission assignment

        // Guru permissions
        $guruRole->permissions()->attach([
            ...$examMgmt->pluck('id')->toArray(),
            ...$questionMgmt->pluck('id')->toArray(),
            ...$results->pluck('id')->toArray(),
        ]);

        // Siswa permissions
        $siswaRole->permissions()->attach([
            ...$examTaking->pluck('id')->toArray(),
        ]);
    }
}
