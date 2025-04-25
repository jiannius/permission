<?php

namespace Jiannius\Permission\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected static function booted() : void
    {
        static::saving(fn ($permission) => $permission->clearUsersCache());
        static::deleting(fn ($permission) => $permission->clearUsersCache());
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'permission_users');
    }

    // eg: ['invoice' => ['view', 'create', 'update', 'delete']]
    public static function actions()
    {
        $permissions = self::all()->map(function ($permission) {
            $split = explode('.', $permission->name);
            $module = head($split);
            $action = count($split) > 1 ? last($split) : null;
            return ['module' => $module, 'action' => $action];
        });

        return $permissions->pluck('module')->unique()->values()->mapWithKey(fn ($module) => [
            $module => $permissions->where('module', $module)->pluck('action')->values()->toArray(),
        ])->toArray();
    }

    public function clearUsersCache()
    {
        $this->users()->get()->each(fn ($user) => $user->clearPermissionsCache());
    }

}
