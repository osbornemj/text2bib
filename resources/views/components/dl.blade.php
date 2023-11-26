@props([''])

@php
$classes = '';
@endphp

<dl {{ $attributes->merge(['class' => $classes]) }}>