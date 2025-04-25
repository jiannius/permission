<?php

namespace Jiannius\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRole
{
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_users');
    }

    public function scopeIsRole($query, $names) : void
    {
        if (!$names) return;

        $query->whereHas('roles', function ($q) use ($names) {
            $q->where(function ($q) use ($names) {
                foreach ((array) $names as $name) {
                    if (str($name)->endsWith('*')) $q->orWhere('roles.slug', 'like', str($name)->beforeLast('*').'%');
                    else if (str($name)->endsWith('-*')) $q->orWhere('roles.slug', 'like', str($name)->beforeLast('-*').'%');
                    else if (str($name)->startsWith('*')) $q->orWhere('roles.slug', 'like', str($name)->afterFirst('*').'%');
                    else if (str($name)->startsWith('*-')) $q->orWhere('roles.slug', 'like', str($name)->afterFirst('*-').'%');
                    else $q->orWhere('roles.slug', $name);
                }
            });
        });
    }

    public function scopeIsAdmin($query) : void
    {
        $query->isRole(['admin', 'administrator']);
    }

    public function getRolesCacheKey()
    {
        return 'user_roles_'.$this->id;
    }

    public function getRoles()
    {
        return cache()->rememberForever($this->getRolesCacheKey(), fn () => $this->roles()->get());
    }

    public function isRole($names) : bool
    {
        return collect($names)->search(fn ($name) =>
            $this->getRoles()->search(fn ($role) =>
                $role->slug === $name
                    || (str($name)->endsWith('*') && str($role->slug)->startsWith(str($name)->beforeLast('*')))
                    || (str($name)->endsWith('-*') && str($role->slug)->startsWith(str($name)->beforeLast('-*')))
                    || (str($name)->startsWith('*') && str($role->slug)->endsWith(str($name)->afterFirst('*')))
                    || (str($name)->startsWith('*-') && str($role->slug)->endsWith(str($name)->afterFirst('*-')))
            ) !== false
        ) !== false;
    }

    public function isAdmin() : bool
    {
        return $this->isRole(['admin', 'administrator']);
    }

    public function clearRolesCache()
    {
        cache()->forget($this->getRolesCacheKey());
    }
}