<?php

declare(strict_types=1);

use App\Build;
use App\Home;
use App\ListFile;
use App\Roles;
use App\Users;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

Home\Platform::registerScreens();
ListFile\Platform::registerScreens();
Build\Platform::registerScreens();
Users\Platform::registerScreens();
Roles\Platform::registerScreens();
