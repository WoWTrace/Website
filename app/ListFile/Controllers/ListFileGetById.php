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
    /**
     * @OA\Post(
     *     path="/api/v1/listfile/getById",
     *     summary="Get listfile entry by id",
     *     tags={"ListFile"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1")
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
     *     @OA\Response(response=404, description="File id not found!"),
     * )
     */
    public function __invoke(ListFileGetByIdRequest $request): Response
    {
        $id = (int)$request->get('id');
        $file = ListFile::query()->find($id);

        if (!$file) {
            abort(Response::HTTP_NOT_FOUND, 'File id not found!');
        }

        return new JsonResponse([
            'id'   => $file->id,
            'path' => $file->path
        ]);
    }
}
