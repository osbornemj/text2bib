<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usage statistics by day') }}
        </h2>
    </x-slot>

    <div class="sm:mx-4 font-semibold">
        <h2 class="text-lg">Users</h2>

        <div style="width:100%;">
            {!! $chartjsUsers->render() !!}
        </div>
    </div>

    <div class="sm:mx-4 mt-4 font-semibold">
        <h2 class="text-lg">Conversions</h2>

        <div style="width:100%;">
            {!! $chartjsConversions->render() !!}
        </div>
    </div>

    <div class="sm:mx-4 mt-4 font-semibold">
        <h2 class="text-lg">Items converted</h2>

        <div style="width:100%;">
            {!! $chartjsItems->render() !!}
        </div>
    </div>

    <div class="sm:mx-4 mt-4 font-semibold">
        <h2 class="text-xl">Conversions by intended use</h2>

        <div style="width:100%;">
            {!! $chartjsUses->render() !!}
        </div>
    </div>

</x-app-layout>