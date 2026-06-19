<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruPengampu;
use App\Models\JawabanSiswa;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats()
    {
        $aktivitas = Cache::remember('dashboard_aktivitas', 300, function () {
            $today = now()->toDateString();

            $sesiAktifIds = SesiUjian::whereDate('start', $today)->pluck('id');
            $masihMengerjakan = JawabanSiswa::whereIn('sesi_ujian_id', $sesiAktifIds)->whereNull('end')->count();
            $sudahSelesai = JawabanSiswa::whereIn('sesi_ujian_id', $sesiAktifIds)->whereNotNull('end')->count();

            return [
                'sesi_aktif'        => $sesiAktifIds->count(),
                'siswa_login'       => $masihMengerjakan + $sudahSelesai,
                'masih_mengerjakan' => $masihMengerjakan,
                'sudah_selesai'     => $sudahSelesai,
                'cached_at'         => now()->format('H:i:s'),
            ];
        });

        $master = [
            'total_guru'         => Guru::count(),
            'total_siswa'        => Siswa::count(),
            'total_mapel'        => MataPelajaran::count(),
            'total_pembuat_soal' => GuruPengampu::where('pembuat_soal', true)->distinct('guru_id')->count('guru_id'),
        ];

        return response()->json(['data' => array_merge($aktivitas, $master)]);
    }

    public function refresh()
    {
        Cache::forget('dashboard_aktivitas');
        return $this->stats();
    }
}
