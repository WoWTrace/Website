<?php

namespace App\ListFile\Screens;

use App\Common\Screen\Screen;
use App\ListFile\Layouts\Index\ListFileAddLayout;
use App\ListFile\Layouts\Index\ListFileTableLayout;
use App\ListFile\Platform as ListFilePlatform;
use App\Models\ListFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ListFileScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'Listfile';

    /** @inerhitDoc */
    public $description;

    public function __construct()
    {
        $this->description = __('A list of all ListFile entries');
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'listfile' => ListFile::query()
                ->filters()
                ->defaultSort('id')
                ->paginate(25)
        ];
    }

    /** @inerhitDoc */
    public function commandBar(): array
    {
        return [
            Link::make(__('Review suggestions'))
                ->icon('eyeglasses')
                ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGESTIONS_REVIEW_KEY)
                ->canSee(Auth::user()?->hasAccess('listfile.reviewSuggestions') ?? false),
            Link::make(__('Suggest names'))
                ->icon('cloud-upload')
                ->route(ListFilePlatform::ROUTE_LISTFILE_SUGGEST_KEY)
                ->canSee(Auth::user()?->hasAccess('listfile.suggest') ?? false),
            ModalToggle::make(__('Add'))
                ->icon('plus')
                ->modal('oneAsyncModal')
                ->modalTitle(__('Add new ListFile entry'))
                ->method('addListFileEntry')
                ->canSee(Auth::user()?->hasAccess('listfile.add') ?? false),
            Link::make(__('Download'))
                ->icon('save')
                ->route('web.listfile.download')
                ->download(),
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        return [
            ListFileTableLayout::class,
            Layout::modal('oneAsyncModal', ListFileAddLayout::class)
                ->applyButton('Save')
                ->closeButton('Close')
                ->canSee(Auth::user()?->hasAccess('listfile.add') ?? false),
        ];
    }

    //<editor-fold desc="Actions">
    public function remove(ListFile $listFile): RedirectResponse
    {
        if (!Auth::user()?->hasAccess('listfile.delete')) {
            return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_OVERVIEW_KEY);
        }

        $listFile->delete();
        Toast::info(__('ListFile entry was removed'));
        return redirect()->route(ListFilePlatform::ROUTE_LISTFILE_OVERVIEW_KEY);
    }

    public function addListFileEntry(Request $request): void
    {
        if (!Auth::user()?->hasAccess('listfile.add')) {
            return;
        }

        $request->validate([
            'listfile.id'   => 'int|required|unique:listfile,id',
            'listfile.path' => 'string|required|unique:listfile,path',
        ]);

        $id   = $request->input('listfile.id');
        $path = $request->input('listfile.path');

        ListFile::query()->insertOrIgnore([
            'id'        => $id,
            'path'      => $path,
            'type'      => pathinfo($path, PATHINFO_EXTENSION),
            'userId'    => Auth::id()
        ]);

        Toast::info(__('New ListFile entry added.'));
    }
    //</editor-fold>
}
