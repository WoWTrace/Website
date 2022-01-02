<?php

namespace App\ListFile\Layouts\SuggestionsReview;

use App\Common\Screen\Actions\Span;
use App\Common\Screen\Layouts\PersonaWithoutLink;
use App\Common\Screen\TDExtended;
use App\ListFile\Platform as ListFilePlatform;
use App\Models\Build;
use App\Models\ListFile;
use App\Models\ListFileVersion;
use Illuminate\Database\Eloquent\Model;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Content;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class SuggestionsReviewTableLayout extends Table
{
    /** @inerhitDoc */
    protected $target = 'suggestionsReview';

    /** @inerhitDoc */
    protected function columns(): array
    {
        return [
            TD::make('suggestionKey', __('Suggestion Key'))
                ->cantHide()
                ->alignLeft()
                ->render(function (Model $suggestion) {
                    return Link::make($suggestion->suggestionKey)
                        ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_KEY, ['suggestionKey' => $suggestion->suggestionKey]);
                }),

            TD::make('userId', __('User'))
                ->cantHide()
                ->alignLeft()
                ->render(function (Model $suggestion) {
                    if ($user = User::find($suggestion->userId)) {
                        return new PersonaWithoutLink($user->presenter());
                    }

                    return sprintf(__('Unknown (%u)', $suggestion->userId));
                }),

            TD::make('entryCount', __('Entry Count'))
                ->cantHide()
                ->alignLeft(),

            TD::make('view', '')
                ->cantHide()
                ->alignRight()
                ->render(function (Model $suggestion) {
                    return Link::make('')
                        ->icon('magnifier')
                        ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_DETAIL_KEY, ['suggestionKey' => $suggestion->suggestionKey]);
                }),
        ];
    }
}
