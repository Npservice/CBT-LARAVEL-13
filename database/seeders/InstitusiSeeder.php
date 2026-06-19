<?php

namespace Database\Seeders;

use App\Models\Institusi;
use Illuminate\Database\Seeder;

class InstitusiSeeder extends Seeder
{
    public function run(): void
    {
        Institusi::create([
            'nama_sekolah' => 'SMA Negeri 1',
            'akreditasi_sekolah' => 'A',
            'tingkat' => 'SMA',
        ]);
    }
}
