<?php

declare(strict_types=1);

namespace App\Common\Screen;

use Closure;
use Illuminate\Support\Str;
use Orchid\Screen\TD;

class TDWithColor extends TD
{
    protected ?string  $backgroundColor;
    protected ?string  $textColor;
    protected ?Closure $renderColor;

    public function backgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function textColor(string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function renderColor(Closure $closure): self
    {
        $this->renderColor = $closure;

        return $this;
    }

    /**
     * @inerhitDoc
     */
    public function buildTd($repository)
    {
        $value = $this->render
            ? $this->handler($repository)
            : $repository->getContent($this->name);

        if (!empty($this->renderColor)) {
            call_user_func($this->renderColor, $repository, $this);
        }

        return view('partials.layouts.tdWithColor', [
            'align'           => $this->align,
            'value'           => $value,
            'render'          => $this->render,
            'slug'            => $this->sluggable(),
            'width'           => $this->width,
            'colspan'         => $this->colspan,
            'backgroundColor' => $this->backgroundColor ?? null,
            'textColor'       => $this->textColor ?? null,
        ]);
    }

    private function sluggable(): string
    {
        return Str::slug($this->name);
    }
}
