
@php
    $classes = 'ml-4';
@endphp

<dd {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</dd>