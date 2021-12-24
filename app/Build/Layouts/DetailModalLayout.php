<?php

declare(strict_types=1);

namespace App\Build\Layouts;

use App\Models\Build;
use Illuminate\View\View;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

class DetailModalLayout extends Layout
{
    /**
     * @var string
     */
    protected $template = 'layouts.build';

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
        if ($this->target instanceof Build) {
            return view($this->template, [
                'product'       => $this->target->product->product,
                'productName'   => $this->target->product->name,
                'buildName'     => $this->target->name,
                'version'       => sprintf('%s.%u', $this->target->patch, $this->target->clientBuild),
                'buildConfig'   => $this->target->buildConfig,
                'cdnConfig'     => $this->target->cdnConfig,
                'patchConfig'   => $this->target->patchConfig,
                'productConfig' => $this->target->productConfig,
                'detected'      => $this->target->created_at->format('Y-m-d H:i:s'),

                'encodingContentHash' => $this->target->encodingContentHash,
                'encodingCdnHash'     => $this->target->encodingCdnHash,
                'rootContentHash'     => $this->target->rootContentHash,
                'rootCdnHash'         => $this->target->rootCdnHash,
                'installContentHash'  => $this->target->installContentHash,
                'installCdnHash'      => $this->target->installCdnHash,
                'downloadCdnHash'     => $this->target->downloadCdnHash,
                'downloadContentHash' => $this->target->downloadContentHash,
                'sizeContentHash'     => $this->target->sizeContentHash,
                'sizeCdnHash'         => $this->target->sizeCdnHash,
            ]);
        }
        return view($this->template);
    }
}
