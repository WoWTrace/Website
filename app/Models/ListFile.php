<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * App\Models\ListFile
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $type
 * @property int|null $user_id
 * @property string|null $lookup
 * @property string|null $content_hash
 * @property string|null $root_cdn
 * @property bool $verified
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ListFile defaultSort(string $column, string $direction = 'asc')
 * @method static Builder|ListFile filters(?\Orchid\Filters\HttpFilter $httpFilter = null)
 * @method static Builder|ListFile filtersApply(array $filters = [])
 * @method static Builder|ListFile filtersApplySelection($selection)
 * @method static Builder|ListFile newModelQuery()
 * @method static Builder|ListFile newQuery()
 * @method static Builder|ListFile query()
 * @mixin Eloquent
 */
final class ListFile extends Model
{
    use Filterable, AsSource;

    /** @var string */
    protected $table = 'listfile';

    /** @inerhitDoc */
    protected $fillable = [
        'path',
        'type',
        'user_id',
        'lookup',
        'content_hash',
        'root_cdn',
        'verified',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'verified'   => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id',
        'path',
        'type'
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'path',
        'type'
    ];
}
