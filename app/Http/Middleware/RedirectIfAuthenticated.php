<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    private Factory    $authManager;
    private Redirector $redirector;

    public function __construct(Factory $authManager, Redirector $redirector)
    {
        $this->authManager = $authManager;
        $this->redirector = $redirector;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param string|null ...$guards
     *
     * @return Response
     *
     * @psalm-param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->authManager->guard($guard)->check()) {
                return $this->redirector->to('/');
            }
        }

        return $next($request);
    }
}
