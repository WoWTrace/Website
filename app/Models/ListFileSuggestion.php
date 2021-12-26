<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ListFileSuggestion
 *
 * @property int $id
 * @property int $userId
 * @property string $path
 * @property string|null $type
 * @property bool|null $accepted
 * @property int|null $reviewerUserId
 * @property Carbon|null $reviewedAt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User|null $reviewerUser
 * @property-read \App\Models\User $user
 * @method static Builder|ListFileSuggestion newModelQuery()
 * @method static Builder|ListFileSuggestion newQuery()
 * @method static Builder|ListFileSuggestion query()
 * @mixin Eloquent
 */
class ListFileSuggestion extends Model
{
    /** @var string */
    protected $table = 'listfile_suggestion';

    /** @inerhitDoc */
    protected $fillable = [
        'id',
        'userId',
        'path',
        'type',
        'accepted',
        'reviewerUserId',
        'reviewedAt',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'accepted'   => 'bool',
        'reviewedAt' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function reviewerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewerUserId', 'id');
    }
}
