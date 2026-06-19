<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = Jurusan::count();
        $query = Jurusan::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_jurusan', 'like', "%{$search}%")
                  ->orWhere('kode_jurusan', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $jurusan = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $jurusan->items(),
            'pagination' => [
                'total' => $jurusan->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $jurusan->currentPage(),
                'last_page' => (int) $jurusan->lastPage(),
                'from' => $jurusan->firstItem() ?: 0,
                'to' => $jurusan->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        return response()->json([
            'data' => $jurusan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:255',
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan',
        ]);

        $jurusan = Jurusan::create($request->all());

        return response()->json([
            'message' => 'Jurusan created successfully',
            'data' => $jurusan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'nama_jurusan' => 'sometimes|string|max:255',
            'kode_jurusan' => 'sometimes|string|unique:jurusan,kode_jurusan,' . $id,
        ]);

        $jurusan->update($request->all());

        return response()->json([
            'message' => 'Jurusan updated successfully',
            'data' => $jurusan
        ]);
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();

        return response()->json([
            'message' => 'Jurusan deleted successfully'
        ]);
    }
}
