<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\JawabEssai;
use App\Models\JawabPilihanGanda;
use App\Models\Pilihan;
use App\Models\SesiUjian;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaPortalController extends Controller
{
    private function getSiswa(Request $request)
    {
        return Siswa::with(['kelas.jurusan'])->where('user_id', $request->user()->id)->first();
    }

    public function tersedia(Request $request)
    {
        $siswa = $this->getSiswa($request);
        $user = $request->user();

        if (!$siswa) {
            return response()->json([
                'data' => [],
                'siswa' => [
                    'nama' => $user->name,
                    'nis' => null,
                    'jurusan' => null,
                    'kelas' => null,
                ],
                'no_profile' => true,
            ]);
        }

        $now = now();

        $sesiList = SesiUjian::with('paketSoal')
            ->where('start', '<=', $now->copy()->addMinutes(15))
            ->where('end', '>=', $now)
            ->where(function ($q) use ($siswa) {
                $q->whereNull('kelas_id')->orWhere('kelas_id', $siswa->kelas_id);
            })
            ->orderBy('start')
            ->get();

        $jawabanMap = JawabanSiswa::where('siswa_id', $siswa->id)
            ->whereIn('sesi_ujian_id', $sesiList->pluck('id'))
            ->get()
            ->keyBy('sesi_ujian_id');

        $result = $sesiList->map(function ($sesi) use ($jawabanMap) {
            $jawaban = $jawabanMap[$sesi->id] ?? null;
            $status = 'belum';
            if ($jawaban) {
                $status = $jawaban->end ? 'selesai' : 'lanjut';
            }
            return [
                'id' => $sesi->id,
                'judul' => $sesi->judul,
                'kode_paket' => $sesi->kode_paket,
                'mata_pelajaran' => $sesi->paketSoal?->mata_pelajaran,
                'durasi_menit' => $sesi->paketSoal?->durasi_menit,
                'kode_kelas' => $sesi->kode_kelas,
                'start' => $sesi->start,
                'end' => $sesi->end,
                'status' => $status,
                'jawaban_id' => $jawaban?->id,
            ];
        });

        return response()->json([
            'data' => $result,
            'siswa' => [
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'jurusan' => $siswa->kelas?->jurusan?->nama_jurusan,
                'kelas' => $siswa->kelas?->kode_kelas,
            ],
            'no_profile' => false,
        ]);
    }

    public function mulai(Request $request, $sesiId)
    {
        $siswa = $this->getSiswa($request);
        $sesi = SesiUjian::with(['paketSoal.soalPilihanGanda', 'paketSoal.soalEssai'])->findOrFail($sesiId);
        $now = now();

        if ($now < $sesi->start || $now > $sesi->end) {
            return response()->json(['message' => 'Sesi ujian tidak aktif'], 422);
        }

        $existing = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('sesi_ujian_id', $sesiId)->first();

        if ($existing) {
            return response()->json(['data' => ['jawaban_id' => $existing->id]]);
        }

        $jawaban = null;
        DB::transaction(function () use ($sesi, $siswa, $now, &$jawaban) {
            $jawaban = JawabanSiswa::create([
                'sesi_ujian_id' => $sesi->id,
                'siswa_id' => $siswa->id,
                'nama_siswa' => $siswa->nama,
                'start' => $now,
            ]);

            foreach ($sesi->paketSoal->soalPilihanGanda as $soal) {
                JawabPilihanGanda::create([
                    'jawaban_siswa_id' => $jawaban->id,
                    'soal_id' => $soal->id,
                    'pilihan_id' => null,
                    'nilai' => 0,
                ]);
            }

            foreach ($sesi->paketSoal->soalEssai as $soal) {
                JawabEssai::create([
                    'jawaban_siswa_id' => $jawaban->id,
                    'soal_id' => $soal->id,
                    'jawaban' => null,
                    'nilai' => 0,
                ]);
            }
        });

        return response()->json(['data' => ['jawaban_id' => $jawaban->id]], 201);
    }

    public function getSoal(Request $request, $jawabanId)
    {
        $siswa = $this->getSiswa($request);
        $jawaban = JawabanSiswa::with([
            'sesiUjian.paketSoal',
            'jawabPilihanGanda.soal.pilihan',
            'jawabEssai.soal',
        ])->where('id', $jawabanId)->where('siswa_id', $siswa->id)->firstOrFail();

        $sesi = $jawaban->sesiUjian;
        $paket = $sesi->paketSoal;
        $now = now();
        $durasi = $paket?->durasi_menit ?? 60;
        $endByDurasi = $jawaban->start->copy()->addMinutes($durasi);
        $endTime = $endByDurasi->lt($sesi->end) ? $endByDurasi : $sesi->end;
        $sisaDetik = max(0, $endTime->timestamp - $now->timestamp);

        $pg = $jawaban->jawabPilihanGanda->shuffle()->map(fn($j) => [
            'jawab_id' => $j->id,
            'soal_id' => $j->soal_id,
            'pertanyaan' => $j->soal?->pertanyaan,
            'nilai_soal' => $j->soal?->nilai,
            'pilihan' => $j->soal?->pilihan->shuffle()->map(fn($p) => ['id' => $p->id, 'pilihan' => $p->pilihan]),
            'pilihan_id' => $j->pilihan_id,
        ]);

        $essai = $jawaban->jawabEssai->shuffle()->map(fn($j) => [
            'jawab_id' => $j->id,
            'soal_id' => $j->soal_id,
            'pertanyaan' => $j->soal?->pertanyaan,
            'nilai_soal' => $j->soal?->nilai,
            'jawaban' => $j->jawaban,
        ]);

        return response()->json([
            'data' => [
                'jawaban_id' => $jawaban->id,
                'judul' => $sesi->judul,
                'mata_pelajaran' => $paket?->mata_pelajaran,
                'sudah_selesai' => !is_null($jawaban->end),
                'sisa_detik' => $sisaDetik,
                'end_time' => $endTime->toIso8601String(),
                'pg' => $pg,
                'essai' => $essai,
            ]
        ]);
    }

    public function jawabPg(Request $request, $jawabanId)
    {
        $request->validate([
            'jawab_id' => 'required|string',
            'pilihan_id' => 'nullable|string',
        ]);

        $siswa = $this->getSiswa($request);
        $jawaban = JawabanSiswa::where('id', $jawabanId)->where('siswa_id', $siswa->id)->firstOrFail();

        if ($jawaban->end) {
            return response()->json(['message' => 'Ujian sudah selesai'], 422);
        }

        JawabPilihanGanda::where('id', $request->jawab_id)
            ->where('jawaban_siswa_id', $jawabanId)
            ->update(['pilihan_id' => $request->pilihan_id]);

        return response()->json(['message' => 'Jawaban disimpan']);
    }

    public function jawabEssai(Request $request, $jawabanId)
    {
        $request->validate([
            'jawab_id' => 'required|string',
            'jawaban' => 'nullable|string',
        ]);

        $siswa = $this->getSiswa($request);
        $jawaban = JawabanSiswa::where('id', $jawabanId)->where('siswa_id', $siswa->id)->firstOrFail();

        if ($jawaban->end) {
            return response()->json(['message' => 'Ujian sudah selesai'], 422);
        }

        JawabEssai::where('id', $request->jawab_id)
            ->where('jawaban_siswa_id', $jawabanId)
            ->update(['jawaban' => $request->jawaban]);

        return response()->json(['message' => 'Jawaban disimpan']);
    }

    public function selesai(Request $request, $jawabanId)
    {
        $siswa = $this->getSiswa($request);
        $jawaban = JawabanSiswa::with('jawabPilihanGanda.soal')
            ->where('id', $jawabanId)->where('siswa_id', $siswa->id)->firstOrFail();

        if ($jawaban->end) {
            return response()->json(['message' => 'Ujian sudah diselesaikan'], 422);
        }

        $totalNilai = 0;
        foreach ($jawaban->jawabPilihanGanda as $j) {
            if ($j->pilihan_id) {
                $pilihan = Pilihan::find($j->pilihan_id);
                if ($pilihan?->benar) {
                    $nilai = $j->soal?->nilai ?? 1;
                    $j->update(['nilai' => $nilai, 'hasil' => true]);
                    $totalNilai += $nilai;
                } else {
                    $j->update(['nilai' => 0, 'hasil' => false]);
                }
            }
        }

        $jawaban->update(['end' => now()]);

        return response()->json([
            'message' => 'Ujian berhasil diselesaikan',
            'data' => ['nilai_pg' => $totalNilai],
        ]);
    }
}
