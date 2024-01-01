<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Encoding error') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                The item
                <ul class="mt-4 mb-4">
                    <li class="ml-6">{{ $convItem['source'] }}</li>
                </ul>
                or its conversion
                <ul class="mt-4 mb-4">
                @foreach ($convItem['item'] as $item)
                    <li class="ml-6">{{ $item }}</li>
                @endforeach
                </ul>
                contains at least one character that is not valid utf-8.
            </p>
            <p>
                One way to fix the problem is to open the file in Notepad++, click on Encoding and check that it is set to UTF-8, and locate and fix the bad characters.
            </p>
        </div>
    </div>

</x-app-layout>

