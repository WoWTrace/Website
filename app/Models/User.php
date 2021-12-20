<?php

namespace App\Models;

use App\Orchid\Presenters\UserPresenter;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;
use Orchid\Filters\HttpFilter;
use Orchid\Platform\Models\Role;
use Orchid\Platform\Models\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property array|null $permissions
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|User byAccess(string $permitWithoutWildcard)
 * @method static \Illuminate\Database\Eloquent\Builder|User byAnyAccess($permitsWithoutWildcard)
 * @method static \Illuminate\Database\Eloquent\Builder|User countByDays($startDate = null, $stopDate = null, string $dateColumn = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|User countForGroup(string $groupColumn)
 * @method static \Illuminate\Database\Eloquent\Builder|User defaultSort(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|User filters(?\Orchid\Filters\HttpFilter $httpFilter = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User filtersApply(array $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User filtersApplySelection($selection)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User sumByDays(string $value, $startDate = null, $stopDate = null, string $dateColumn = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|User valuesByDays(string $value, $startDate = null, $stopDate = null, string $dateColumn = 'created_at')
 * @mixin Eloquent
 */
final class User extends Authenticatable
{
    /** @inerhitDoc */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    /** @inerhitDoc */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /** @inerhitDoc */
    protected $casts = [
        'permissions'       => 'array',
        'email_verified_at' => 'datetime',
    ];

    /** @inerhitDoc */
    protected $allowedFilters = [
        'id',
        'name',
        'email',
        'permissions',
    ];

    /** @inerhitDoc */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];


    public function presenter(): UserPresenter
    {
        return new UserPresenter($this);
    }
}
