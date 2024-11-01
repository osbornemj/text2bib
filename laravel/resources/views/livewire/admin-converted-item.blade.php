<div>
    <div class="mt-4">
        <x-link href="{{ url('admin/formatExample/' . $output->id)}}" target="_blank">Format for Examples Seeder</x-link>
    </div>
    <div class="mt-2">
        {{ $output->source }}
    </div>
    <div class="mt-2">

        User:
        @if ($output->correctness == 2)
            <span class="bg-blue-600 dark:bg-blue-600">Corrected</span>
        @elseif ($output->correctness == 1) 
            <span class="bg-emerald-600 dark:bg-emerald-300">Correct</span>
        @else
            <span class="bg-slate-300 dark:bg-slate-600">Unrated</span>
        @endif

        <br/>

        Admin:
        @if ($output->admin_correctness == -1)
            <x-medium-button wire:click="setCorrectness(1)" class="bg-red-500 dark:bg-red-400">
                {{ __('Incorrect') }}
            </x-medium-button>
        @elseif ($output->admin_correctness == 1) 
            <x-medium-button wire:click="setCorrectness(-1)" class="p-0 bg-emerald-600 dark:bg-emerald-300">
                {{ __('Correct') }}
            </x-medium-button>
        @else
            <x-medium-button wire:click="setCorrectness(1)" class="bg-slate-400 dark:bg-slate-300">
                {{ __('Correct') }}
            </x-medium-button>
            <x-medium-button wire:click="setCorrectness(-1)" class="bg-slate-400 dark:bg-slate-300">
                {{ __('Incorrect') }}
            </x-medium-button>
        @endif

        <br/>

        Author pattern:
        @if ($output->author_pattern === null)
            <span class="bg-red-500">none</span>
        @else
            {{ $output->author_pattern }}
        @endif

        <br/>
        
        @if (isset($output['crossref_item_type']) && $output->itemType->name != $output['crossref_item_type'])
        <p>
            Crossref says that the type of this item is <code>{{ $output['crossref_item_type']}}</code>, not <code>{{ $output->itemType->name }}</code>.
        </p>
        @endif

        <code>{{ '@' }}{{ $output->itemType->name }}</code>{{ '{' }}{{ $output->label }},
        @foreach ($convertedItem as $name => $content)
            <div class="ml-6">
                <code>{{ $name }}</code> = {{ '{' }}{{ $content }}{{ '}' }},
            </div>
            @if (! isset($originalItem[$name]) || $originalItem[$name] != $convertedItem[$name])
                <div class="ml-10">
                    original: 
                    @if (! isset($originalItem[$name]))
                        not set
                    @else
                        <span class="text-slate-600 dark:text-slate-400">{{ $originalItem[$name] }}</span>
                    @endif
                </div>
            @endif
            @if (isset($crossrefItem[$name]) && $crossrefItem[$name] != $convertedItem[$name])
                <div class="ml-10">
                    crossref: <span class="text-orange-800 dark:text-orange-300">{{ $crossrefItem[$name] }}</span>
                </div>
            @endif
        @endforeach
        {{ '}' }}
    </div>
</div>
