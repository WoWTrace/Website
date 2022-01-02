<?php

declare(strict_types=1);

namespace App\ListFile\Layouts\SuggestionsReview;

use Illuminate\View\View;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

class SuggestionsDetailReviewLayout extends Layout
{
    /**
     * @var string
     */
    protected $template = 'layouts.suggestionsDetailReview';

    /**
     * @var Repository
     */
    protected $query;

    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'buildDetails';

    public function build(Repository $repository): string
    {
        $this->query = $repository;

        if (is_string($this->target) || is_array($this->target)) {
            $this->target = $repository->get($this->target, $this->target);
        }

        return (string)$this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view($this->template, [
            'repository' => $this->query,
        ]);
    }
}
