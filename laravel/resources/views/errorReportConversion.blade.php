<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Conversion report
        </h2>
    </x-slot>

    <div class="px-4 sm:rounded-lg">
        <ul>
        @foreach ($result['details'] as $details)
            <li>
                @include('index.partials.conversionDetails')
            </li>
        @endforeach
        </ul>
    </div>
</x-app-layout>
