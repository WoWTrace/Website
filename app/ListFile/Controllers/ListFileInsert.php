<?php
declare(strict_types=1);

namespace App\ListFile\Controllers;

use App\ListFile\Requests\ListFileInsertRequest;
use App\ListFile\Services\ListFileService;
use App\Models\ListFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class ListFileInsert
{
    public function __invoke(ListFileInsertRequest $request, ListFileService $listFilePathService): Response
    {
        /** @var int|null $id */
        $id = $request->get('id');

        $path = $listFilePathService->pathClean((string)$request->get('path'));

        $listFileQuery = ListFile::query();

        if (!$id) {
            $maxId = $listFileQuery->max('id');
            if ($maxId) {
                $id = $maxId + 1;
            }

            $id = max($id ?? 0, config('listfile.customIdStart'));
        }


        $listFileQuery->insertOrIgnore([
            'id'        => $id,
            'path'      => $path,
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'user_id'   => Auth::id()
        ]);

        $file = $listFileQuery->where('path', $path)->first();

        return new JsonResponse([
            'id'   => $file->id,
            'path' => $file->path
        ]);
    }
}
