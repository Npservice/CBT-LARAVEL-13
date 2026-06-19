<?php

namespace Database\Seeders;

use App\Models\SoalPilihanGanda;
use App\Models\PaketSoal;
use Illuminate\Database\Seeder;

class SoalPilihanGandaSeeder extends Seeder
{
    public function run(): void
    {
        $paketSoal = PaketSoal::all();

        $pertanyaan = [
            'Berapa hasil dari 2 + 2?',
            'Apa ibukota Indonesia?',
            'Siapa presiden Indonesia pertama?',
            'Berapa tahun Indonesia merdeka?',
            'Apa itu fotosintesis?',
            'Berapa jumlah provinsi di Indonesia?',
            'Siapa penemu lampu pijar?',
            'Apa warna bendera Indonesia?',
        ];

        foreach ($paketSoal as $paket) {
            for ($i = 0; $i < 5; $i++) {
                SoalPilihanGanda::create([
                    'paket_soal_id' => $paket->id,
                    'pertanyaan' => $pertanyaan[array_rand($pertanyaan)] . ' (Soal ' . ($i + 1) . ')',
                    'nilai' => rand(1, 5),
                ]);
            }
        }
    }
}
