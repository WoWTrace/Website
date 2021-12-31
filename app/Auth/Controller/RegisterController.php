<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Orchid\Access\UserSwitch;
use Orchid\Platform\Http\Controllers\Controller;
use Orchid\Platform\Models\Role;
use Redirect;
use Throwable;

class RegisterController extends Controller
{
    private const DEFAULT_PERMISSION = [
        'listfile.suggest',
    ];

    /**
     * @var Guard|SessionGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->guard = $auth->guard(config('platform.guard'));
        $this->middleware('guest');
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     *
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|min:3|max:20|alpha_dash|unique:users,name',
            'email'    => 'required|string|unique:users,email',
            'password' => 'required|string|min:1',
        ]);

        try {
            $user = User::create([
                'name'     => $request->get('name'),
                'email'    => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]);
        } catch (Throwable $ex) {
            return Redirect::refresh()->withErrors("User could not be created.");
        }

        if ($role = Role::firstWhere('slug', RoleSeeder::SLUG_USER)) {
            $user->replaceRoles([$role->id]);
        }

        $auth = $this->guard->attempt(
            $request->only(['email', 'password']),
            true
        );

        if ($auth) {
            $request->session()->regenerate();
            return $request->wantsJson() ? new JsonResponse([], 204) : redirect()->intended(route(config('platform.index')));
        }

        return Redirect::refresh()->withErrors("User could not be created.");
    }

    /**
     * @param Request $request
     * @param Guard $guard
     *
     * @return Factory|View
     */
    public function showRegisterForm(Request $request)
    {
        $user = $request->cookie('lockUser');

        /** @var EloquentUserProvider $provider */
        $provider = $this->guard->getProvider();

        $model = $provider->createModel()->find($user);

        return view('auth.register', [
            'name'       => __('Register'),
            'isLockUser' => optional($model)->exists ?? false,
            'lockUser'   => $model,
        ]);
    }

    /**
     * @param CookieJar $cookieJar
     *
     * @return RedirectResponse
     */
    public function resetCookieLockMe(CookieJar $cookieJar)
    {
        $lockUser = $cookieJar->forget('lockUser');

        return redirect()->route('platform.login')->withCookie($lockUser);
    }

    /**
     * @return RedirectResponse
     */
    public function switchLogout()
    {
        UserSwitch::logout();

        return redirect()->route(config('platform.index'));
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     *
     * @return RedirectResponse|JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended(route(config('platform.index')));
    }
}
