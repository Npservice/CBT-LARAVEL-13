<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\GuruPengampu;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class GuruPengampuSeeder extends Seeder
{
    public function run(): void
    {
        // [nig_guru => [[kode_mapel_prefix, kode_kelas, pembuat_soal], ...]]
        $assignments = [
            'GUR001' => [['MTK', '10A', false], ['MTK', '11A', false]],
            'GUR002' => [['B.IND', '10A', false], ['B.IND', '11A', false]],
            'GUR003' => [['FIS', '10A', true], ['FIS', '11A', true]],
            'GUR004' => [['KIM', '10A', true], ['KIM', '11A', true]],
        ];

        foreach ($assignments as $nig => $mapelList) {
            $guru = Guru::where('nig', $nig)->first();
            if (!$guru) continue;

            foreach ($mapelList as [$mapelPrefix, $kodeKelas, $pembuatSoal]) {
                $kelas = Kelas::where('kode_kelas', $kodeKelas)->first();
                if (!$kelas) continue;

                $mapel = MataPelajaran::where('kode_mapel', 'like', $mapelPrefix . '-%')
                    ->whereHas('kelas', fn($q) => $q->where('id', $kelas->id))
                    ->first();
                if (!$mapel) continue;

                // Key: (mata_pelajaran_id, kelas_id) — satu slot per kelas per mapel
                GuruPengampu::firstOrCreate(
                    ['mata_pelajaran_id' => $mapel->id, 'kelas_id' => $kelas->id],
                    ['guru_id' => $guru->id, 'pembuat_soal' => $pembuatSoal]
                );
            }
        }
    }
}
