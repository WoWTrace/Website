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
 * App\Models\Build
 * @property string $buildConfig
 * @property string $cdnConfig
 * @property string|null $patchConfig
 * @property string $productConfig
 * @property-read Product $product
 * @property string $expansion
 * @property string $major
 * @property string $minor
 * @property int $build
 * @property-read  string $patch
 * @property string $encodingContentHash
 * @property string $encodingCdnHash
 * @property string $rootContentHash
 * @property string $rootCdnHash
 * @property string $installContentHash
 * @property string $installCdnHash
 * @property string $downloadContentHash
 * @property string $downloadCdnHash
 * @property string $sizeContentHash
 * @property string $sizeCdnHash
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
        'product',
        'expansion',
        'major',
        'minor',
        'build',
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
    ];

    /** @inerhitDoc */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product', 'product');
    }
}
