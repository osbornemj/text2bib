<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Encoding error') }}
        </h2>
    </x-slot>

    <div class="px-4 space-y-6">
        <div class="sm:p-0 pt-0">
            <p>
                The following {{ count($nonUtf8Entries) > 1 ? 'items contain' : 'item contains' }} at least one character that is not valid utf-8.  (Each such character appears as '?' on this page.) One way to fix the problem is to open the file in <x-link href="https://notepad-plus-plus.org/" target="_blank">Notepad++</x-link>, click on Encoding, check that it is set to UTF-8, and locate and replace the bad characters.
            </p>
            <ul class="mt-4 mb-4">
            @foreach ($nonUtf8Entries as $entry)
                <li class="ml-6 mt-4">{{ $entry }}</li>
            @endforeach
            </ul>
        </div>
    </div>

</x-app-layout>

