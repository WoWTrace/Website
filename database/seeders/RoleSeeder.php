<?php

declare(strict_types=1);

namespace Database\Seeders;

use Dashboard;
use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    public const SLUG_USER            = 'user';
    public const SLUG_RESTRICTED_USER = 'restrictedUser';
    public const SLUG_MODERATOR       = 'moderator';
    public const SLUG_ADMIN           = 'admin';

    public function run(): void
    {
        Role::query()->upsert(
            [
                [
                    'slug'          => self::SLUG_USER,
                    'name'          => 'User',
                    'permissions'   => json_encode(['platform.index' => true, 'listfile.suggest' => true]),
                ],
                [
                    'slug'          => self::SLUG_RESTRICTED_USER,
                    'name'          => 'Restricted User',
                    'permission'    => json_encode(['platform.index' => true]),
                ],
                [
                    'slug'          => self::SLUG_MODERATOR,
                    'name'          => 'Moderator',
                    'permissions'   => json_encode(
                        [
                            'platform.index' => true, 
                            'listfile.suggest' => true,
                            'listfile.reviewSuggestions' => true,
                        ]
                    ),
                ],
                [
                    'slug'          => self::SLUG_ADMIN,
                    'name'          => 'Admin',
                    'permission'    => json_encode(Dashboard::getAllowAllPermission()->toArray()),
                ]
            ],
            ['slug']
        );
    }
}
