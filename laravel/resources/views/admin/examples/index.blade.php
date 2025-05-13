<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Examples
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-2">
        <div class="p-0 sm:p-0 px-4 sm:px-4 sm:rounded-lg">
            <p>
                After making a change in the conversion algorithm, you should check that all the examples are still converted correctly.
            </p>
            <p>
                <x-link href="/admin/runExampleCheck">Check all conversions</x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="/admin/seedExamples">Run seeder</x-link>
            </p>
        </div>
        <div class="px-4">
            <x-link href="{{ url('admin/examplesShow/article') }}">articles</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/book') }}">books</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/incollection') }}">incollections</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/inproceedings') }}">inproceedings</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/phdthesis') }}">phdtheses</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/mastersthesis') }}">masterstheses</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/online') }}">online</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/techreport') }}">techreports</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/examplesShow/unpublished') }}">unpublished</x-link>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6 mt-2">
        <div class="p-0 sm:p-0 px-4 sm:px-4 sm:rounded-lg">
            <p>
                To add an example, edit the file <code>database/seeders/ExampleSeeder.php</code> and then re-seed the <code>examples</code> table by executing <code>php artisan db:seed ExampleSeeder</code>.  The newly-added example will appear at the top of the list on this page.
            </p>
            <p>
                You can also <x-link href="/admin/examples/create">add an example manually</x-link>, but if you do so it will be overwritten the next time you run the <code>ExampleSeeder</code>.  You can also edit any component of an example by clicking on the component in the following list.
            </p>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 pt-2 sm:pt-2 sm:rounded-lg">
                @foreach ($examples as $example)
                    <div class="bg-blue-200 dark:bg-blue-900 mt-4 px-2">
                        {{ $example->id }}.
                        <x-link href="{{ url('admin/examples/' . $example->id . '/edit') }}">{{ $example->source }}</x-link>
                    </div>
                    <div class="bg-sky-200 dark:bg-blue-800 px-2">
                        type: {{ $example->type }}
                        @foreach ($example->fields as $field)
                        <p>
                            {{ $field->name }}: <x-link href="{{ url('admin/exampleFields/' . $field->id . '/editContent') }}">{{ $field->content }}</x-link>
                        </p>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        @php
                            $selectedLanguage = [];
                            $selectedLanguage[$example->language] = true;
                            $selectedCharEncoding = [];
                            $selectedCharEncoding[$example->char_encoding] = true;
                        @endphp
                        <form method="POST" action="{{ url('/admin/runExampleCheck') }}" class="mt-0 space-y-0">
                            @csrf
                            <input type="hidden" id="exampleId" name="exampleId" value="{{ $example->id }}"/>
                            <x-select-input id="report_type" name="report_type" :options="$typeOptions" class="p-2 w-24"></x-select-input>
                            <x-select-input id="char_encoding" name="char_encoding" :options="$utf8Options" :selected="$selectedCharEncoding" class="p-2 w-64"></x-select-input>
                            <x-select-input id="language" name="language" :options="$languageOptions" :selected="$selectedLanguage" class="p-2 w-16"></x-select-input>
                            <x-select-input id="detailsIfCorrect" name="detailsIfCorrect" :options="$detailOptions" class="p-2 w-24"></x-select-input>
                            <x-primary-button class="ml-0">
                                {{ __('Submit') }}
                            </x-primary-button>
                        </form>
                    </div>
                @endforeach
            {{ $examples->links() }}
        </div>
    </div>
</x-app-layout>
