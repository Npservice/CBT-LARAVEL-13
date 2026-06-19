<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            InstitusiSeeder::class,
            JurusanSeeder::class,
            KelasSeeder::class,
            MataPelajaranSeeder::class,
            UserSeeder::class,
            GuruSeeder::class,
            SiswaSeeder::class,
            GuruPengampuSeeder::class,
            PaketSoalSeeder::class,
            SesiUjianSeeder::class,
            SoalPilihanGandaSeeder::class,
            PilihanSeeder::class,
            SoalEssaiSeeder::class,
        ]);
    }
}
