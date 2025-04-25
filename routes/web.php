<?php

use Illuminate\Support\Facades\Route;

Route::post('__permission/grant', [\Jiannius\Permission\Controllers\PermissionController::class, 'grant'])
    ->middleware(['web', 'auth'])
    ->name('__permission.grant');

Route::post('__permission/forbid', [\Jiannius\Permission\Controllers\PermissionController::class, 'forbid'])
    ->middleware(['web', 'auth'])
    ->name('__permission.forbid');
