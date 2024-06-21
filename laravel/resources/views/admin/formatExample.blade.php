<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Example formatted for Examples Seeder') }}
        </h2>
    </x-slot>

    <div class="px-4 space-y-6">
        <div class="sm:p-0 pt-0">
            <pre>
            [
                'source' => '{{ str_replace(["\'", "'"], ["\\\'", "\'"], $output->source) }}',
                'type' => '{{ $itemType->name }}',
                'bibtex' => [
                    @foreach ($output->item as $name => $content)
'{{ $name }}' => '{{ str_replace(["\'", "'"], ["\\\'", "\'"], $content) }}',
                    @endforeach
]
            ],
            </pre>
        </div>
    </div>
</x-app-layout>
