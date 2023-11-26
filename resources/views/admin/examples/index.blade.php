<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Examples
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-0 sm:p-0 pl-4 sm:pl-4 sm:rounded-lg">
            <div class="max-w-xl">
                <x-link href="/admin/examples/create">Add example</x-link>
            </div>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-0 sm:p-0 pl-4 sm:pl-4 sm:rounded-lg">
            <div class="max-w-xl">
                <x-link href="/admin/runExampleCheck">Check all conversions</x-link>
            </div>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 pt-2 sm:pt-2 sm:rounded-lg">
                @foreach ($examples as $example)
                    <div class="bg-blue-200 dark:bg-blue-900 mt-4 px-2">
                        <x-link href="{{ url('admin/examples/' . $example->id . '/edit') }}">{{ $example->source }}</x-link>
                    </div>
                    <div class="bg-sky-500 dark:bg-blue-700 px-2">
                        type: {{ $example->type }}
                        @foreach ($example->fields as $field)
                        <p>
                            {{ $field->name }}: <x-link href="{{ url('admin/exampleFields/' . $field->id . '/editContent') }}">{{ $field->content }}</x-link>
                        </p>
                        @endforeach
                    </div>
                    <div>
                        Check conversion: <x-link href="{{ url('admin/runExampleCheck/0/' . $example->id) }}">brief</x-link>, <x-link href="{{ url('admin/runExampleCheck/1/' . $example->id) }}">verbose</x-link>
                    </div>
                @endforeach
            {{ $examples->links() }}
        </div>
    </div>
</x-app-layout>
