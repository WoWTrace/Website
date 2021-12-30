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
    /**
     * @OA\Post(
     *     path="/v1/listfile/getByPath",
     *     summary="Get listfile entry by path",
     *     tags={"ListFile"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="path", type="string", example="interface/cinematics/logo_800.avi")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="path", type="string", example="interface/cinematics/logo_800.avi"),
     *         ),
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="File path not found!"),
     * )
     */
    public function __invoke(ListFileGetByPathRequest $request): Response
    {
        $path = (string)$request->get('path');
        $file = ListFile::query()->where('path', $path)->first();

        if (!$file) {
            abort(Response::HTTP_NOT_FOUND, 'File path not found!');
        }

        return new JsonResponse([
            'id'   => $file->id,
            'path' => $file->path
        ]);
    }
}
