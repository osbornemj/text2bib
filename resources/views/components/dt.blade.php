
@php
    $classes = 'font-bold mt-4';
@endphp

<dt {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</dt>