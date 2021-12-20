<?php

namespace App\Common\Authorize;

use Auth;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[]|null ...$permissions
     * @return mixed
     *
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user();

        if (!$user || !$user->hasAnyAccess($permissions ?? [])) {
            throw new AuthorizationException('Unauthorized or missing correct permissions');
        }

        return $next($request);
    }
}