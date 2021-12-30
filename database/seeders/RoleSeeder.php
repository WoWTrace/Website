<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    public const SLUG_USER            = 'user';
    public const SLUG_RESTRICTED_USER = 'restrictedUser';

    public function run(): void
    {
        Role::query()->upsert(
            [
                [
                    'slug'        => self::SLUG_USER,
                    'name'        => 'User',
                    'permissions' => json_encode(["platform.index" => true, 'listfile.suggest' => true]),
                ],
                [
                    'slug'       => self::SLUG_RESTRICTED_USER,
                    'name'       => 'Restricted User',
                    'permission' => json_encode(["platform.index" => true]),
                ]
            ],
            ['slug']
        );
    }
}
