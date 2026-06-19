<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::all();

        $mapel = [
            ['kode_mapel' => 'MTK',   'mapel' => 'Matematika'],
            ['kode_mapel' => 'B.IND', 'mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'B.ING', 'mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'FIS',   'mapel' => 'Fisika'],
            ['kode_mapel' => 'KIM',   'mapel' => 'Kimia'],
            ['kode_mapel' => 'BIO',   'mapel' => 'Biologi'],
        ];

        foreach ($mapel as $data) {
            foreach ($kelas as $k) {
                $record = MataPelajaran::create([
                    'mapel'      => $data['mapel'],
                    'kode_mapel' => $data['kode_mapel'] . '-' . $k->kode_kelas,
                ]);
                $record->kelas()->sync([$k->id]);
            }
        }
    }
}
