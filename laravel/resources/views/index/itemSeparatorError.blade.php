<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Item separator error') }}
        </h2>
    </x-slot>

    <div class="px-4 space-y-6">
        <div class="sm:p-0 pt-0">
            <p>
                You have chosen a blank line as the item separator, but the items in your file appear not to be separated by blank lines.  The first entry starts
            </p>
            <ul class="mt-4 ml-6">
                <li>
                    {{ substr($entry, 0, 500) }} ...
                </li>
            </ul>
            <p class="mt-4">
                <x-link href="{{ url('admin/convert/' . $conversionId . '/cr') }}">Re-try with carriage return as item separator</x-link>
            </p>
        </div>
    </div>

</x-app-layout>

