<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index(Request $request)
    {
        // Get total users without filter (original count)
        $totalOriginal = User::count();

        $query = User::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort', 'id');
        $sortDir = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 10);

        // Check which pagination mode to use
        $useCursor = $request->has('cursor');
        $usePage = $request->has('page');

        // Determine pagination mode
        if ($useCursor) {
            // Cursor-based pagination
            return $this->cursorPaginate($query, $request, $perPage, $totalOriginal);
        } else {
            // Offset-based pagination (default)
            return $this->offsetPaginate($query, $request, $perPage, $totalOriginal);
        }
    }

    /**
     * Offset-based pagination (page-based)
     */
    private function offsetPaginate($query, $request, $perPage, $totalOriginal)
    {
        $page = $request->get('page', 1);

        // Paginate with per_page
        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'total_original' => $totalOriginal,
                'per_page' => (int) $perPage,
                'current_page' => (int) $users->currentPage(),
                'last_page' => (int) $users->lastPage(),
                'from' => $users->firstItem() ?: 0,
                'to' => $users->lastItem() ?: 0,
                'mode' => 'offset'
            ]
        ]);
    }

    /**
     * Cursor-based pagination
     */
    private function cursorPaginate($query, $request, $perPage, $totalOriginal)
    {
        $cursor = $request->get('cursor');
        $sortDir = $request->get('direction', 'asc');
        $sortBy = $request->get('sort', 'id');
        $limit = $perPage + 1; // Get extra item to check if there's next page

        if ($cursor) {
            if ($sortDir === 'asc') {
                $query->where('id', '>', $cursor);
            } else {
                $query->where('id', '<', $cursor);
            }
        }

        $users = $query->limit($limit)->get();

        // Check if there's next page
        $hasNext = $users->count() > $perPage;
        if ($hasNext) {
            $users = $users->take($perPage);
        }

        $nextCursor = null;
        $prevCursor = null;

        if ($users->count() > 0) {
            $lastItem = $users->last();
            if ($hasNext) {
                $nextCursor = $lastItem->id;
            }
            if ($cursor) {
                $prevCursor = $users->first()->id;
            }
        }

        // Calculate filtered total
        $filteredQuery = User::query();
        if ($request->has('search')) {
            $search = $request->search;
            $filteredQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%");
        }
        $filteredTotal = $filteredQuery->count();

        return response()->json([
            'data' => $users->values()->toArray(),
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
     * Get single user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,guru,guru-pembuat-soal,siswa',
        ]);

        $roleModel = Role::where('nama_role', $request->role)->first();

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'role_id' => $roleModel?->id,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|string|in:admin,guru,guru-pembuat-soal,siswa',
        ]);

        $data = $request->all();

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->has('role')) {
            $roleModel = Role::where('nama_role', $request->role)->first();
            $data['role_id'] = $roleModel?->id;
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Search users (for select dropdown, etc)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'username', 'role']);

        return response()->json([
            'data' => $users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'description' => $user->email,
            ])
        ]);
    }
}
