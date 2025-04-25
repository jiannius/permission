<?php

namespace Jiannius\Permission\Controllers;

use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function grant()
    {
        \App\Models\User::whereIn('id', (array) request()->user_id)->get()->each(function ($user) {
            foreach ((array) request()->permission as $name) {
                $user->grantPermission($name);
            }
        });
    }

    public function forbid()
    {
        \App\Models\User::whereIn('id', (array) request()->user_id)->get()->each(function ($user) {
            foreach ((array) request()->permission as $name) {
                $user->forbidPermission($name);
            }
        });
    }
}