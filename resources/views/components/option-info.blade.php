@props(['value'])

<div {{ $attributes->merge(['class' => 'hidden mt-2 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</div>
