<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswaRole = Role::where('nama_role', 'siswa')->first();

        $kelas10A = Kelas::where('kode_kelas', '10A')->first() ?? Kelas::first();
        $kelas11A = Kelas::where('kode_kelas', '11A')->first() ?? Kelas::first();
        $kelas10B = Kelas::where('kode_kelas', '10B')->first() ?? Kelas::first();

        $data = [
            [
                'user' => [
                    'name'     => 'Andi Pratama',
                    'username' => 'andi.pratama',
                    'email'    => 'andi@student.vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'role_id'  => $siswaRole?->id,
                ],
                'siswa' => [
                    'nis'           => 'SIS001',
                    'nisn'          => '0012345601',
                    'nama'          => 'Andi Pratama',
                    'jenis_kelamin' => 'L',
                    'kelas_id'      => $kelas10A?->id,
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Binti Nurhasanah',
                    'username' => 'binti.nurhasanah',
                    'email'    => 'binti@student.vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'role_id'  => $siswaRole?->id,
                ],
                'siswa' => [
                    'nis'           => 'SIS002',
                    'nisn'          => '0012345602',
                    'nama'          => 'Binti Nurhasanah',
                    'jenis_kelamin' => 'P',
                    'kelas_id'      => $kelas10A?->id,
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Chandra Wijaya',
                    'username' => 'chandra.wijaya',
                    'email'    => 'chandra@student.vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'role_id'  => $siswaRole?->id,
                ],
                'siswa' => [
                    'nis'           => 'SIS003',
                    'nisn'          => '0012345603',
                    'nama'          => 'Chandra Wijaya',
                    'jenis_kelamin' => 'L',
                    'kelas_id'      => $kelas11A?->id,
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Diana Kusuma',
                    'username' => 'diana.kusuma',
                    'email'    => 'diana@student.vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'role_id'  => $siswaRole?->id,
                ],
                'siswa' => [
                    'nis'           => 'SIS004',
                    'nisn'          => '0012345604',
                    'nama'          => 'Diana Kusuma',
                    'jenis_kelamin' => 'P',
                    'kelas_id'      => $kelas10B?->id,
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Eka Putra',
                    'username' => 'eka.putra',
                    'email'    => 'eka@student.vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'role_id'  => $siswaRole?->id,
                ],
                'siswa' => [
                    'nis'           => 'SIS005',
                    'nisn'          => '0012345605',
                    'nama'          => 'Eka Putra',
                    'jenis_kelamin' => 'L',
                    'kelas_id'      => $kelas10B?->id,
                    'is_aktif'      => true,
                ],
            ],
        ];

        foreach ($data as $item) {
            $user = User::firstOrCreate(
                ['username' => $item['user']['username']],
                $item['user']
            );

            Siswa::firstOrCreate(
                ['nis' => $item['siswa']['nis']],
                array_merge($item['siswa'], ['user_id' => $user->id])
            );
        }
    }
}
