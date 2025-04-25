<?php

namespace Jiannius\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Permission::class, 'permission_users');
    }

    public function getPermissions()
    {
        return cache()->rememberForever('user_permissions_'.$this->id, fn () => $this->permissions()->get());
    }

    public function isPermitted($actions) : bool
    {
        return $this->getPermissions()->whereIn('name', (array) $actions)->isNotEmpty();
    }

    public function isPermittedAll($actions)
    {
        return $this->getPermissions()->whereIn('name', (array) $actions)->count() === count($actions);
    }

    public function grantPermission($action) : void
    {
        if ($this->isPermitted($action)) return;

        if ($permission = \App\Models\Permission::where('name', $action)->first()) {
            $this->permissions()->attach($permission->id);
            $this->clearPermissionsCache();
        }
    }

    public function forbidPermission($action) : void
    {
        if (!$this->isPermitted($action)) return;

        if ($permission = \App\Models\Permission::where('name', $action)->first()) {
            $this->permissions()->detach($permission->id);
            $this->clearPermissionsCache();
        }
    }

    public function clearPermissionsCache()
    {
        cache()->forget('user_permissions_'.$this->id);
    }
}
