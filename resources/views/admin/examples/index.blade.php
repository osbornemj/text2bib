<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Examples
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-0 sm:p-0 px-4 sm:px-4 sm:rounded-lg">
            <p>
                After making a change in the conversion algorithm, you should check that all the examples are still converted correctly.
            </p>
            <p>
                <x-link href="/admin/runExampleCheck">Check all conversions</x-link>
            </p>
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
                    <div class="bg-sky-500 dark:bg-blue-800 px-2">
                        type: {{ $example->type }}
                        @foreach ($example->fields as $field)
                        <p>
                            {{ $field->name }}: <x-link href="{{ url('admin/exampleFields/' . $field->id . '/editContent') }}">{{ $field->content }}</x-link>
                        </p>
                        @endforeach
                    </div>
                    <div>
                        Check conversion: 
                        <x-link href="{{ url('admin/runExampleCheck/0/0/' . $example->id) }}">brief</x-link>,
                        <x-link href="{{ url('admin/runExampleCheck/1/0/' . $example->id) }}">verbose (convert utf8)</x-link>,
                        <x-link href="{{ url('admin/runExampleCheck/1/1/' . $example->id) }}">show details even if correct</x-link>,
                        <x-link href="{{ url('admin/runExampleCheck/1/0/' . $example->id . '/utf8leave') }}">verbose (leave utf8)</x-link>
                    </div>
                @endforeach
            {{ $examples->links() }}
        </div>
    </div>
</x-app-layout>
