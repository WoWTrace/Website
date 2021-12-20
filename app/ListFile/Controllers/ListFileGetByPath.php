<?php
declare(strict_types=1);

namespace App\ListFile\Controllers;

use App\ListFile\Requests\ListFileGetByPathRequest;
use App\Models\ListFile;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function abort;

final class ListFileGetByPath
{
    public function __invoke(ListFileGetByPathRequest $request): Response
    {
        $path = (string)$request->get('path');
        $file = ListFile::query()->where('path', $path)->first();

        if (!$file) {
            abort(Response::HTTP_NOT_FOUND, 'File Id not found!');
        }

        return new JsonResponse([
            'id'   => $file->id,
            'path' => $file->path
        ]);
    }
}
