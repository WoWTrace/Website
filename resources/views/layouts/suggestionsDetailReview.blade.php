</form>
<script type="application/javascript">
    function suggestionsDetailReviewCheckAll(checkBoxName) {
        let inputs = document.querySelectorAll("input[type='checkbox'][name='" + checkBoxName + "']");
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].checked = !inputs[i].checked;
        }
    }
</script>

<form class="m-t-md"
      role="form"
      method="POST"
      data-controller="form"
      data-action="form#submit"
      data-form-button-animate=".button-submit"
      data-form-button-text="{{ __('Submitting...') }}"
      action="{{ route('platform.listfile.suggestions.review.detail', ['suggestionKey' => $repository['suggestionKey'], 'method' => 'submit']) }}">
    @csrf

    @if(!($repository['nameSuggestions']?->isEmpty() ?? true))
        <div class="row align-items-center">
            <div class="col-md-6">
                <legend class="text-black mt-3 ms-3">
                    <x-orchid-icon path="flag" class="large me-2 mb-1"/>
                    <b>New naming</b>
                </legend>
            </div>
            <div class="col-md-6">
                <a id="button-login" type="submit" class="btn btn-outline-secondary btn-block w-sm pull-right mb-2"
                   onclick="suggestionsDetailReviewCheckAll('nameSuggestions[]')">
                    <x-orchid-icon path="check" class="large me-2"/>
                    {{__('Select all')}}
                </a>
            </div>
        </div>
        {!! App\ListFile\Layouts\SuggestionsReview\SuggestionsDetailReviewTableLayout::make('nameSuggestions', true)->build($repository) !!}
    @endif

    @if(!($repository['overrideSuggestions']?->isEmpty() ?? true))
        <div class="row align-items-center">
            <div class="col-md-6">
                <legend class="text-black mt-3 ms-3">
                    <x-orchid-icon path="pencil" class="large me-2 mb-1"/>
                    <b>Overwritten naming</b>
                </legend>
            </div>
            <div class="col-md-6">
                <a id="button-login" type="submit" class="btn btn-outline-secondary btn-block w-sm pull-right mb-2"
                   onclick="suggestionsDetailReviewCheckAll('overrideSuggestions[]')">
                    <x-orchid-icon path="check" class="large me-2"/>
                    {{__('Select all')}}
                </a>
            </div>
        </div>
        {!! App\ListFile\Layouts\SuggestionsReview\SuggestionsDetailReviewTableLayout::make('overrideSuggestions', true, true, true)->build($repository) !!}
    @endif

    @if(!($repository['newSuggestions']?->isEmpty() ?? true))
        <div class="row align-items-center">
            <div class="col-md-6">
                <legend class="text-black mt-3 ms-3">
                    <x-orchid-icon path="plus" class="large me-2 mb-1"/>
                    <b>Untracked entries</b>
                    <i class="small">(Unshipped files or currently no versions available)</i>
                </legend>
            </div>
            <div class="col-md-6">
                <a id="button-login" type="submit" class="btn btn-outline-secondary btn-block w-sm pull-right mb-2"
                   onclick="suggestionsDetailReviewCheckAll('newSuggestions[]')">
                    <x-orchid-icon path="check" class="large me-2"/>
                    {{__('Select all')}}
                </a>
            </div>
        </div>
        {!! App\ListFile\Layouts\SuggestionsReview\SuggestionsDetailReviewTableLayout::make('newSuggestions', true)->build($repository) !!}
    @endif

    @if(!($repository['skipSuggestions']?->isEmpty() ?? true))
        <legend class="text-black mt-3 ms-3">
            <x-orchid-icon path="close" class="large me-2 mb-1"/>
            <b>Skipped entries</b>
            <i class="small">(Verified entries cannot be overwritten)</i>
        </legend>
        {!! App\ListFile\Layouts\SuggestionsReview\SuggestionsDetailReviewTableLayout::make('skipSuggestions', false, true)->build($repository) !!}
    @endif

    @if(!($repository['nameSuggestions']?->isEmpty() ?? true) || !($repository['overrideSuggestions']?->isEmpty() ?? true) || !($repository['newSuggestions']?->isEmpty() ?? true))
    <div class="row align-items-center mt-5 mb-5">
        <div class="col-md-6 col-xs-12 mb-2">
            <b>{{ __('Once you have reviewed all the suggested ListFile entries, you can accept or reject them.') }}</b>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="row align-items-end">
                <div class="col-md-3 col-xs-12">
                    <a class="btn btn-outline-warning btn-block"
                       href="{{ route('platform.listfile.suggestions.review') }}">
                        <x-orchid-icon path="action-undo" class="large me-2"/>
                        {{__('Go back')}}
                    </a>
                </div>
                <div class="col-md-3 col-xs-12">
                    <button type="submit" class="button-submit btn btn-danger btn-block" name="type" value="reject">
                        <x-orchid-icon path="close" class="large me-2"/>
                        {{__('Reject')}}
                    </button>
                </div>
                <div class="col-md-3 col-xs-12">
                    <button type="submit" class="button-submit btn btn-success btn-block" name="type" value="accept">
                        <x-orchid-icon path="check" class="large me-2"/>
                        {{__('Accept')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
        <h1>👀 {{__('There seems to be nothing more to do here.')}}</h1>
        <br>
        <a class="btn btn-outline-warning pull-left"
           href="{{ route('platform.listfile.suggestions.review') }}">
            <x-orchid-icon path="action-undo" class="large me-2"/>
            {{__('Go back')}}
        </a>
    @endif

