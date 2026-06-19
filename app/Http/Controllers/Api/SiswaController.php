<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = Siswa::count();

        $query = Siswa::with(['kelas.jurusan']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);

        if ($request->has('cursor')) {
            return $this->cursorPaginate($query, $request, $perPage, $totalOriginal);
        } else {
            return $this->offsetPaginate($query, $request, $perPage, $totalOriginal);
        }
    }

    private function offsetPaginate($query, $request, $perPage, $totalOriginal)
    {
        $page = $request->get('page', 1);
        $siswas = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $siswas->items(),
            'pagination' => [
                'total' => $siswas->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $siswas->currentPage(),
                'last_page' => (int) $siswas->lastPage(),
                'from' => $siswas->firstItem() ?: 0,
                'to' => $siswas->lastItem() ?: 0,
                'mode' => 'offset'
            ]
        ]);
    }

    private function cursorPaginate($query, $request, $perPage, $totalOriginal)
    {
        $cursor = $request->get('cursor');
        $sortDir = $request->get('direction', 'asc');
        $limit = $perPage + 1;

        if ($cursor) {
            if ($sortDir === 'asc') {
                $query->where('id', '>', $cursor);
            } else {
                $query->where('id', '<', $cursor);
            }
        }

        $siswas = $query->limit($limit)->get();
        $hasNext = $siswas->count() > $perPage;
        if ($hasNext) {
            $siswas = $siswas->take($perPage);
        }

        $nextCursor = $prevCursor = null;
        if ($siswas->count() > 0) {
            $lastItem = $siswas->last();
            if ($hasNext) {
                $nextCursor = $lastItem->id;
            }
            if ($cursor) {
                $prevCursor = $siswas->first()->id;
            }
        }

        $filteredQuery = Siswa::query();
        if ($request->has('search')) {
            $search = $request->search;
            $filteredQuery->where('nama', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%");
        }
        $filteredTotal = $filteredQuery->count();

        return response()->json([
            'data' => $siswas->values()->toArray(),
            'pagination' => [
                'total' => $filteredTotal,
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'cursor' => $cursor,
                'next_cursor' => $nextCursor,
                'prev_cursor' => $prevCursor,
                'has_next' => $hasNext,
                'mode' => 'cursor'
            ]
        ]);
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas.jurusan'])->findOrFail($id);

        return response()->json([
            'data' => $siswa
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:siswa,user_id',
            'nis' => 'required|string|unique:siswa,nis',
            'nisn' => 'nullable|string|unique:siswa,nisn',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $siswa = Siswa::create($request->all());

        return response()->json([
            'message' => 'Siswa created successfully',
            'data' => $siswa->load(['kelas.jurusan'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'user_id' => 'sometimes|exists:users,id|unique:siswa,user_id,' . $id,
            'nis' => 'sometimes|string|unique:siswa,nis,' . $id,
            'nisn' => 'nullable|string|unique:siswa,nisn,' . $id,
            'nama' => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'email' => 'nullable|email',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'kelas_id' => 'sometimes|exists:kelas,id',
        ]);

        $siswa->update($request->all());

        return response()->json([
            'message' => 'Siswa updated successfully',
            'data' => $siswa->load(['kelas.jurusan'])
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return response()->json([
            'message' => 'Siswa deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $siswas = Siswa::where('nama', 'like', "%{$query}%")
                      ->orWhere('nis', 'like', "%{$query}%")
                      ->orWhere('nisn', 'like', "%{$query}%")
                      ->limit(10)
                      ->get();

        return response()->json([
            'data' => $siswas->map(fn($siswa) => [
                'id' => $siswa->id,
                'nama' => $siswa->nama,
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'description' => $siswa->nis,
            ])
        ]);
    }
}
