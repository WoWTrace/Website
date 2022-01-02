<?php

declare(strict_types=1);

namespace App\ListFile;

use App\ListFile\Screens\ListFileScreen;
use App\ListFile\Screens\SuggestionsReviewDetailScreen;
use App\ListFile\Screens\SuggestionsReviewScreen;
use App\ListFile\Screens\SuggestScreen;
use App\Models\ListFileSuggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Screen\Actions\Menu;
use Tabuna\Breadcrumbs\Trail;

final class Platform
{
    public const ROUTE_LISTFILE_OVERVIEW_SLUG           = 'listfile';
    public const ROUTE_LISTFILE_OVERVIEW_KEY            = 'platform.listfile';
    public const ROUTE_LISTFILE_SUGGEST_SLUG            = 'listfile/suggest';
    public const ROUTE_LISTFILE_SUGGEST_KEY             = 'platform.listfile.suggest';
    public const ROUTE_LISTFILE_SUGGESTIONS_REVIEW_SLUG = 'listfile/suggestions/review';
    public const ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY  = 'platform.listfile.suggestions.review';
    public const ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_SLUG = 'listfile/suggestions/detailReview/{suggestionKey}';
    public const ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_KEY  = 'platform.listfile.suggestions.review.detail';

    public static function boot(Dashboard $dashboard)
    {
        $permissions = ItemPermission::group('ListFile')
            ->addPermission('listfile.reviewSuggestions', 'Review suggestions')
            ->addPermission('listfile.suggest', 'Suggest Entries')
            ->addPermission('listfile.add', 'Add Entries')
            ->addPermission('listfile.delete', 'Delete Entries')
            ->addPermission('listfile.api', 'API access');

        $dashboard->registerPermissions($permissions);
    }

    public static function registerMainMenu(): array
    {
        $pendingSuggestions = null;

        if (($user = Auth::getUser()) && $user->hasAccess('listfile.reviewSuggestions')) {
            if (($count = ListFileSuggestion::where('userId', '!=', $user->id)->whereNull('accepted')->count()) && $count > 0) {
                $pendingSuggestions = $count;
            }
        }

        return [
            Menu::make('Files')
                ->icon('list')
                ->route(self::ROUTE_LISTFILE_OVERVIEW_KEY)->list([
                    Menu::make('Suggest names')
                        ->icon('cloud-upload')
                        ->route(self::ROUTE_LISTFILE_SUGGEST_KEY)
                        ->permission('listfile.suggest'),
                    Menu::make('Review suggestions')
                        ->icon('eyeglasses')
                        ->route(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY)
                        ->permission('listfile.reviewSuggestions')
                        ->badge(static function () use ($pendingSuggestions): ?int {
                            return $pendingSuggestions;
                        }),
                ])->badge(static function () use ($pendingSuggestions): ?int {
                    return $pendingSuggestions;
                }),
        ];
    }

    public static function registerScreens(): void
    {
        Route::screen(self::ROUTE_LISTFILE_OVERVIEW_SLUG, ListFileScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_LISTFILE_OVERVIEW_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY));
            });

        Route::screen(self::ROUTE_LISTFILE_SUGGEST_SLUG, SuggestScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_LISTFILE_SUGGEST_KEY)
            ->middleware(['permission:listfile.suggest'])
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY))
                    ->push(__('Suggest'), route(self::ROUTE_LISTFILE_SUGGEST_KEY));
            });

        Route::screen(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_SLUG, SuggestionsReviewScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY)
            ->middleware(['permission:listfile.reviewSuggestions'])
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY))
                    ->push(__('Review Suggestions'), route(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY));
            });

        Route::screen(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_SLUG, SuggestionsReviewDetailScreen::class)
            ->domain(env('APP_DOMAIN', 'localhost'))
            ->name(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_KEY)
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(__('ListFile'))
                    ->push(__('Files'), route(self::ROUTE_LISTFILE_OVERVIEW_KEY))
                    ->push(__('Review Suggestions'), route(self::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY))
                    ->push(__('Detail'));
            });
    }
}
