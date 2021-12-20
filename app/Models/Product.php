<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Orchid\Screen\AsSource;

/**
 * App\Models\Product
 * @property int $id
 * @property string $product
 * @property string $name
 * @property string $badgeText
 * @property string $badgeType
 * @property boolean $encrypted
 * @property string|null $lastVersion
 * @property string|null $lastBuildConfig
 * @property Carbon|null $detected
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Build[] $builds
 * @property-read int|null $builds_count
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @mixin Eloquent
 */
final class Product extends Model
{
    use AsSource;

    /** @var string */
    protected $table = 'product';

    /** @inerhitDoc */
    protected $fillable = [
        'product',
        'name',
        'badgeText',
        'badgeType',
        'encrypted',
        'lastVersion',
        'lastBuildConfig',
        'detected',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'encrypted'  => 'boolean',
        'detected'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function builds()
    {
        return $this->hasMany(Build::class, 'product', 'product');
    }
}
