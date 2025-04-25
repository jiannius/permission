<?php

namespace Jiannius\Permission;

class Policy
{
    public function role($user, $role) : bool
    {
        return $user->isRole($role);
    }

    public function permission($user, $permission) : bool
    {
        return $user->isPermitted($permission);
    }
}