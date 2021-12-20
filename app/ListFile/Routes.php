<?php

declare(strict_types=1);

namespace App\ListFile;

use App\ListFile\Controllers\ListFileDownload;
use App\ListFile\Controllers\ListFileGetById;
use App\ListFile\Controllers\ListFileGetByPath;
use App\ListFile\Controllers\ListFileInsert;
use Illuminate\Routing\Router;

final class Routes
{
    public static function registerApi(Router $router): void
    {
        $router->group(
            [
                'prefix'     => 'listfile',
                'as'         => 'listfile.',
                'middleware' => ['permission:listfile.api']
            ],
            static function (Router $router): void {
                $router->post('/getById', ListFileGetById::class)->name('getById');
                $router->post('/getByPath', ListFileGetByPath::class)->name('getByPath');
                $router->get('/download', ListFileDownload::class)->name('download');

                $router->put('/insert', ListFileInsert::class)->name('insert');
            }
        );
    }

    public static function registerWeb(Router $router): void
    {
        $router->group(
            [
                'prefix' => 'listfile',
                'as'     => 'listfile.'
            ],
            static function (Router $router): void {
                $router->get('/download', ListFileDownload::class)->name('download')->middleware(['permission:listfile.view']);
            }
        );
    }
}
