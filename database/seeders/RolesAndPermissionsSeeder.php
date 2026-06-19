<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin       = Role::firstOrCreate(['nama_role' => 'admin'],            ['display_role' => 'Administrator']);
        $guru        = Role::firstOrCreate(['nama_role' => 'guru'],             ['display_role' => 'Guru']);
        $guruPembuat = Role::firstOrCreate(['nama_role' => 'guru-pembuat-soal'],['display_role' => 'Guru Pembuat Soal']);
        $siswa       = Role::firstOrCreate(['nama_role' => 'siswa'],            ['display_role' => 'Siswa']);

        // Permissions: [nama, group, display]
        $permissionsData = [
            // User & Role Management (admin only)
            ['manage-users',              'user-management',          'Kelola User'],
            ['manage-roles',              'role-management',          'Kelola Role & Permission'],

            // Siswa
            ['view-siswa',                'siswa-management',         'Lihat Siswa'],
            ['create-siswa',              'siswa-management',         'Tambah Siswa'],
            ['edit-siswa',                'siswa-management',         'Edit Siswa'],
            ['delete-siswa',              'siswa-management',         'Hapus Siswa'],

            // Guru
            ['view-guru',                 'guru-management',          'Lihat Guru'],
            ['create-guru',               'guru-management',          'Tambah Guru'],
            ['edit-guru',                 'guru-management',          'Edit Guru'],
            ['delete-guru',               'guru-management',          'Hapus Guru'],

            // Guru Pengampu
            ['view-guru-pengampu',        'guru-pengampu-management', 'Lihat Guru Pengampu'],
            ['create-guru-pengampu',      'guru-pengampu-management', 'Tambah Guru Pengampu'],
            ['edit-guru-pengampu',        'guru-pengampu-management', 'Edit Guru Pengampu'],
            ['delete-guru-pengampu',      'guru-pengampu-management', 'Hapus Guru Pengampu'],

            // Institusi
            ['view-institusi',            'master-data',              'Lihat Institusi'],
            ['create-institusi',          'master-data',              'Tambah Institusi'],
            ['edit-institusi',            'master-data',              'Edit Institusi'],
            ['delete-institusi',          'master-data',              'Hapus Institusi'],

            // Kelas
            ['view-kelas',                'master-data',              'Lihat Kelas'],
            ['create-kelas',              'master-data',              'Tambah Kelas'],
            ['edit-kelas',                'master-data',              'Edit Kelas'],
            ['delete-kelas',              'master-data',              'Hapus Kelas'],

            // Jurusan
            ['view-jurusan',              'master-data',              'Lihat Jurusan'],
            ['create-jurusan',            'master-data',              'Tambah Jurusan'],
            ['edit-jurusan',              'master-data',              'Edit Jurusan'],
            ['delete-jurusan',            'master-data',              'Hapus Jurusan'],

            // Mata Pelajaran
            ['view-mata-pelajaran',       'master-data',              'Lihat Mata Pelajaran'],
            ['create-mata-pelajaran',     'master-data',              'Tambah Mata Pelajaran'],
            ['edit-mata-pelajaran',       'master-data',              'Edit Mata Pelajaran'],
            ['delete-mata-pelajaran',     'master-data',              'Hapus Mata Pelajaran'],

            // Paket Soal
            ['view-paket-soal',           'paket-soal',               'Lihat Paket Soal'],
            ['create-paket-soal',         'paket-soal',               'Tambah Paket Soal'],
            ['edit-paket-soal',           'paket-soal',               'Edit Paket Soal'],
            ['delete-paket-soal',         'paket-soal',               'Hapus Paket Soal'],

            // Soal
            ['view-soal',                 'soal',                     'Lihat Soal'],
            ['create-soal',               'soal',                     'Tambah Soal'],
            ['edit-soal',                 'soal',                     'Edit Soal'],
            ['delete-soal',               'soal',                     'Hapus Soal'],

            // Sesi Ujian
            ['view-sesi-ujian',           'sesi-ujian',               'Lihat Sesi Ujian'],
            ['create-sesi-ujian',         'sesi-ujian',               'Tambah Sesi Ujian'],
            ['edit-sesi-ujian',           'sesi-ujian',               'Edit Sesi Ujian'],
            ['delete-sesi-ujian',         'sesi-ujian',               'Hapus Sesi Ujian'],

            // Hasil
            ['view-hasil',                'hasil',                    'Lihat Hasil Ujian'],
            ['nilai-essai',               'hasil',                    'Beri Nilai Essai'],
        ];

        foreach ($permissionsData as [$nama, $group, $display]) {
            Permission::firstOrCreate(
                ['nama_permission' => $nama],
                ['group_permission' => $group, 'display_permission' => $display]
            );
        }

        // Admin: all permissions (also bypassed via role check in code)
        $admin->permissions()->sync(Permission::pluck('id')->toArray());

        // Guru: lihat guru pengampu sendiri, CRUD sesi ujian, hasil (tidak bisa akses paket/soal)
        $guruPerms = Permission::whereIn('nama_permission', [
            'view-guru-pengampu',
            'view-sesi-ujian', 'create-sesi-ujian', 'edit-sesi-ujian', 'delete-sesi-ujian',
            'view-hasil', 'nilai-essai',
        ])->pluck('id')->toArray();
        $guru->permissions()->sync($guruPerms);

        // Guru Pembuat Soal: semua guru + CRUD paket soal + CRUD soal
        $guruPembuatPerms = Permission::whereIn('nama_permission', [
            'view-guru-pengampu',
            'view-paket-soal', 'create-paket-soal', 'edit-paket-soal', 'delete-paket-soal',
            'view-soal',       'create-soal',        'edit-soal',       'delete-soal',
            'view-sesi-ujian', 'create-sesi-ujian',  'edit-sesi-ujian', 'delete-sesi-ujian',
            'view-hasil', 'nilai-essai',
        ])->pluck('id')->toArray();
        $guruPembuat->permissions()->sync($guruPembuatPerms);

        // Siswa: no permissions (role-based only)
        $siswa->permissions()->sync([]);

        echo "Roles and Permissions seeded successfully!\n";
    }
}
