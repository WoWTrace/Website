<?php

declare(strict_types=1);

namespace App\Common\Screen\Actions;

use Orchid\Screen\Actions\ModalToggle;

/**
 * Class ModalToggle.
 *
 * @method ModalToggle name(string $name = null)
 * @method ModalToggle modal(string $modalName = null)
 * @method ModalToggle icon(string $icon = null)
 * @method ModalToggle class(string $classes = null)
 * @method ModalToggle parameters(array $name)
 * @method ModalToggle modalTitle(string $title)
 * @method ModalToggle async(bool $enabled = true)
 * @method ModalToggle open(bool $status = true)
 */
class LargeModalToggle extends ModalToggle
{
    /**
     * @var string
     */
    protected $view = 'platform::actions.modal';
}
