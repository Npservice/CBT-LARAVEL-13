<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = MataPelajaran::count();
        $query = MataPelajaran::with(['kelas.jurusan']);

        if ($request->has('kelas_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('kelas.id', $request->kelas_id));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $mapel = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $mapel->items(),
            'pagination' => [
                'total' => $mapel->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $mapel->currentPage(),
                'last_page' => (int) $mapel->lastPage(),
                'from' => $mapel->firstItem() ?: 0,
                'to' => $mapel->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => MataPelajaran::with(['kelas.jurusan'])->findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mapel'       => 'required|string|max:255',
            'kode_mapel'  => 'required|string|unique:mata_pelajaran,kode_mapel',
            'kelas_ids'   => 'required|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $mapel = MataPelajaran::create($request->only(['mapel', 'kode_mapel']));
        $mapel->kelas()->sync($request->input('kelas_ids', []));

        return response()->json([
            'message' => 'Mata Pelajaran created successfully',
            'data'    => $mapel->load(['kelas.jurusan']),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            'mapel'       => 'sometimes|string|max:255',
            'kode_mapel'  => 'sometimes|string|unique:mata_pelajaran,kode_mapel,' . $id,
            'kelas_ids'   => 'sometimes|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $mapel->update($request->only(['mapel', 'kode_mapel']));

        if ($request->has('kelas_ids')) {
            $mapel->kelas()->sync($request->input('kelas_ids', []));
        }

        return response()->json([
            'message' => 'Mata Pelajaran updated successfully',
            'data'    => $mapel->load(['kelas.jurusan']),
        ]);
    }

    public function destroy($id)
    {
        MataPelajaran::findOrFail($id)->delete();

        return response()->json(['message' => 'Mata Pelajaran deleted successfully']);
    }
}
