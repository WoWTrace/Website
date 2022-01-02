<?php

declare(strict_types=1);

namespace App\Common\Screen\Layouts;

use Illuminate\View\View;
use Orchid\Screen\Contracts\Personable;
use Orchid\Screen\Layouts\Content;

class PersonaWithoutLink extends Content
{
    /**
     * @var string
     */
    protected $template = 'layouts.personaWithoutLink';

    /**
     * @param Personable $user
     *
     * @return View
     */
    public function render(Personable $user): View
    {
        return view($this->template, [
            'title'    => $user->title(),
            'subTitle' => $user->subTitle(),
            'image'    => $user->image(),
        ]);
    }
}
