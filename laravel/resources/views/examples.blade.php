<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Examples
        </h2>
    </x-slot>

    <div class="ml-4 mt-0">
        The algorithm converts all of these examples correctly.
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 pt-2 sm:rounded-lg">
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
                    <div class="bg-sky-200 dark:bg-blue-800 px-2">
                        Settings: Language = {{ $example->language }}
                        &nbsp;&bull;&nbsp;
                        {{ $example->char_encoding == 'utf8' ? 'Convert accented characters to TeX' : 'Leave accented characters as is'}}
                        {{--
                        &nbsp;&bull;&nbsp;
                        Use: {{ $example->use ?: 'latex' }}
                        --}}
                    </div>
                    @endforeach
            {{ $examples->links() }}
        </div>
    </div>
</x-app-layout>
