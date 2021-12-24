<?php

declare(strict_types=1);

namespace App\Common\Screen\Actions;

use Orchid\Screen\Action;

/**
 * Class Span.
 *
 * @method Span name(string $name = null)
 * @method Span icon(string $icon = null)
 * @method Span class(string $classes = null)
 * @method Span title(string $title = null)
 * @method Span onclick(string $script = null)
 */
class Span extends Action
{
    /**
     * @var string
     */
    protected $view = 'actions.span';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'p-2',
        'icon'  => null,
        'turbo' => true,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    public $inlineAttributes = [
        'onclick'
    ];
}
