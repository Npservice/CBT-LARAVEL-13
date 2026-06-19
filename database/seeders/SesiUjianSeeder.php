<?php

namespace Database\Seeders;

use App\Models\PaketSoal;
use App\Models\SesiUjian;
use Illuminate\Database\Seeder;

class SesiUjianSeeder extends Seeder
{
    public function run(): void
    {
        $paketSoal = PaketSoal::all();

        foreach ($paketSoal as $index => $paket) {
            for ($sesi = 1; $sesi <= 2; $sesi++) {
                if ($sesi === 1 && $index < 3) {
                    $start = now()->subMinutes(10);
                    $end   = now()->addMinutes($paket->durasi_menit);
                } else {
                    $start = now()->addDays(rand(1, 7))->setHour(rand(8, 14))->setMinute(0);
                    $end   = $start->copy()->addMinutes($paket->durasi_menit);
                }

                SesiUjian::create([
                    'paket_soal_id' => $paket->id,
                    'judul'         => 'Ujian ' . $paket->mata_pelajaran . ' Sesi ' . $sesi,
                    'kelas_id'      => $paket->kelas_id,
                    'kode_kelas'    => $paket->kode_kelas,
                    'kode_paket'    => $paket->kode_soal . '-S' . $sesi,
                    'sesi'          => $sesi,
                    'start'         => $start,
                    'end'           => $end,
                ]);
            }
        }
    }
}
