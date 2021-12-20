<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class BuildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::query()->insert([
            //<editor-fold desc="Mainline">
            [
                'product'    => 'wow',
                'name'       => 'WoW Retail',
                'badgeText'  => 'Retail',
                'badgeType'  => 'badge-primary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowt',
                'name'       => 'WoW PTR',
                'badgeText'  => 'PTR',
                'badgeType'  => 'badge-warning',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wow_beta',
                'name'       => 'WoW Beta',
                'badgeText'  => 'Beta',
                'badgeType'  => 'badge-danger',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Classic">
            [
                'product'    => 'wow_classic',
                'name'       => 'WoW Classic',
                'badgeText'  => 'Classic',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wow_classic_ptr',
                'name'       => 'WoW Classic PTR',
                'badgeText'  => 'Classic PTR',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wow_classic_beta',
                'name'       => 'WoW Classic Beta',
                'badgeText'  => 'Classic Beta',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Classic Era">
            [
                'product'    => 'wow_classic_era',
                'name'       => 'WoW Classic Era',
                'badgeText'  => 'Classic Era',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wow_classic_era_ptr',
                'name'       => 'WoW Classic Era PTR',
                'badgeText'  => 'Classic Era PTR',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wow_classic_era_beta',
                'name'       => 'WoW Classic Era Beta',
                'badgeText'  => 'Classic Era Beta',
                'badgeType'  => 'badge-info',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Event">
            [
                'product'    => 'wowe1',
                'name'       => 'WoW Event 1',
                'badgeText'  => 'Vendor',
                'badgeType'  => 'badge-secondary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowe2',
                'name'       => 'WoW Event 2',
                'badgeText'  => 'Event 1',
                'badgeType'  => 'badge-secondary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowe3',
                'name'       => 'WoW Event 3',
                'badgeText'  => 'Event 2',
                'badgeType'  => 'badge-secondary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Other">
            [
                'product'    => 'wowz',
                'name'       => 'WoW Submission',
                'badgeText'  => 'Submission',
                'badgeType'  => 'badge-secondary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowlivetest',
                'name'       => 'WoW Live Test',
                'badgeText'  => 'Live Test',
                'badgeType'  => 'badge-secondary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Vendor">
            [
                'product'    => 'wowv',
                'name'       => 'WoW Vendor',
                'badgeText'  => 'Vendor',
                'badgeType'  => 'badge-danger',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowv2',
                'name'       => 'WoW Vendor 2',
                'badgeText'  => 'Vendor 2',
                'badgeType'  => 'badge-danger',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>

            //<editor-fold desc="Alpha">
            [
                'product'    => 'wowdev',
                'name'       => 'WoW Alpha',
                'badgeText'  => 'Alpha',
                'badgeType'  => 'badge-danger',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'product'    => 'wowdev2',
                'name'       => 'TBC Classic Alpha',
                'badgeText'  => 'Classic Alpha',
                'badgeType'  => 'badge-danger',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            //</editor-fold>
        ]);
    }
}