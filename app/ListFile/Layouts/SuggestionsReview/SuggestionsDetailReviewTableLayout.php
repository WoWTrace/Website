<?php

namespace App\ListFile\Layouts\SuggestionsReview;

use App\Common\Screen\TDExtended;
use App\ListFile\Layouts\Index\ListFileTableLayout;
use Erorus\CASC\Manifest\Root;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use function __;

class SuggestionsDetailReviewTableLayout extends Table
{
    /** @inerhitDoc */
    protected $target = 'newSuggestions';

    protected bool $hasCheckbox = false;
    protected bool $showCurrentPath = false;
    protected bool $showLookup = false;

    public static function make(string $target, bool $hasCheckbox = false, bool $showCurrentPath = false, bool $showLookup = false): static
    {
        $table                  = new static();
        $table->target          = $target;
        $table->hasCheckbox     = $hasCheckbox;
        $table->showCurrentPath = $showCurrentPath;
        $table->showLookup      = $showLookup;

        return $table;
    }

    /** @inerhitDoc */
    protected function columns(): array
    {
        $columns = [
            TD::make('id', __('FD ID'))
                ->cantHide()
                ->alignLeft(),
            TD::make('suggestedPath', __('Suggested Path'))
                ->cantHide()
                ->alignLeft(),
        ];

        if ($this->hasCheckbox) {
            array_unshift($columns, TD::make()
                ->cantHide()
                ->width(40)
                ->render(function (Model $model) {
                    return CheckBox::make(sprintf('%s[]', $this->target))
                        ->value($model->id)
                        ->checked(false);
                })
            );
        }

        if ($this->showCurrentPath) {
            $columns[] =
                TD::make('currentPath', __('Current Path'))
                    ->cantHide()
                    ->alignLeft();
        }

        if ($this->showLookup) {
        $columns[] =
            TDExtended::make('calculatedLookup', __('Calculated Lookup'))
                ->cantHide()
                ->alignLeft()
                ->renderColor(static function (Model $model, TDExtended $td): void {
                    if (empty($model->currentLookup)) {
                        return;
                    }

                    $calculatedHash = bin2hex(strrev(Root::jenkins_hashlittle2($model->suggestedPath, true)));
                    if ($model->currentLookup !== $calculatedHash) {
                        $td->backgroundColor(ListFileTableLayout::COLOR_EMPTY);
                    }
                })
                ->render(static function (Model $model) {
                    return bin2hex(strrev(Root::jenkins_hashlittle2($model->suggestedPath, true)));
                });

        $columns[] =
            TD::make('currentLookup', __('Current Lookup'))
                ->cantHide()
                ->alignLeft();
    }

        return $columns;
    }
}
