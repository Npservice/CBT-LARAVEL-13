<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuruPengampu;
use App\Models\SesiUjian;
use Illuminate\Http\Request;

class SesiUjianController extends Controller
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

    public function index(Request $request)
    {
        $user = auth()->user();
        $isGuruRole = in_array($user->role, ['guru', 'guru-pembuat-soal']);
        $mapelIds = $isGuruRole ? $this->getGuruMapelIds() : [];

        $baseQuery = fn() => SesiUjian::when($isGuruRole, fn($q) =>
            $q->whereHas('paketSoal', fn($pq) => $pq->whereIn('mapel_id', $mapelIds))
        );

        $totalOriginal = $baseQuery()->count();
        $query = $baseQuery()->with(['paketSoal', 'kelas.jurusan']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('kode_paket', 'like', "%{$search}%")
                  ->orWhereHas('paketSoal', function($pq) use ($search) {
                      $pq->where('kode_soal', 'like', "%{$search}%")
                         ->orWhere('mata_pelajaran', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('start', 'desc');

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $sesiUjian = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $sesiUjian->items(),
            'pagination' => [
                'total' => $sesiUjian->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $sesiUjian->currentPage(),
                'last_page' => (int) $sesiUjian->lastPage(),
                'from' => $sesiUjian->firstItem() ?: 0,
                'to' => $sesiUjian->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $sesiUjian = SesiUjian::with(['paketSoal', 'kelas.jurusan'])->findOrFail($id);

        return response()->json(['data' => $sesiUjian]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soal,id',
            'judul'         => 'required|string|max:255',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'start'         => 'required|date_format:Y-m-d\TH:i',
            'end'           => 'required|date_format:Y-m-d\TH:i|after:start',
        ]);

        $paketSoal = \App\Models\PaketSoal::findOrFail($request->paket_soal_id);

        // Kelas harus terikat dengan mapel paket soal ini
        if ($request->kelas_id) {
            $kelasValid = \Illuminate\Support\Facades\DB::table('mata_pelajaran_kelas')
                ->where('mata_pelajaran_id', $paketSoal->mapel_id)
                ->where('kelas_id', $request->kelas_id)
                ->exists();
            if (!$kelasValid) {
                return response()->json(['message' => 'Kelas ini tidak terikat dengan mata pelajaran dari paket soal ini'], 422);
            }
        }

        // Sesi auto-increment per paket_soal
        $sesi = SesiUjian::where('paket_soal_id', $request->paket_soal_id)->count() + 1;

        $kodeKelas = null;
        if ($request->kelas_id) {
            $kelas = \App\Models\Kelas::find($request->kelas_id);
            $kodeKelas = $kelas?->kode_kelas;
        }

        $sesiUjian = SesiUjian::create([
            'paket_soal_id' => $request->paket_soal_id,
            'judul'         => $request->judul,
            'kelas_id'      => $request->kelas_id,
            'kode_kelas'    => $kodeKelas,
            'sesi'          => $sesi,
            'kode_paket'    => $paketSoal->kode_soal . '-S' . $sesi,
            'start'         => $request->start,
            'end'           => $request->end,
        ]);

        return response()->json([
            'message' => 'Sesi Ujian berhasil dibuat',
            'data'    => $sesiUjian->load(['paketSoal', 'kelas.jurusan'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $sesiUjian = SesiUjian::with('paketSoal')->findOrFail($id);

        $request->validate([
            'judul'    => 'sometimes|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'start'    => 'sometimes|date_format:Y-m-d\TH:i',
            'end'      => 'sometimes|date_format:Y-m-d\TH:i|after:start',
        ]);

        $data = $request->only(['judul', 'start', 'end']);

        if ($request->has('kelas_id')) {
            if ($request->kelas_id) {
                $kelasValid = \Illuminate\Support\Facades\DB::table('mata_pelajaran_kelas')
                    ->where('mata_pelajaran_id', $sesiUjian->paketSoal->mapel_id)
                    ->where('kelas_id', $request->kelas_id)
                    ->exists();
                if (!$kelasValid) {
                    return response()->json(['message' => 'Kelas ini tidak terikat dengan mata pelajaran dari paket soal ini'], 422);
                }
                $kelas = \App\Models\Kelas::find($request->kelas_id);
                $data['kode_kelas'] = $kelas?->kode_kelas;
            } else {
                $data['kode_kelas'] = null;
            }
            $data['kelas_id'] = $request->kelas_id;
        }

        $sesiUjian->update($data);

        return response()->json([
            'message' => 'Sesi Ujian berhasil diperbarui',
            'data'    => $sesiUjian->load(['paketSoal', 'kelas.jurusan'])
        ]);
    }

    public function destroy($id)
    {
        $sesiUjian = SesiUjian::findOrFail($id);
        $sesiUjian->delete();

        return response()->json(['message' => 'Sesi Ujian berhasil dihapus']);
    }
}
