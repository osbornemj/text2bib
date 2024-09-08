<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Total numbers since 2024.3.15') }}
        </h2>
    </x-slot>

    <div class="mx-4">
        Registered users: {{ number_format($userCount) }}
        <br/>
        Files converted: {{ number_format($conversionCount) }}
        <br/>
        Items converted: {{ number_format($itemCount) }}
        {{--
        <br/>
        Types detected in items converted:
        <x-list>
        @foreach ($itemTypeCounts as $itemTypeCount)
            <li class="ml-5">{{ $itemTypeCount->name}}: {{ $itemTypeCount->item_type_count}}</li>
        @endforeach
        </x-list>
        --}}
    </div>

    <h2 class="mx-4 mt-2 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Usage statistics by day') }}
    </h2>

    <div class="mx-4 mt-2 font-semibold">
        <h2 class="text-lg">Users</h2>

        <div style="width:100%;">
            {!! $chartjsUsers->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-lg">Conversions</h2>

        <div style="width:100%;">
            {!! $chartjsConversions->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-lg">Items converted</h2>

        <div style="width:100%;">
            {!! $chartjsItems->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-xl">Conversions by intended use</h2>
        (since 2024.4.30)

        <div style="width:100%;">
            {!! $chartjsUses->render() !!}
        </div>
    </div>

</x-app-layout>