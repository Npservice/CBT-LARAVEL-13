<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuruPengampu;
use App\Models\SoalEssai;
use Illuminate\Http\Request;

class SoalEssaiController extends Controller
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

        $baseScope = function($q) use ($request, $isGuruRole, $mapelIds) {
            if ($request->filled('paket_soal_id')) {
                $q->where('paket_soal_id', $request->paket_soal_id);
            }
            if ($isGuruRole) {
                $q->whereHas('paketSoal', fn($pq) => $pq->whereIn('mapel_id', $mapelIds));
            }
        };

        $totalOriginal = SoalEssai::tap($baseScope)->count();
        $query = SoalEssai::with(['paketSoal'])->tap($baseScope);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhereHas('paketSoal', fn($pq) => $pq->where('kode_soal', 'like', "%{$search}%"));
            });
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $soal = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $soal->items(),
            'pagination' => [
                'total' => $soal->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $soal->currentPage(),
                'last_page' => (int) $soal->lastPage(),
                'from' => $soal->firstItem() ?: 0,
                'to' => $soal->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $soal = SoalEssai::with(['paketSoal'])->findOrFail($id);

        return response()->json([
            'data' => $soal
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soal,id',
            'pertanyaan' => 'required|string',
            'nilai' => 'required|integer|min:1',
        ]);

        $soal = SoalEssai::create($request->all());

        return response()->json([
            'message' => 'Soal Essai created successfully',
            'data' => $soal->load(['paketSoal'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $soal = SoalEssai::findOrFail($id);

        $request->validate([
            'paket_soal_id' => 'sometimes|exists:paket_soal,id',
            'pertanyaan' => 'sometimes|string',
            'nilai' => 'sometimes|integer|min:1',
        ]);

        $soal->update($request->all());

        return response()->json([
            'message' => 'Soal Essai updated successfully',
            'data' => $soal->load(['paketSoal'])
        ]);
    }

    public function destroy($id)
    {
        $soal = SoalEssai::findOrFail($id);
        $soal->delete();

        return response()->json([
            'message' => 'Soal Essai deleted successfully'
        ]);
    }
}
