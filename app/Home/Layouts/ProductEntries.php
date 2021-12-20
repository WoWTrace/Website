<?php

namespace App\Home\Layouts;

use App\Common\Screen\Actions\Icon;
use App\Models\Product;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class ProductEntries extends Table
{

    /** @var string */
    public const TARGET = 'productEntries';

    /** @inerhitDoc */
    protected $target = self::TARGET;

    public static function getContent(): array
    {
        $rows = [];

        Product::query()
            ->orderBy('id')
            ->each(static function (Product $product) use (&$rows) {
                $rows[] = $product;
            });

        return $rows;
    }

    /** @inerhitDoc */
    protected function columns(): array
    {
        return [
            TD::make('name', __('Name'))->cantHide()->render(static function (Product $product) {
                $name = $product->name;

                if (!$product->encrypted) {
                    return $name;
                }

                return sprintf('%s %s', $name, Icon::make()->icon('lock')->render());
            }),

            TD::make('lastVersion', __('Build'))->cantHide()->render(static function (Product $product) {
                if (!$product->lastVersion) {
                    return __('Unknown');
                }

                return $product->lastVersion;
            }),

            TD::make('detected', __('Detected at (CEST)'))->cantHide()->render(static function (Product $product) {
                if (!$product->detected) {
                    return __('Unknown');
                }

                return $product->detected->format('Y-m-d H:i:s');
            }),
        ];
    }
}
