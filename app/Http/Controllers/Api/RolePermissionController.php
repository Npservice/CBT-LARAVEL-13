<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function roles()
    {
        $roles = Role::with('permissions')
            ->orderByRaw("FIELD(nama_role, 'admin', 'guru-pembuat-soal', 'guru', 'siswa')")
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'nama_role'    => $r->nama_role,
                'display_role' => $r->display_role,
                'permissions'  => $r->permissions->pluck('nama_permission'),
            ]);

        return response()->json(['data' => $roles]);
    }

    public function permissions()
    {
        $perms = Permission::orderBy('group_permission')->orderBy('display_permission')->get();

        $grouped = $perms->groupBy('group_permission')->map(fn($items, $group) => [
            'group'       => $group,
            'permissions' => $items->map(fn($p) => [
                'id'               => $p->id,
                'nama_permission'  => $p->nama_permission,
                'display_permission' => $p->display_permission,
            ])->values(),
        ])->values();

        return response()->json(['data' => $grouped]);
    }

    public function syncPermissions(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($roleId);

        // Prevent editing admin role permissions
        if ($role->nama_role === 'admin') {
            return response()->json(['message' => 'Permission role admin tidak dapat diubah'], 403);
        }

        $role->permissions()->sync($request->permissions);

        return response()->json(['message' => 'Permission berhasil diperbarui']);
    }
}
