<?php

namespace Database\Seeders;

use App\Models\SoalEssai;
use App\Models\PaketSoal;
use Illuminate\Database\Seeder;

class SoalEssaiSeeder extends Seeder
{
    public function run(): void
    {
        $paketSoal = PaketSoal::all();

        $pertanyaan = [
            'Jelaskan pengertian globalisasi dan pengaruhnya terhadap Indonesia!',
            'Apa yang dimaksud dengan demokrasi dan sebutkan contohnya!',
            'Jelaskan proses terjadinya hujan!',
            'Bagaimana cara melakukan fotosintesis pada tumbuhan?',
            'Apa yang dimaksud dengan ekonomi kerakyatan?',
            'Jelaskan pentingnya pendidikan dalam pembangunan bangsa!',
        ];

        foreach ($paketSoal as $paket) {
            for ($i = 0; $i < 3; $i++) {
                SoalEssai::create([
                    'paket_soal_id' => $paket->id,
                    'pertanyaan' => $pertanyaan[array_rand($pertanyaan)] . ' (Essay ' . ($i + 1) . ')',
                    'nilai' => rand(5, 10),
                ]);
            }
        }
    }
}
