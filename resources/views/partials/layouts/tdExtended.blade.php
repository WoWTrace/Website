<?php
$style = [];

if (!empty($width)) {
    $style[] = sprintf('width: %s;', $width);
}

if (!empty($minWidth)) {
    $style[] = sprintf('min-width: %s !important;', $minWidth);
}

if (!empty($backgroundColor)) {
    $style[] = sprintf('background-color: %s;', $backgroundColor);
}

if (!empty($color)) {
    $style[] = sprintf('color: %s;', $color);
}
?>

<td class="text-{{$align}} @if(!$width && !$minWidth) text-truncate @endif"
    data-column="{{ $slug }}" colspan="{{ $colspan }}"
    @empty(!$style)style="{{implode(' ', $style)}}"@endempty
>
    <div>
        @isset($render)
            {!! $value !!}
        @else
            {{ $value }}
        @endisset
    </div>
</td>
