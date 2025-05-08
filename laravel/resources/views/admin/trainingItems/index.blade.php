<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Training data
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6 mt-2">
        <div class="p-0 sm:p-0 px-4 sm:px-4 sm:rounded-lg">
            <ul class="ml-4 list-disc">
                <li>
                    Delete all existing <code>training items</code> and enter ones from <code>outputs</code> table for which associated <code>conversion</code> has been checked and is marked as 'usable' and <code>source</code> field (1)&nbsp;is at least 35 characters long, (2)&nbsp;is at most 1,000 characters long, (3)&nbsp;does not begin with "__" or "--" or "—", (4)&nbsp;contains at least 4 spaces, and (5)&nbsp; has <code>detected_encoding</code> UTF-8: <x-link href="/admin/trainingItems/copy" onclick="return confirm('Are you sure you want to delete all the existing training items and create new ones?');">delete existing items and enter new ones</x-link>.
                </li>
                <li>
                    <x-link href="/admin/trainingItems/showLowercase">Show only items for which source starts with lowercase letter</x-link>
                </li>
                <li>
                    <x-link href="/admin/trainingItems/convert">Convert all items using current version of Converter</x-link> (to view progress, open main Admin page in new tab)
                </li>
                <li>
                    <x-link href="/admin/trainingItems/selectAndFormat">Select 6,000 random items, format as json, and download</x-link>
                </li>
            <ul>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 pt-2 sm:pt-2 sm:rounded-lg">
            <p>
                {{ number_format($trainingItems->total()) }} items 
                @if ($type == 'lowercase')
                    for which source starts with lowercase letter
                @endif
            </p>
            @if ($type == 'lowercase')
                <p>
                    <x-link href="/admin/trainingItems">Show all items</x-link>
                </p>
            @endif
            @foreach ($trainingItems as $trainingItem)
                <div class="bg-blue-200 dark:bg-blue-900 mt-4 px-2">
                    {{ $trainingItem->id }}.
                    {{--<x-link href="{{ url('admin/trainingItems/' . $trainingItem->id . '/edit') }}">--}}
                    {{ $trainingItem->output->source }}
                    {{--</x-link>--}}
                    [<x-link href="{{ url('admin/outputs/' . $trainingItem->output_id . '/edit') }}">edit & re-convert</x-link>]
                    [<x-link href="{{ url('admin/showConversion/' . $trainingItem->output->conversion_id . '/0/normal/1#' . $trainingItem->output_id) }}" target="_blank">conversion</x-link>]
                </div>
                <div class="bg-sky-200 dark:bg-blue-800 px-2">
                    @if ($trainingItem->output->itemType)
                        type: {{ $trainingItem->output->itemType->name }}
                    @endif
                    @if ($trainingItem->output->item)
                        @foreach ($trainingItem->output->item as $name => $content)
                        <p>
                            {{ $name }}: {{ $content }}
                        </p>
                        @endforeach
                    @endif
                    <p>
                        language: {{ $trainingItem->output->conversion->language }}
                    </p>
                </div>
                <div class="mt-2">
                    {{--
                    @php
                        $selectedLanguage = [];
                        $selectedLanguage[$trainingItem->language] = true;
                    @endphp
                    <form method="POST" action="{{ url('/admin/runExampleCheck') }}" class="mt-0 space-y-0">
                        @csrf
                        <input type="hidden" id="trainingItemId" name="trainingItemId" value="{{ $trainingItem->id }}"/>
                        <x-select-input id="language" name="language" :options="$languageOptions" :selected="$selectedLanguage" class="p-2 w-24"></x-select-input>
                        <x-primary-button class="ml-0">
                            {{ __('Submit') }}
                        </x-primary-button>
                    </form>
                    --}}
                </div>
            @endforeach
            {{ $trainingItems->links() }}
        </div>
    </div>

</x-app-layout>
