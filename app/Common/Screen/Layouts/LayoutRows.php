<?php

declare(strict_types=1);

namespace App\Common\Screen\Layouts;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;
use Throwable;

/**
 * Class Layout Rows.
 */
class LayoutRows extends Layout
{
    /**
     * @var string
     */
    protected $template = 'layouts.layoutRow';

    public function __construct(array $layouts = [])
    {
        $this->layouts = $layouts;
    }

    public static function make(array $layouts = [])
    {
        return new static($layouts);
    }

    /**
     * @param Repository $repository
     *
     * @return Factory|View
     * @throws Throwable
     *
     */
    public function build(Repository $repository)
    {
        return $this->buildAsDeep($repository);
    }

    /**
     * @param string|null $title
     *
     * @return static
     */
    public function title(string $title = null): self
    {
        $this->variables['title'] = $title;

        return $this;
    }

    /**
     * @param string|null $class
     *
     * @return static
     */
    public function icon(string $icon = null): self
    {
        $this->variables['icon'] = $icon;

        return $this;
    }
}
