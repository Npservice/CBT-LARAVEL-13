<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = Kelas::count();
        $query = Kelas::with('jurusan');

        // Filter kelas berdasarkan mapel yang terikat (via pivot mata_pelajaran_kelas)
        if ($request->has('mapel_id')) {
            $query->whereIn('id', function($q) use ($request) {
                $q->select('kelas_id')
                  ->from('mata_pelajaran_kelas')
                  ->where('mata_pelajaran_id', $request->mapel_id);
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_kelas', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhereHas('jurusan', fn($jq) => $jq->where('nama_jurusan', 'like', "%{$search}%"));
            });
        }

        $sortBy = $request->get('sort', 'kelas');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $kelas = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $kelas->items(),
            'pagination' => [
                'total' => $kelas->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $kelas->currentPage(),
                'last_page' => (int) $kelas->lastPage(),
                'from' => $kelas->firstItem() ?: 0,
                'to' => $kelas->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => Kelas::with('jurusan')->findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas'      => 'required|string|max:255',
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        $kelas = Kelas::create($request->only(['kelas', 'kode_kelas', 'jurusan_id']));

        return response()->json([
            'message' => 'Kelas created successfully',
            'data' => $kelas->load('jurusan')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'kelas'      => 'sometimes|string|max:255',
            'kode_kelas' => 'sometimes|string|unique:kelas,kode_kelas,' . $id,
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        $kelas->update($request->only(['kelas', 'kode_kelas', 'jurusan_id']));

        return response()->json([
            'message' => 'Kelas updated successfully',
            'data' => $kelas->load('jurusan')
        ]);
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();

        return response()->json(['message' => 'Kelas deleted successfully']);
    }
}
