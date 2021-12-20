<?php


declare(strict_types=1);

use Illuminate\Routing\Router;

/**
 * @var Router $router
 */

$router->group(
    [],
    static function (Router $router): void {
        // Routes require login
        $router->group(
            ['middleware' => ['web']],
            static function (Router $router): void {
                App\ListFile\Routes::registerWeb($router);
            }
        );
    }
);