<?php

declare(strict_types=1);

namespace App\Common\Screen\Actions;

use Orchid\Screen\Action;

/**
 * Class Icon.
 *
 * @method Icon icon(string $icon = null)
 * @method Icon class(string $classes = null)
 * @method Icon title(string $title = null)
 * @method Icon onclick(string $script = null)
 */
class Icon extends Action
{
    /**
     * @var string
     */
    protected $view = 'actions.icon';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class' => '',
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
