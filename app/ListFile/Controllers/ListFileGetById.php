<?php
declare(strict_types=1);

namespace App\ListFile\Controllers;

use App\ListFile\Requests\ListFileGetByIdRequest;
use App\Models\ListFile;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function abort;

final class ListFileGetById
{
    public function __invoke(ListFileGetByIdRequest $request): Response
    {
        $id   = (int)$request->get('id');
        $file = ListFile::query()->find($id);

        if (!$file) {
            abort(Response::HTTP_NOT_FOUND, 'File Id not found!');
        }

        return new JsonResponse([
            'id'   => $file->id,
            'path' => $file->path
        ]);
    }
}
