<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Training data
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6 mt-2">
        <div class="p-0 sm:p-0 px-4 sm:px-4 sm:rounded-lg">
            <p>
                <x-link href="/admin/trainingItems/clean">Clean all training sources</x-link>
            </p>
            <p>
                <x-link href="/admin/trainingItems/convert">Convert all training sources</x-link>
            </p>
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
                        <x-link href="{{ url('admin/examples/' . $trainingItem->id . '/edit') }}">{{ $trainingItem->source }}</x-link>
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
                            <x-select-input id="language" name="language" :options="$languageOptions" :selected="$selectedLanguage" class="p-2 w-20"></x-select-input>
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
