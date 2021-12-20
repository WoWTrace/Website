<?php
declare(strict_types=1);

namespace App\ListFile\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use function abort;
use function config;

final class ListFileDownload
{
    public function __invoke(): Response
    {
        /** @var string $listFileName */
        $listFileName = config('listfile.cachePath');
        $listFilePath = Storage::path($listFileName);
        if (!file_exists($listFilePath)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return Storage::download($listFileName);
    }
}
