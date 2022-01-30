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
 * @property array $processedBy List of process class names which processed this build
 * @property int $firstBuildId
 * @property int $clientBuild
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Build $firstBuild
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
        'processedBy',
        'buildId',
        'clientBuild',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'encrypted'   => 'boolean',
        'processedBy' => 'array',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function firstBuild(): BelongsTo
    {
        return $this->belongsTo(Build::class, 'firstBuildId', 'id');
    }
}
