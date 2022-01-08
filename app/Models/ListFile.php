<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Filters\HttpFilter;
use Orchid\Screen\AsSource;

/**
 * App\Models\ListFile
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $type
 * @property int|null $userId
 * @property string|null $lookup
 * @property bool $verified
 * @property Carbon|null $pathDiscovery
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|ListFileVersion[] $versions
 * @property-read int|null $versions_count
 * @method static Builder|ListFile defaultSort(string $column, string $direction = 'asc')
 * @method static Builder|ListFile filters(?HttpFilter $httpFilter = null)
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
        'userId',
        'lookup',
        'contentHash',
        'rootCdnHash',
        'verified',
        'encrypted',
        'processed',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'verified'      => 'boolean',
        'pathDiscovery' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id',
        'path',
        'type',
        'lookup',
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'path',
        'type',
        'lookup',
    ];

    public function versions(): HasMany
    {
        return $this
            ->hasMany(ListFileVersion::class, 'id', 'id')
            ->orderBy('clientBuild');
    }
}
