<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Get all guru
     */
    public function index(Request $request)
    {
        $totalOriginal = Guru::count();

        $query = Guru::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nig', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);

        // Check which pagination mode to use
        $useCursor = $request->has('cursor');

        if ($useCursor) {
            return $this->cursorPaginate($query, $request, $perPage, $totalOriginal);
        } else {
            return $this->offsetPaginate($query, $request, $perPage, $totalOriginal);
        }
    }

    private function offsetPaginate($query, $request, $perPage, $totalOriginal)
    {
        $page = $request->get('page', 1);
        $gurus = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $gurus->items(),
            'pagination' => [
                'total' => $gurus->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $gurus->currentPage(),
                'last_page' => (int) $gurus->lastPage(),
                'from' => $gurus->firstItem() ?: 0,
                'to' => $gurus->lastItem() ?: 0,
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

        $gurus = $query->limit($limit)->get();
        $hasNext = $gurus->count() > $perPage;
        if ($hasNext) {
            $gurus = $gurus->take($perPage);
        }

        $nextCursor = $prevCursor = null;
        if ($gurus->count() > 0) {
            $lastItem = $gurus->last();
            if ($hasNext) {
                $nextCursor = $lastItem->id;
            }
            if ($cursor) {
                $prevCursor = $gurus->first()->id;
            }
        }

        $filteredQuery = Guru::query();
        if ($request->has('search')) {
            $search = $request->search;
            $filteredQuery->where('nama', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('nig', 'like', "%{$search}%");
        }
        $filteredTotal = $filteredQuery->count();

        return response()->json([
            'data' => $gurus->values()->toArray(),
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

    /**
     * Get single guru
     */
    public function show($id)
    {
        $guru = Guru::findOrFail($id);

        return response()->json([
            'data' => $guru
        ]);
    }

    /**
     * Create new guru
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:guru,user_id',
            'nig' => 'required|string|unique:guru,nig',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => 'required|email|unique:guru,email',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $guru = Guru::create($request->all());

        return response()->json([
            'message' => 'Guru created successfully',
            'data' => $guru
        ], 201);
    }

    /**
     * Update guru
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'user_id' => 'sometimes|exists:users,id|unique:guru,user_id,' . $id,
            'nig' => 'sometimes|string|unique:guru,nig,' . $id,
            'nama' => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'email' => 'sometimes|email|unique:guru,email,' . $id,
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $guru->update($request->all());

        return response()->json([
            'message' => 'Guru updated successfully',
            'data' => $guru
        ]);
    }

    /**
     * Delete guru
     */
    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return response()->json([
            'message' => 'Guru deleted successfully'
        ]);
    }

    /**
     * Search guru
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $gurus = Guru::where('nama', 'like', "%{$query}%")
                    ->orWhere('nig', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();

        return response()->json([
            'data' => $gurus->map(fn($guru) => [
                'id' => $guru->id,
                'nama' => $guru->nama,
                'nig' => $guru->nig,
                'email' => $guru->email,
                'description' => $guru->nig,
            ])
        ]);
    }
}
