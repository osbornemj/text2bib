<x-app-layout>

    <div class="sm:px-0 lg:px-0 pt-4 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            @foreach ($outputs as $output)
                <div class="mt-4">
                    {{ $output->source }}
                </div>
                <div class="mt-4">
                    {{ '@' }}{{ $output->itemType->name }}{{ '{' }}{{ $output->label }},
                    @foreach ($output->item as $name => $content)
                        <div class="ml-6">
                            {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
                        </div>
                    @endforeach
                    }
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
