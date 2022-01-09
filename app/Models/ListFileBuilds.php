<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ListFileBuilds
 *
 * @property int $id
 * @property int $buildId
 * @property-read Build $build
 * @method static Builder|ListFileBuilds newModelQuery()
 * @method static Builder|ListFileBuilds newQuery()
 * @method static Builder|ListFileBuilds query()
 * @mixin Eloquent
 */
class ListFileBuilds extends Model
{
    /** @var string */
    protected $table = 'listfile_builds';

    public function build(): BelongsTo
    {
        return $this->belongsTo(Build::class, 'buildId', 'id');
    }
}
