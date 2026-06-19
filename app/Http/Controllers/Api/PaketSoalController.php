<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuruPengampu;
use App\Models\PaketSoal;
use Illuminate\Http\Request;

class PaketSoalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isPembuatSoal = $user->role === 'guru-pembuat-soal';

        $query = PaketSoal::with(['pembuat.guru']);

        if ($isPembuatSoal) {
            $guruPengampuIds = GuruPengampu::whereHas('guru', fn($q) => $q->where('user_id', $user->id))
                ->pluck('id')
                ->toArray();
            $query->whereIn('created_by', $guruPengampuIds);
        }

        $totalOriginal = (clone $query)->count();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_soal', 'like', "%{$search}%")
                  ->orWhere('mata_pelajaran', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $paketSoal = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paketSoal->items(),
            'pagination' => [
                'total' => $paketSoal->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $paketSoal->currentPage(),
                'last_page' => (int) $paketSoal->lastPage(),
                'from' => $paketSoal->firstItem() ?: 0,
                'to' => $paketSoal->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $paketSoal = PaketSoal::with(['pembuat.guru'])->findOrFail($id);

        return response()->json([
            'data' => $paketSoal
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mapel_id'    => 'required|exists:mata_pelajaran,id',
            'durasi_menit'=> 'required|integer|min:1',
            'kode_soal'   => 'required|string|unique:paket_soal,kode_soal',
        ]);

        // Resolve pembuat soal otomatis dari mapel
        $pembuat = GuruPengampu::with('guru')
            ->where('mata_pelajaran_id', $request->mapel_id)
            ->where('pembuat_soal', true)
            ->first();

        if (!$pembuat) {
            return response()->json(['message' => 'Mapel ini belum memiliki pembuat soal. Tentukan pembuat soal di menu Guru Pengampu terlebih dahulu.'], 422);
        }

        $mataPelajaran = \App\Models\MataPelajaran::find($request->mapel_id);

        $paketSoal = PaketSoal::create([
            'mapel_id'      => $request->mapel_id,
            'mata_pelajaran'=> $mataPelajaran->mapel,
            'created_by'    => $pembuat->id,
            'dibuat_oleh'   => $pembuat->guru?->nama,
            'durasi_menit'  => $request->durasi_menit,
            'kode_soal'     => $request->kode_soal,
        ]);

        return response()->json([
            'message' => 'Paket Soal berhasil dibuat',
            'data'    => $paketSoal->load(['pembuat.guru'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $paketSoal = PaketSoal::findOrFail($id);

        $request->validate([
            'mapel_id'    => 'sometimes|exists:mata_pelajaran,id',
            'durasi_menit'=> 'sometimes|integer|min:1',
            'kode_soal'   => 'sometimes|string|unique:paket_soal,kode_soal,' . $id,
        ]);

        $data = $request->only(['durasi_menit', 'kode_soal']);

        if ($request->has('mapel_id')) {
            $pembuat = GuruPengampu::with('guru')
                ->where('mata_pelajaran_id', $request->mapel_id)
                ->where('pembuat_soal', true)
                ->first();

            if (!$pembuat) {
                return response()->json(['message' => 'Mapel ini belum memiliki pembuat soal.'], 422);
            }

            $mataPelajaran = \App\Models\MataPelajaran::find($request->mapel_id);
            $data['mapel_id']       = $request->mapel_id;
            $data['mata_pelajaran'] = $mataPelajaran->mapel;
            $data['created_by']     = $pembuat->id;
            $data['dibuat_oleh']    = $pembuat->guru?->nama;
        }

        $paketSoal->update($data);

        return response()->json([
            'message' => 'Paket Soal berhasil diperbarui',
            'data'    => $paketSoal->load(['pembuat.guru'])
        ]);
    }

    public function destroy($id)
    {
        $paketSoal = PaketSoal::findOrFail($id);
        $paketSoal->delete();

        return response()->json([
            'message' => 'Paket Soal deleted successfully'
        ]);
    }
}
