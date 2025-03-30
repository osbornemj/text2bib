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
        <br/>
        Types detected in items converted:
        <x-list>
        @foreach ($itemTypeCounts as $itemTypeCount)
            <li class="ml-5">
                {{ $itemTypeCount->name}}: {{ number_format($itemTypeCount->item_type_count) }} ({{ number_format(100 * $itemTypeCount->item_type_count / $itemCount) }}%)
            </li>
        @endforeach
        </x-list>
    </div>

    <div class="mx-4 mt-2">
        <p>
            Last 90 days by day: &nbsp;
            <x-link href="{{ url('#users') }}">Users</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('#conversions') }}">Conversions</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('#items') }}">Items converted</x-link>
        </p>
        <p>
            By month: &nbsp;
            <x-link href="{{ url('#usersMonth') }}">Users</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('#conversionsMonth') }}">Conversions</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('#itemsMonth') }}">Items converted</x-link>
        </p>
        <p>
            <x-link href="{{ url('#intendedUse') }}">Intended use</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('#howDiscovered') }}">Discovery method</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('statsBibtex') }}">BibTeX style file usage</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('statsLanguages') }}">Language selection</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('statsCrossref') }}">Crossref usage</x-link>
        </p>
    
    </div>

    <h2 class="mx-4 mt-2 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Usage statistics by day for last 90 days') }}
    </h2>

    <div class="mx-4 mt-2 font-semibold">
        <h2 class="text-lg"><a name="users">Users</a></h2>

        <div style="width:100%;">
            {!! $chartjsUsers->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-lg"><a name="conversions">Conversions</a></h2>

        <div style="width:100%;">
            {!! $chartjsConversions->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-lg"><a name="items">Items converted</a></h2>

        <div style="width:100%;">
            {!! $chartjsItems->render() !!}
        </div>
    </div>

    <h2 class="mx-4 mt-2 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Usage statistics by month') }}
    </h2>

    <div class="mx-4 mt-2 font-semibold">
        <h2 class="text-lg"><a name="usersMonth">Users by month</a></h2>

        <div style="width:100%;">
            {!! $chartjsUsersByMonth->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-2 font-semibold">
        <h2 class="text-lg"><a name="conversionsMonth">Conversions by month</a></h2>

        <div style="width:100%;">
            {!! $chartjsConversionsByMonth->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-2 font-semibold">
        <h2 class="text-lg"><a name="itemsMonth">Items converted by month</a></h2>

        <div style="width:100%;">
            {!! $chartjsItemsByMonth->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-xl"><a name="intendedUse">Conversions by intended use</a></h2>
        (since 2024.4.30)

        <div style="width:100%;">
            {!! $chartjsUses->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        <h2 class="text-xl"><a name="howDiscovered">How have users discovered the site?</a></h2>

        <div style="width:100%;">
            {!! $chartjsSources->render() !!}
        </div>
    </div>

    <div class="mx-4 mt-4 font-semibold">
        &nbsp;
    </div>


</x-app-layout>