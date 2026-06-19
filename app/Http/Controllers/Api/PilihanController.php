<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pilihan;
use Illuminate\Http\Request;

class PilihanController extends Controller
{
    public function index(Request $request)
    {
        $totalOriginal = Pilihan::count();
        $query = Pilihan::with(['soalPilihanGanda']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('pilihan', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $pilihan = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $pilihan->items(),
            'pagination' => [
                'total' => $pilihan->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $pilihan->currentPage(),
                'last_page' => (int) $pilihan->lastPage(),
                'from' => $pilihan->firstItem() ?: 0,
                'to' => $pilihan->lastItem() ?: 0,
            ]
        ]);
    }

    public function show($id)
    {
        $pilihan = Pilihan::with(['soalPilihanGanda'])->findOrFail($id);

        return response()->json([
            'data' => $pilihan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'soal_pilihan_ganda_id' => 'required|exists:soal_pilihan_ganda,id',
            'pilihan' => 'required|string',
            'benar' => 'required|boolean',
        ]);

        $pilihan = Pilihan::create($request->all());

        return response()->json([
            'message' => 'Pilihan created successfully',
            'data' => $pilihan->load(['soalPilihanGanda'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $pilihan = Pilihan::findOrFail($id);

        $request->validate([
            'soal_pilihan_ganda_id' => 'sometimes|exists:soal_pilihan_ganda,id',
            'pilihan' => 'sometimes|string',
            'benar' => 'sometimes|boolean',
        ]);

        $pilihan->update($request->all());

        return response()->json([
            'message' => 'Pilihan updated successfully',
            'data' => $pilihan->load(['soalPilihanGanda'])
        ]);
    }

    public function destroy($id)
    {
        $pilihan = Pilihan::findOrFail($id);
        $pilihan->delete();

        return response()->json([
            'message' => 'Pilihan deleted successfully'
        ]);
    }
}
