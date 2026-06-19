<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institusi;
use Illuminate\Http\Request;

class InstitusiController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = Institusi::count();
        $query = Institusi::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_sekolah', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $institusi = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $institusi->items(),
            'pagination' => [
                'total' => $institusi->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $institusi->currentPage(),
                'last_page' => (int) $institusi->lastPage(),
                'from' => $institusi->firstItem() ?: 0,
                'to' => $institusi->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $institusi = Institusi::findOrFail($id);

        return response()->json([
            'data' => $institusi
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'akreditasi_sekolah' => 'nullable|string|max:1',
            'tingkat' => 'nullable|string|max:255',
        ]);

        $institusi = Institusi::create($request->all());

        return response()->json([
            'message' => 'Institusi created successfully',
            'data' => $institusi
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $institusi = Institusi::findOrFail($id);

        $request->validate([
            'nama_sekolah' => 'sometimes|string|max:255',
            'akreditasi_sekolah' => 'nullable|string|max:1',
            'tingkat' => 'nullable|string|max:255',
        ]);

        $institusi->update($request->all());

        return response()->json([
            'message' => 'Institusi updated successfully',
            'data' => $institusi
        ]);
    }

    public function destroy($id)
    {
        $institusi = Institusi::findOrFail($id);
        $institusi->delete();

        return response()->json([
            'message' => 'Institusi deleted successfully'
        ]);
    }
}
