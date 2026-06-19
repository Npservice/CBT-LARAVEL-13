<?php

namespace Database\Seeders;

use App\Models\Pilihan;
use App\Models\SoalPilihanGanda;
use Illuminate\Database\Seeder;

class PilihanSeeder extends Seeder
{
    public function run(): void
    {
        $soal = SoalPilihanGanda::all();

        $options = [
            ['A', 'B', 'C', 'D', 'E'],
        ];

        $answers = [
            [
                ['pilihan' => '1', 'benar' => true],
                ['pilihan' => '2', 'benar' => false],
                ['pilihan' => '3', 'benar' => false],
                ['pilihan' => '4', 'benar' => false],
            ],
            [
                ['pilihan' => 'Jakarta', 'benar' => true],
                ['pilihan' => 'Bandung', 'benar' => false],
                ['pilihan' => 'Surabaya', 'benar' => false],
                ['pilihan' => 'Medan', 'benar' => false],
            ],
            [
                ['pilihan' => 'Soekarno', 'benar' => true],
                ['pilihan' => 'Soeharto', 'benar' => false],
                ['pilihan' => 'Habibie', 'benar' => false],
                ['pilihan' => 'Wahid', 'benar' => false],
            ],
            [
                ['pilihan' => '1945', 'benar' => true],
                ['pilihan' => '1944', 'benar' => false],
                ['pilihan' => '1946', 'benar' => false],
                ['pilihan' => '1947', 'benar' => false],
            ],
        ];

        foreach ($soal as $s) {
            $answer = $answers[array_rand($answers)];
            foreach ($answer as $idx => $data) {
                Pilihan::create([
                    'soal_pilihan_ganda_id' => $s->id,
                    'pilihan' => $data['pilihan'],
                    'benar' => $data['benar'],
                ]);
            }
        }
    }
}
