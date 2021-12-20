<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Convert422To400
{
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Response $response
         */
        $response = $next($request);

        if ($response->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
