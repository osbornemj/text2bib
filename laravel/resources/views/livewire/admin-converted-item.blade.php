<div>
    <div class="mt-4">
        <x-link href="{{ url('admin/formatExample/' . $output->id)}}" target="_blank">Format for Examples Seeder</x-link>
    </div>
    <div class="mt-2">
        {{ $output->source }}
    </div>
    <div class="mt-2">

        User:
        @if ($output->correctness == -1)
            @if ($output->rawOutput)
                <span class="bg-blue-600">Corrected</span>
            @endif
        @elseif ($output->correctness == 1) 
            <span class="bg-emerald-600 dark:bg-emerald-300">Correct</span>
        @else
            <span class="bg-slate-600 dark:bg-slate-600">Unrated</span>
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
            <x-medium-button wire:click="setCorrectness(1)" class="bg-slate-600 dark:bg-slate-300">
                {{ __('Correct') }}
            </x-medium-button>
            <x-medium-button wire:click="setCorrectness(-1)" class="bg-slate-600 dark:bg-slate-300">
                {{ __('Incorrect') }}
            </x-medium-button>
        @endif

        <br/>
        
        {{ '@' }}{{ $output->itemType->name }}{{ '{' }}{{ $output->label }},
        @foreach ($convertedItem as $name => $content)
            <div class="ml-6">
                {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
            </div>
        @endforeach
        {{ '}' }}
    </div>

    @if ($output->rawOutput)
        <div class="mt-2">
            @if ($output->correctness == -1) 
                <span class="bg-red-500 dark:bg-red-400">Original</span>
            @endif

            {{ '@' }}{{ $output->rawOutput->itemType->name }}{{ '{' }}{{ $output->rawOutput->label }},
            @foreach ($originalItem as $name => $content)
                <div class="ml-6">
                    {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
                </div>
            @endforeach
        {{ '}' }}
        </div>
    @endif
</div>
