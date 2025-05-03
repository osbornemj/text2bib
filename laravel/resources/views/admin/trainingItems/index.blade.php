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
                    Delete all existing <code>training items</code> and copy new ones from <code>source</code> and <code>language</code> fields in <code>outputs</code> and <code>conversions</code> tables that (1)&nbsp;are at least 35 characters long, (2)&nbsp;are at most 1,000 characters long, (3)&nbsp;do not begin with "__" or "--" or "—", and (4)&nbsp;contain at least 4 spaces, after which you will need to convert all the training items again:  <x-link href="/admin/trainingItems/copy" onclick="return confirm('Are you sure you want to delete all the existing training items and create new ones?');">copy and clean</x-link>.
                </li>
                <li>
                    <x-link href="/admin/trainingItems/showLowercase">Show all items for which source starts with lowercase letter</x-link>
                </li>
                <li>
                    <x-link href="/admin/trainingItems/convert">Convert all training sources</x-link>
                </li>
            <ul>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 pt-2 sm:pt-2 sm:rounded-lg">
            <p>
                {{ number_format($trainingItems->total()) }} items found
            </p>
                @foreach ($trainingItems as $trainingItem)
                    <div class="bg-blue-200 dark:bg-blue-900 mt-4 px-2">
                        {{ $trainingItem->id }}.
                        <x-link href="{{ url('admin/trainingItems/' . $trainingItem->id . '/edit') }}">{{ $trainingItem->source }}</x-link>
                    </div>
                    <div class="bg-sky-200 dark:bg-blue-800 px-2">
                        @if ($trainingItem->type)
                            type: {{ $trainingItem->type }}
                        @endif
                        @if ($trainingItem->item)
                            @foreach ($trainingItem->item as $name => $content)
                            <p>
                                {{ $name }}: {{ $content }}
                            </p>
                            @endforeach
                        @endif
                    </div>
                    <div class="mt-2">
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
                    </div>
                @endforeach
            {{ $trainingItems->links() }}
        </div>
    </div>

</x-app-layout>
