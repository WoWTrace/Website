<?php
declare(strict_types=1);

namespace App\ListFile\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use function abort;
use function config;

final class ListFileDownload
{
    /**
     * @OA\Get(
     *     path="/v1/listfile/download",
     *     summary="Download full listfile",
     *     tags={"ListFile"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="text/plain",
     *             example="1;interface/cinematics/logo_800.avi"
     *         ),
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
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
