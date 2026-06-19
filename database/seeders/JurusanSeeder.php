<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $jurusan = [
            ['nama_jurusan' => 'IPA', 'kode_jurusan' => 'IPA'],
            ['nama_jurusan' => 'IPS', 'kode_jurusan' => 'IPS'],
            ['nama_jurusan' => 'Bahasa', 'kode_jurusan' => 'BHS'],
            ['nama_jurusan' => 'RPL', 'kode_jurusan' => 'RPL'],
            ['nama_jurusan' => 'TKJ', 'kode_jurusan' => 'TKJ'],
        ];

        foreach ($jurusan as $data) {
            Jurusan::create($data);
        }
    }
}
