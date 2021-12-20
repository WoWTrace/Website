<?php

namespace App\Providers;

use Erorus\CASC\BLTE;
use Erorus\CASC\Cache;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

final class TACTProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(Cache::class, function ($app) {
            $cachePath = Storage::path('tactCache');

            if (!is_dir($cachePath)) {
                mkdir($cachePath, 0777, true);
            }

            return new Cache($cachePath);
        });
    }

    /**
     * @throws Exception
     */
    public function boot(): void
    {
        if (PHP_INT_MAX < 8589934590) {
            throw new Exception("Requires 64-bit PHP");
        }

        if (!in_array('blte', stream_get_wrappers())) {
            stream_wrapper_register('blte', BLTE::class);
        }
    }
}
