<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'text2bib') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 max-w-4xl mx-auto antialiased">
        @include('layouts.header')

        <div class="h-screen flex justify-center">
            {{ $slot }}
        </div>
    </body>
</html>
