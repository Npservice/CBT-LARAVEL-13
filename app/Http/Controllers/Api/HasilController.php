<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuruPengampu;
use App\Models\JawabanSiswa;
use App\Models\JawabEssai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    private function getGuruMapelIds(): array
    {
        $user = auth()->user();
        if (!in_array($user->role, ['guru', 'guru-pembuat-soal'])) {
            return [];
        }
        return GuruPengampu::whereHas('guru', fn($q) => $q->where('user_id', $user->id))
            ->pluck('mata_pelajaran_id')
            ->toArray();
    }

    // Level 1: daftar kelas yang punya hasil, grouped by jurusan
    public function index()
    {
        $user = auth()->user();
        $isGuruRole = in_array($user->role, ['guru', 'guru-pembuat-soal']);
        $mapelIds = $isGuruRole ? $this->getGuruMapelIds() : [];

        // Kelas yang punya siswa dan punya jawaban selesai
        $kelasIds = Siswa::distinct()->pluck('kelas_id');

        $result = [];

        foreach ($kelasIds as $kelasId) {
            if (!$kelasId) continue;

            $kelas = Kelas::with('jurusan')->find($kelasId);
            if (!$kelas) continue;

            // Hitung siswa selesai: siswa di kelas ini yang punya jawaban end not null
            $selesaiQuery = JawabanSiswa::whereNotNull('end')
                ->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId));

            if ($isGuruRole) {
                $selesaiQuery->whereHas('sesiUjian.paketSoal', fn($q) => $q->whereIn('mapel_id', $mapelIds)
                );
            }

            $selesai = $selesaiQuery->distinct('siswa_id')->count('siswa_id');

            // Jika guru role dan kelas ini tidak ada hasilnya sama sekali, skip
            if ($isGuruRole && $selesai === 0) continue;

            // Total siswa terdaftar di kelas
            $total = Siswa::where('kelas_id', $kelasId)->count();

            if (!$isGuruRole && $selesai === 0) continue; // skip kelas tanpa hasil

            $result[] = [
                'kelas_id' => $kelas->id,
                'kelas' => $kelas->kelas,
                'kode_kelas' => $kelas->kode_kelas,
                'jurusan_id' => $kelas->jurusan_id,
                'jurusan' => $kelas->jurusan?->nama_jurusan,
                'selesai' => $selesai,
                'total' => $total,
            ];
        }

        // Sort by jurusan, lalu kelas
        usort($result, fn($a, $b) => strcmp($a['jurusan'] ?? '', $b['jurusan'] ?? '') ?: strcmp($a['kelas'] ?? '', $b['kelas'] ?? '')
        );

        return response()->json(['data' => $result]);
    }

    // Level 2: daftar mapel yang punya hasil di kelas ini
    public function mapel($kelasId)
    {
        $user = auth()->user();
        $isGuruRole = in_array($user->role, ['guru', 'guru-pembuat-soal']);
        $guruMapelIds = $isGuruRole ? $this->getGuruMapelIds() : [];

        $kelas = Kelas::with('jurusan')->findOrFail($kelasId);
        $totalSiswa = Siswa::where('kelas_id', $kelasId)->count();

        // Ambil distinct mapel_id dari jawaban selesai milik siswa di kelas ini
        $jawabSelesai = JawabanSiswa::whereNotNull('end')
            ->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId))
            ->with([
                'sesiUjian:id,paket_soal_id',
                'sesiUjian.paketSoal:id,mapel_id',
            ])
            ->get(['id', 'sesi_ujian_id']);

        $mapelIds = $jawabSelesai->pluck('sesiUjian.paketSoal.mapel_id')->unique()->filter();

        if ($isGuruRole && !empty($guruMapelIds)) {
            $mapelIds = $mapelIds->intersect($guruMapelIds);
        }

        $mapelList = MataPelajaran::whereIn('id', $mapelIds)->orderBy('mapel')->get();

        $data = $mapelList->map(function ($mapel) use ($kelasId, $totalSiswa) {
            $selesai = JawabanSiswa::whereNotNull('end')
                ->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId))
                ->whereHas('sesiUjian.paketSoal', fn($q) => $q->where('mapel_id', $mapel->id))
                ->distinct('siswa_id')
                ->count('siswa_id');

            return [
                'mapel_id' => $mapel->id,
                'mapel' => $mapel->mapel,
                'kode_mapel' => $mapel->kode_mapel,
                'selesai' => $selesai,
                'total' => $totalSiswa,
            ];
        });

        return response()->json([
            'kelas' => $kelas->kelas,
            'kode_kelas' => $kelas->kode_kelas,
            'jurusan' => $kelas->jurusan?->nama_jurusan,
            'data' => $data->values(),
        ]);
    }

    // Level 3: jawaban siswa di kelas ini untuk mapel tertentu
    public function siswa($kelasId, $mapelId)
    {
        $user = auth()->user();
        $isGuruRole = in_array($user->role, ['guru', 'guru-pembuat-soal']);
        $mapelIds = $isGuruRole ? $this->getGuruMapelIds() : [];

        $kelas = Kelas::with('jurusan')->findOrFail($kelasId);
        $mapel = MataPelajaran::findOrFail($mapelId);

        $query = JawabanSiswa::with(['siswa', 'sesiUjian', 'jawabPilihanGanda', 'jawabEssai'])
            ->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId))
            ->whereHas('sesiUjian.paketSoal', fn($q) => $q->where('mapel_id', $mapelId));

        if ($isGuruRole) {
            $query->whereHas('sesiUjian.paketSoal', fn($q) => $q->whereIn('mapel_id', $mapelIds)
            );
        }

        $jawaban = $query->whereNotNull('end')->orderBy('nama_siswa')->get();

        $data = $jawaban->map(fn($j) => [
            'jawaban_id' => $j->id,
            'nama_siswa' => $j->nama_siswa,
            'nis' => $j->siswa?->nis,
            'judul_sesi' => $j->sesiUjian?->judul,
            'nilai_pg' => $j->jawabPilihanGanda->sum('nilai'),
            'nilai_essai' => $j->jawabEssai->sum('nilai'),
            'total' => $j->jawabPilihanGanda->sum('nilai') + $j->jawabEssai->sum('nilai'),
            'durasi_menit' => $j->start && $j->end
                ? round($j->start->diffInMinutes($j->end))
                : null,
        ]);

        return response()->json([
            'kelas' => $kelas->kelas,
            'kode_kelas' => $kelas->kode_kelas,
            'jurusan' => $kelas->jurusan?->nama_jurusan,
            'mapel' => $mapel->mapel,
            'data' => $data->values(),
        ]);
    }

    // Level 3: detail jawaban siswa (tidak berubah)
    public function detail($jawabanId)
    {
        $jawaban = JawabanSiswa::with([
            'siswa',
            'sesiUjian.paketSoal',
            'jawabPilihanGanda.soal.pilihan',
            'jawabPilihanGanda.pilihan',
            'jawabEssai.soal',
        ])->findOrFail($jawabanId);

        $sesi = $jawaban->sesiUjian;

        $pg = $jawaban->jawabPilihanGanda->map(fn($j) => [
            'id' => $j->id,
            'pertanyaan' => $j->soal?->pertanyaan,
            'nilai_soal' => $j->soal?->nilai,
            'pilihan' => $j->soal?->pilihan->map(fn($p) => [
                'id' => $p->id,
                'pilihan' => $p->pilihan,
                'benar' => (bool)$p->benar,
            ]),
            'pilihan_id' => $j->pilihan_id,
            'pilihan_dipilih' => $j->pilihan?->pilihan,
            'hasil' => $j->hasil,
            'nilai' => $j->nilai,
        ]);

        $essai = $jawaban->jawabEssai->map(fn($j) => [
            'id' => $j->id,
            'pertanyaan' => $j->soal?->pertanyaan,
            'nilai_soal' => $j->soal?->nilai,
            'jawaban' => $j->jawaban,
            'nilai' => $j->nilai,
        ]);

        return response()->json([
            'data' => [
                'jawaban_id' => $jawaban->id,
                'nama_siswa' => $jawaban->nama_siswa,
                'nis' => $jawaban->siswa?->nis,
                'judul_ujian' => $sesi?->judul,
                'mata_pelajaran' => $sesi?->paketSoal?->mata_pelajaran,
                'mapel_id' => $sesi?->paketSoal?->mapel_id,
                'kelas_id' => $jawaban->siswa?->kelas_id,
                'kode_kelas' => $sesi?->kode_kelas,
                'start' => $jawaban->start,
                'end' => $jawaban->end,
                'nilai_pg' => $jawaban->jawabPilihanGanda->sum('nilai'),
                'nilai_essai' => $jawaban->jawabEssai->sum('nilai'),
                'sesi_id' => $sesi?->id,
                'pg' => $pg,
                'essai' => $essai,
            ]
        ]);
    }

    public function nilaiEssai(Request $request, $jawabEssaiId)
    {
        $request->validate(['nilai' => 'required|integer|min:0']);
        JawabEssai::findOrFail($jawabEssaiId)->update(['nilai' => $request->nilai]);
        return response()->json(['message' => 'Nilai essai disimpan']);
    }
}
