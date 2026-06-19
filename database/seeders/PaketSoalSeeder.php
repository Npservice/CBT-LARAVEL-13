<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruPengampu;
use App\Models\PaketSoal;
use Illuminate\Database\Seeder;

class PaketSoalSeeder extends Seeder
{
    public function run(): void
    {
        $kelas       = Kelas::with('jurusan')->get();
        $mapel       = MataPelajaran::with('kelas')->get();
        $guruPengampu = GuruPengampu::with(['guru', 'mataPelajaran.kelas'])->get();

        if ($kelas->isEmpty() || $mapel->isEmpty() || $guruPengampu->isEmpty()) {
            $this->command->warn('PaketSoalSeeder: data referensi belum tersedia, skip.');
            return;
        }

        $paket = [
            ['kode_soal' => 'PKT-MTK-001', 'durasi_menit' => 90],
            ['kode_soal' => 'PKT-MTK-002', 'durasi_menit' => 90],
            ['kode_soal' => 'PKT-IND-001', 'durasi_menit' => 80],
            ['kode_soal' => 'PKT-ING-001', 'durasi_menit' => 80],
            ['kode_soal' => 'PKT-FIS-001', 'durasi_menit' => 100],
            ['kode_soal' => 'PKT-KIM-001', 'durasi_menit' => 100],
        ];

        foreach ($paket as $data) {
            $gp         = $guruPengampu->random();
            $mapelItem  = $gp->mataPelajaran;
            $kelasItem  = $mapelItem?->kelas->first();

            PaketSoal::firstOrCreate(
                ['kode_soal' => $data['kode_soal']],
                [
                    'kelas_id'       => $kelasItem?->id,
                    'kode_kelas'     => $kelasItem?->kode_kelas,
                    'mapel_id'       => $mapelItem?->id,
                    'mata_pelajaran' => $mapelItem?->mapel,
                    'created_by'     => $gp->id,
                    'dibuat_oleh'    => $gp->guru?->nama,
                    'durasi_menit'   => $data['durasi_menit'],
                ]
            );
        }
    }
}
