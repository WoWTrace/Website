<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Controller\LoginController;
use App\Auth\Controller\RegisterController;
use Illuminate\Routing\Router;

final class Routes
{
    public static function registerWeb(Router $router): void
    {
        $router->group(
            [
                'prefix' => 'login',
                'as'     => 'login.'
            ],
            static function (Router $router): void {
                $router->get('/', [LoginController::class, 'showLoginForm'])->name('index');
                $router->post('/', [LoginController::class, 'login'])->name('submit')->middleware('throttle:60,1');
                $router->get('/lock', [LoginController::class, 'resetCookieLockMe'])->name('lock');
            }
        );

        $router->group(
            [
                'prefix' => 'register',
                'as'     => 'register.'
            ],
            static function (Router $router): void {
                $router->get('/', [RegisterController::class, 'showRegisterForm'])->name('index');
                $router->post('/', [RegisterController::class, 'register'])->name('submit')->middleware('throttle:60,1');
            }
        );
    }
}
