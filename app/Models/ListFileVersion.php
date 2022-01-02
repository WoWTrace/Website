<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ListFileVersion
 *
 * @property int $id
 * @property string $contentHash
 * @property bool $encrypted
 * @property int|null $fileSize
 * @property bool $processed
 * @property int $buildId
 * @property int $clientBuild
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Build $build
 * @method static Builder|ListFileVersion newModelQuery()
 * @method static Builder|ListFileVersion newQuery()
 * @method static Builder|ListFileVersion query()
 * @mixin Eloquent
 */
final class ListFileVersion extends Model
{
    /** @var string */
    protected $table = 'listfile_version';

    /** @inerhitDoc */
    protected $fillable = [
        'contentHash',
        'encrypted',
        'processed',
        'buildId',
        'clientBuild',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'encrypted'  => 'boolean',
        'processed'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function build(): BelongsTo
    {
        return $this->belongsTo(Build::class, 'buildId', 'id');
    }
}
