<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tkj = Jurusan::where('kode_jurusan', 'TKJ')->first()?->id;
        $rpl = Jurusan::where('kode_jurusan', 'RPL')->first()?->id;
        $ipa = Jurusan::where('kode_jurusan', 'IPA')->first()?->id;
        $ips = Jurusan::where('kode_jurusan', 'IPS')->first()?->id;

        $kelas = [
            ['kode_kelas' => '10A', 'kelas' => 'X A', 'jurusan_id' => $tkj],
            ['kode_kelas' => '10B', 'kelas' => 'X B', 'jurusan_id' => $rpl],
            ['kode_kelas' => '10C', 'kelas' => 'X C', 'jurusan_id' => $ipa],
            ['kode_kelas' => '10D', 'kelas' => 'X D', 'jurusan_id' => $ips],
            ['kode_kelas' => '11A', 'kelas' => 'XI A', 'jurusan_id' => $tkj],
            ['kode_kelas' => '11B', 'kelas' => 'XI B', 'jurusan_id' => $ipa],
            ['kode_kelas' => '12A', 'kelas' => 'XII A', 'jurusan_id' => $tkj],
            ['kode_kelas' => '12B', 'kelas' => 'XII B', 'jurusan_id' => $ipa],
        ];

        foreach ($kelas as $data) {
            Kelas::create($data);
        }
    }
}
