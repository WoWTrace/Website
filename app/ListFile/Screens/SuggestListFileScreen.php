<?php

namespace App\ListFile\Screens;

use App\ListFile\Layouts\Suggest\SuggestTextBoxLayout;
use App\ListFile\Platform as ListFilePlatform;
use App\ListFile\Services\ListFileService;
use App\Models\ListFileSuggestion;
use App\Rules\CsvValidation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\Response;

class SuggestListFileScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'ListFile Suggest';

    public function __construct(private ListFileService $listFileService)
    {
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [
            Link::make(__('Back'))
                ->icon('action-undo')
                ->route(ListFilePlatform::ROUTE_LISTFILE_OVERVIEW_KEY),
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        $description = sprintf('Here you can suggest listfile entries. All suggestions will be reviewed by a moderator before they are added.
        Repeatedly entering listfile entries incorrectly may result in disabling this feature for you.<br><br>
        <b>A maximum of %u files per request is allowed.</b><br><br>
        You can also use our REST API to suggest listfile entries. The documentation for the API you can find <b><a href="/api/documentation" target="_blank">here</a></b>.', ListFileService::MAX_ROWS_PER_REQUEST);

        return [
            Layout::block(SuggestTextBoxLayout::class)
                ->title(__('Important information!'))
                ->description(__($description))
                ->commands(
                    Button::make(__('Submit'))
                        ->type(Color::PRIMARY())
                        ->icon('save')
                        ->method('submit')
                ),
        ];
    }

    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'suggestions' => [
                'required',
                new CsvValidation(ListFileService::MAX_ROWS_PER_REQUEST, ListFileService::PATH_REGEX)
            ],
        ]);


        /** @var int|null $userId */
        $userId = \Auth::id();
        $now = now()->toDateTimeString();

        $suggestionQuery = [];
        foreach ($this->listFileService->listFileStringToArray($request->get('suggestions')) as $fileId => $path) {
            $suggestionQuery[] = [
                'id'         => $fileId,
                'userId'     => $userId,
                'path'       => $path,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        ListFileSuggestion::query()->insertOrIgnore($suggestionQuery);

        Toast::info(__('Your ListFile suggestions were saved'));

        return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_OVERVIEW_KEY);
    }
}
