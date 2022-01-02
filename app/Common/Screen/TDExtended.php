<?php

declare(strict_types=1);

namespace App\Common\Screen;

use Closure;
use Illuminate\Support\Str;
use Orchid\Screen\TD;

class TDExtended extends TD
{
    /** @var string|null|int */
    protected $minWidth;
    protected ?string  $backgroundColor;
    protected ?string  $textColor;
    protected ?Closure $renderColor;

    /**
     * @param string|int $minWidth
     *
     * @return static
     */
    public function minWidth($minWidth): self
    {
        if (is_int($minWidth)) {
            $minWidth = sprintf('%upx', $minWidth);
        }

        $this->minWidth = $minWidth;

        return $this;
    }

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

        return view('partials.layouts.tdExtended', [
            'align'           => $this->align,
            'value'           => $value,
            'render'          => $this->render,
            'slug'            => $this->sluggable(),
            'width'           => $this->width,
            'colspan'         => $this->colspan,
            'backgroundColor' => $this->backgroundColor ?? null,
            'textColor'       => $this->textColor ?? null,
            'minWidth'        => $this->minWidth ?? null,
        ]);
    }

    private function sluggable(): string
    {
        return Str::slug($this->name);
    }
}
