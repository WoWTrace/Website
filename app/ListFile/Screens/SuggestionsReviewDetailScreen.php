<?php

namespace App\ListFile\Screens;

use App\Common\Screen\Screen;
use App\ListFile\Layouts\SuggestionsReview\SuggestionsDetailReviewLayout;
use App\ListFile\Platform as ListFilePlatform;
use App\Models\ListFile;
use App\Models\ListFileSuggestion;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;

class SuggestionsReviewDetailScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'ListFile Suggestions Review';

    public function __construct(private Request $request)
    {
        $this->name = sprintf(__('Review ListFile Suggest: %s'), $this->request->suggestionKey);
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        /** @var Collection $suggestions */
        $suggestions = ListFileSuggestion::where('suggestionKey', $this->request->suggestionKey)
            ->leftJoin('listfile', 'listfile.id', 'listfile_suggestion.id')
            ->select([
                'listfile_suggestion.id',
                DB::raw('listfile_suggestion.path as suggestedPath'),
                DB::raw('listfile.path as currentPath'),
                DB::raw('listfile.lookup as currentLookup'),
                'listfile.verified'
            ])
            ->whereNull('listfile_suggestion.accepted')
            ->whereNull('listfile.path', 'or')
            ->where('listfile.path', 'listfile_suggestion.path')
            ->get();

        return [
            'suggestionKey'       => $this->request->suggestionKey,
            'newSuggestions'      => $suggestions->filter(static function (Model $model) {
                return $model->verified === null;
            }),
            'nameSuggestions'     => $suggestions->filter(static function (Model $model) {
                return $model->verified !== null && empty($model->currentPath);
            }),
            'overrideSuggestions' => $suggestions->filter(static function (Model $model) {
                return !empty($model->currentPath) && empty($model->verified);
            }),
            'skipSuggestions'     => $suggestions->filter(static function (Model $model) {
                return !empty($model->verified);
            }),
        ];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [
            Link::make(__('Back'))
                ->icon('action-undo')
                ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY),
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        return [
            SuggestionsDetailReviewLayout::class
        ];
    }

    public function submit(Request $request)
    {
        $request->validate([
            'type'                  => 'required|string|in:accept,reject',
            'nameSuggestions'       => 'array',
            'nameSuggestions.*'     => 'integer',
            'overrideSuggestions'   => 'array',
            'overrideSuggestions.*' => 'integer',
            'newSuggestions'        => 'array',
            'newSuggestions.*'      => 'integer',
        ]);

        $currentUser = Auth::getUser();
        $acceptMode  = ($request->type === 'accept');
        $suggestions = ListFileSuggestion::where('suggestionKey', $request->suggestionKey)
            ->whereNull('accepted')
            ->where('userId', '!=', $currentUser->id)
            ->cursor();

        if ($suggestions->isEmpty()) {
            Toast::warning(__('An error occurred while editing, please try again later.'));

            return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY);
        }

        DB::beginTransaction();
        $now = now()->toDateTimeString();

        if (!$acceptMode) {
            /** @var ListFileSuggestion $suggestion */
            foreach ($suggestions as $suggestion) {
                $suggestion->update([
                    'accepted'       => false,
                    'reviewerUserId' => $currentUser->id,
                    'reviewedAt'     => $now
                ]);
            }

            // @TODO Notify suggestion sender

            Toast::success(__('ListFile Review successfully completed'));
            DB::commit();

            return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY);
        }

        $listFileQuery = ListFile::query();

        /** @var ListFileSuggestion $suggestion */
        foreach ($suggestions as $suggestion) {
            $suggestion->update([
                'accepted'       => true,
                'reviewerUserId' => $currentUser->id,
                'reviewedAt'     => $now
            ]);
        }

        Toast::success(__('ListFile Review successfully completed'));
        DB::commit();

        return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY);
    }
}
