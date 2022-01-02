<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Orchid\Filters\Filterable;
use Orchid\Filters\HttpFilter;
use Orchid\Screen\AsSource;

/**
 * App\Models\Build
 *
 * @property int $id
 * @property string $buildConfig
 * @property string $cdnConfig
 * @property string|null $patchConfig
 * @property string|null $productConfig
 * @property string $productKey
 * @property string $expansion
 * @property string $major
 * @property string $minor
 * @property int $clientBuild
 * @property string|null $patch
 * @property string $name
 * @property string $encodingContentHash
 * @property string $encodingCdnHash
 * @property string $rootContentHash
 * @property string $rootCdnHash
 * @property string $installContentHash
 * @property string $installCdnHash
 * @property string $downloadContentHash
 * @property string $downloadCdnHash
 * @property string|null $sizeContentHash
 * @property string|null $sizeCdnHash
 * @property bool $custom Builds which contains custom generated configs
 * @property Carbon|null $compiledAt Compile time of the Wow.exe
 * @property array<string> $processedBy List of process class names which processed this build
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Product $product
 * @method static Builder|Build defaultSort(string $column, string $direction = 'asc')
 * @method static Builder|Build filters(?HttpFilter $httpFilter = null)
 * @method static Builder|Build filtersApply(array $filters = [])
 * @method static Builder|Build filtersApplySelection($selection)
 * @method static Builder|Build newModelQuery()
 * @method static Builder|Build newQuery()
 * @method static Builder|Build query()
 * @mixin Eloquent
 */
final class Build extends Model
{
    use Filterable, AsSource;

    /** @var string */
    protected $table = 'build';

    /** @inerhitDoc */
    protected $fillable = [
        'buildConfig',
        'cdnConfig',
        'patchConfig',
        'productConfig',
        'productKey',
        'expansion',
        'major',
        'minor',
        'clientBuild',
        'name',
        'encodingContentHash',
        'encodingCdnHash',
        'rootContentHash',
        'rootCdnHash',
        'installContentHash',
        'installCdnHash',
        'downloadContentHash',
        'downloadCdnHash',
        'sizeContentHash',
        'sizeCdnHash',
        'archive',
        'compiledAt',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'processedBy' => 'array',
        'custom'      => 'bool',
        'compiledAt'  => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'patch',
        'build'
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'build',
        'created_at'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'productKey', 'product');
    }
}
