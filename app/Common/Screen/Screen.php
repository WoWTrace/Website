<?php

declare(strict_types=1);

namespace App\Common\Screen;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Orchid\Screen\Action;
use Orchid\Screen\Commander;
use Orchid\Screen\Layout;
use Orchid\Screen\LayoutFactory;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen as BaseScreen;
use Orchid\Support\Facades\Dashboard;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Throwable;

/**
 * Class Screen.
 */
abstract class Screen extends BaseScreen
{
    use Commander;

    /**
     * The number of predefined arguments in the route.
     *
     * Example: dashboard/my-screen/{method?}
     */
    private const COUNT_ROUTE_VARIABLES = 1;

    /**
     * Display header name.
     *
     * @var string
     */
    public $name;

    /**
     * Display header description.
     *
     * @var string
     */
    public $description;

    /**
     * Permission.
     *
     * @var string|array
     */
    public $permission;

    /**
     * @var Repository
     */
    private $source;

    /**
     * Get can transfer to the screen only
     * user-created methods available in it.
     *
     * @array
     */
    public static function getAvailableMethods(): array
    {
        return array_diff(
            get_class_methods(static::class), // Custom methods
            get_class_methods(self::class),   // Basic methods
            ['query']                                   // Except methods
        );
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar()
    {
        return [];
    }

    /**
     * @param string $method
     * @param string $slug
     *
     * @return View
     * @throws Throwable
     *
     */
    public function asyncBuild(string $method, string $slug)
    {
        Dashboard::setCurrentScreen($this);

        abort_unless(method_exists($this, $method), 404, "Async method: {$method} not found");

        $query  = $this->callMethod($method, request()->all());
        $source = new Repository($query);

        /** @var Layout $layout */
        $layout = collect($this->layout())
            ->map(function ($layout) {
                return is_object($layout) ? $layout : resolve($layout);
            })
            ->map(function (Layout $layout) use ($slug) {
                return $layout->findBySlug($slug);
            })
            ->filter()
            ->whenEmpty(function () use ($slug) {
                abort(404, "Async template: {$slug} not found");
            })
            ->first();

        return $layout->currentAsync()->build($source);
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     * @throws ReflectionException
     *
     */
    private function callMethod(string $method, array $parameters = [])
    {
        return call_user_func_array([$this, $method],
            $this->reflectionParams($method, $parameters)
        );
    }

    /**
     * @param string $method
     * @param array $httpQueryArguments
     *
     * @return array
     * @throws ReflectionException
     *
     */
    private function reflectionParams(string $method, array $httpQueryArguments = []): array
    {
        $class = new ReflectionClass($this);

        if (!is_string($method)) {
            return [];
        }

        if (!$class->hasMethod($method)) {
            return [];
        }

        $parameters = $class->getMethod($method)->getParameters();

        return collect($parameters)
            ->map(function ($parameter, $key) use ($httpQueryArguments) {
                return $this->bind($key, $parameter, $httpQueryArguments);
            })->all();
    }

    /**
     * It takes the serial number of the argument and the required parameter.
     * To convert to object.
     *
     * @param int $key
     * @param ReflectionParameter $parameter
     * @param array $httpQueryArguments
     *
     * @return mixed
     * @throws BindingResolutionException
     *
     */
    private function bind(int $key, ReflectionParameter $parameter, array $httpQueryArguments)
    {
        $class = $parameter->getType() && !$parameter->getType()->isBuiltin()
            ? $parameter->getType()->getName()
            : null;

        $original = array_values($httpQueryArguments)[$key] ?? null;

        if ($class === null || is_object($original)) {
            return $original;
        }

        $instance = resolve($class);

        if ($original === null || !is_a($instance, UrlRoutable::class)) {
            return $instance;
        }

        $model = $instance->resolveRouteBinding($original);

        throw_if(
            $model === null && !$parameter->isDefaultValueAvailable(),
            (new ModelNotFoundException())->setModel($class, [$original])
        );

        optional(Route::current())->setParameter($parameter->getName(), $model);

        return $model;
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    abstract public function layout(): array;

    /**
     * @param Request $request
     * @param mixed ...$parameters
     *
     * @return Factory|View|\Illuminate\View\View|mixed
     * @throws ReflectionException
     *
     */
    public function handle(Request $request, ...$parameters)
    {
        Dashboard::setCurrentScreen($this);
        abort_unless($this->checkAccess(), 403);

        if ($request->isMethod('GET')) {
            return $this->redirectOnGetMethodCallOrShowView($parameters);
        }

        $method = Route::current()->parameter('method', Arr::last($parameters));

        $parameters = array_diff(
            $parameters,
            [$method]
        );

        $query = $request->query();
        $query = !is_array($query) ? [] : $query;

        $parameters = array_filter($parameters);
        $parameters = array_merge($query, $parameters);

        $response = $this->callMethod($method, $parameters);

        return $response ?? back();
    }

    /**
     * @return bool
     */
    private function checkAccess(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return true;
        }

        return $user->hasAnyAccess($this->permission);
    }

    /**
     * Defines the URL to represent
     * the page based on the calculation of link arguments.
     *
     * @param array $httpQueryArguments
     *
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws ReflectionException
     *
     */
    protected function redirectOnGetMethodCallOrShowView(array $httpQueryArguments)
    {
        $expectedArg = count(Route::current()->getCompiled()->getVariables()) - self::COUNT_ROUTE_VARIABLES;
        $realArg     = count($httpQueryArguments);

        if ($realArg <= $expectedArg) {
            return $this->view($httpQueryArguments);
        }

        array_pop($httpQueryArguments);

        return redirect()->action([static::class, 'handle'], $httpQueryArguments);
    }

    /**
     * @param array $httpQueryArguments
     *
     * @return Factory|\Illuminate\View\View
     * @throws ReflectionException
     *
     */
    public function view(array $httpQueryArguments = [])
    {
        $query        = $this->callMethod('query', $httpQueryArguments);
        $this->source = new Repository($query);
        $commandBar   = $this->buildCommandBar($this->source);

        return view('layouts.base', [
            'name'                => $this->name,
            'description'         => $this->description,
            'commandBar'          => $commandBar,
            'layouts'             => $this->build(),
            'formValidateMessage' => $this->formValidateMessage(),
        ]);
    }

    /**
     * @return View
     * @throws Throwable
     *
     */
    public function build()
    {
        return LayoutFactory::blank([
            $this->layout(),
        ])->build($this->source);
    }

    /**
     * @return string
     */
    public function formValidateMessage(): string
    {
        return __('Please check the entered data, it may be necessary to specify in other languages.');
    }
}
