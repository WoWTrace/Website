<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Routing\UrlGenerator;

class Authenticate extends Middleware
{
    private UrlGenerator $urlGenerator;

    public function __construct(Auth $auth, UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        parent::__construct($auth);
    }

    protected function redirectTo($request): string
    {
        if (!$request->expectsJson()) {
            return $this->urlGenerator->route('login');
        }

        return '';
    }
}
