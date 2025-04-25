<?php

namespace Jiannius\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $guarded = [];

    protected static function booted() : void
    {
        static::saving(function ($role) {
            $role->users()->get()->each(fn ($user) => $user->clearRolesCache());
            $role->fillSlug();
        });

        static::deleting(function ($role) {
            $role->users()->get()->each(fn ($user) => $user->clearRolesCache());
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'role_users');
    }

    public function scopeSearch($query, $search): void
    {
        $query->where('name', 'like', "%$search%");
    }

    public function fillSlug()
    {
        $slug = $this->slug ?? str()->slug($this->name);

        throw_if(self::where('slug', $slug)->where('id', '<>', $this->id)->count(), new \Exception('Duplicated Role'));

        return $this->fill(['slug' => $slug]);
    }
}
