<?php

namespace App\Traits;

use App\Models\Permission;

trait HasPermissions
{
    public function hasPermission(string $permissionName): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->roleRelation()
            ->with('permissions')
            ->first()
            ?->permissions()
            ->where('nama_permission', $permissionName)
            ->exists() ?? false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return collect($permissions)->some(fn($permission) => $this->hasPermission($permission));
    }

    public function hasAllPermissions(array $permissions): bool
    {
        return collect($permissions)->every(fn($permission) => $this->hasPermission($permission));
    }

    public function getPermissions(): array
    {
        if ($this->role === 'admin') {
            return Permission::pluck('nama_permission')->toArray();
        }

        return $this->roleRelation()
            ->with('permissions')
            ->first()
            ?->permissions()
            ->pluck('nama_permission')
            ->toArray() ?? [];
    }

    public function getPermissionsByGroup(string $group): array
    {
        $permissions = $this->getPermissions();

        return Permission::where('group_permission', $group)
            ->whereIn('nama_permission', $permissions)
            ->pluck('nama_permission')
            ->toArray();
    }
}
