<?php

namespace App\ListFile\Screens;

use App\Common\Screen\Screen;
use App\ListFile\Layouts\SuggestionsReview\SuggestionsReviewTableLayout;
use App\ListFile\Platform as ListFilePlatform;
use App\ListFile\Services\ListFileService;
use App\Models\ListFileSuggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Link;

class SuggestionsReviewScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'ListFile Suggestions Review';

    /** @inerhitDoc */
    public $description = 'On this page you will see ListFile suggestions from other users. Thank you for helping to review them 💖';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'suggestionsReview' => ListFileSuggestion::query()
                ->select(['suggestionKey', 'userId', DB::raw('COUNT(*) as entryCount')])
                ->where('userId', '!=', Auth::id())
                ->whereNull('accepted')
                ->groupBy('suggestionKey')
                ->paginate()
        ];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [
            Link::make(__('Refresh'))
                ->icon('reload')
                ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY),
            Link::make(__('Back'))
                ->icon('action-undo')
                ->route(ListFilePlatform::ROUTE_LISTFILE_OVERVIEW_KEY),
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        return [
            SuggestionsReviewTableLayout::class
        ];
    }
}
