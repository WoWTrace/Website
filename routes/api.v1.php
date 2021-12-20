<?php


declare(strict_types=1);

use Illuminate\Routing\Router;

/**
 * @var Router $router
 */

$router->group(
    ['prefix' => 'v1', 'as' => 'api.v1.'],
    static function (Router $router): void {

        // Routes require login
        $router->group(
            ['middleware' => ['auth.basic']],
            static function (Router $router): void {
                App\ListFile\Routes::registerApi($router);
            }
        );
    }
);