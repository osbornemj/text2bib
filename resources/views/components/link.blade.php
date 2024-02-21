@props(['active'])

@php
$classes = ($active ?? false)
            ? 'items-center px-0 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-base font-normal text-blue-900 dark:text-blue-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'items-center px-0 pt-1 border-b-2 border-transparent text-base font-normal text-blue-800 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-blue-700 dark:focus:text-blue-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>