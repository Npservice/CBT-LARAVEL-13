<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $guruRole        = Role::where('nama_role', 'guru')->first();
        $guruPembuatRole = Role::where('nama_role', 'guru-pembuat-soal')->first();

        $data = [
            // Guru biasa
            [
                'user' => [
                    'name'     => 'Budi Santoso',
                    'username' => 'budi.santoso',
                    'email'    => 'budi@vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'guru',
                    'role_id'  => $guruRole?->id,
                ],
                'guru' => [
                    'nig'           => 'GUR001',
                    'nama'          => 'Budi Santoso',
                    'jenis_kelamin' => 'L',
                    'email'         => 'budi@vexta.sch.id',
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Siti Nurhaliza',
                    'username' => 'siti.nurhaliza',
                    'email'    => 'siti@vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'guru',
                    'role_id'  => $guruRole?->id,
                ],
                'guru' => [
                    'nig'           => 'GUR002',
                    'nama'          => 'Siti Nurhaliza',
                    'jenis_kelamin' => 'P',
                    'email'         => 'siti@vexta.sch.id',
                    'is_aktif'      => true,
                ],
            ],

            // Guru pembuat soal
            [
                'user' => [
                    'name'     => 'Ahmad Wijaya',
                    'username' => 'ahmad.wijaya',
                    'email'    => 'ahmad@vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'guru-pembuat-soal',
                    'role_id'  => $guruPembuatRole?->id,
                ],
                'guru' => [
                    'nig'           => 'GUR003',
                    'nama'          => 'Ahmad Wijaya',
                    'jenis_kelamin' => 'L',
                    'email'         => 'ahmad@vexta.sch.id',
                    'is_aktif'      => true,
                ],
            ],
            [
                'user' => [
                    'name'     => 'Dewi Lestari',
                    'username' => 'dewi.lestari',
                    'email'    => 'dewi@vexta.sch.id',
                    'password' => Hash::make('password'),
                    'role'     => 'guru-pembuat-soal',
                    'role_id'  => $guruPembuatRole?->id,
                ],
                'guru' => [
                    'nig'           => 'GUR004',
                    'nama'          => 'Dewi Lestari',
                    'jenis_kelamin' => 'P',
                    'email'         => 'dewi@vexta.sch.id',
                    'is_aktif'      => true,
                ],
            ],
        ];

        foreach ($data as $item) {
            $user = User::firstOrCreate(
                ['username' => $item['user']['username']],
                $item['user']
            );

            Guru::firstOrCreate(
                ['nig' => $item['guru']['nig']],
                array_merge($item['guru'], ['user_id' => $user->id])
            );
        }
    }
}
