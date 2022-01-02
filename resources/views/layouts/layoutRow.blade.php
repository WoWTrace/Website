<fieldset class="mb-3">
    @isset($title)
        <legend class="text-black mt-3 ms-3">
            @if(isset($icon))
                <x-orchid-icon path="{{ $icon }}" class="large me-2 mb-1"/>
            @else
                <span class="text-muted font-weight-bold">|</span>
            @endif
            {{ $title }}
        </legend>
    @endempty

    <div class="p-3 pt-0">
        @foreach($manyForms as $key => $column)
            @foreach($column as $item)
                {!! $item ?? '' !!}
            @endforeach
        @endforeach
    </div>
</fieldset>
