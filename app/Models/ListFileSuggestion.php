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
 * App\Models\ListFileSuggestion
 *
 * @property string $suggestionKey
 * @property int $id
 * @property int $userId
 * @property string $path
 * @property string|null $type
 * @property bool|null $accepted
 * @property int|null $reviewerUserId
 * @property Carbon|null $reviewedAt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $reviewerUser
 * @property-read User $user
 * @method static Builder|ListFileSuggestion defaultSort(string $column, string $direction = 'asc')
 * @method static Builder|ListFileSuggestion filters(?HttpFilter $httpFilter = null)
 * @method static Builder|ListFileSuggestion filtersApply(array $filters = [])
 * @method static Builder|ListFileSuggestion filtersApplySelection($selection)
 * @method static Builder|ListFileSuggestion newModelQuery()
 * @method static Builder|ListFileSuggestion newQuery()
 * @method static Builder|ListFileSuggestion query()
 * @mixin Eloquent
 */
class ListFileSuggestion extends Model
{
    use Filterable, AsSource;

    /** @inerhitDoc */
    public $incrementing = false;
    /** @inerhitDoc */
    protected $table = 'listfile_suggestion';
    /** @inerhitDoc */
    protected $primaryKey = ['id', 'userId', 'path'];
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

    /**
     * Set the keys for a save update query.
     *
     * @param Builder $query
     * @return Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}
