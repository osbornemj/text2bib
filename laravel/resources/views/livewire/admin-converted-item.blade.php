<div>
    <div class="mt-4">
        <x-link href="{{ url('admin/formatExample/' . $output->id)}}" target="_blank">Format for Examples Seeder</x-link>
    </div>
    <div class="mt-2">
        {{ $output->source }}
    </div>
    <div class="mt-2">

        @if ($output->correctness == -1)
            @if ($output->rawOutput)
                <span class="bg-blue-600">Corrected</span>
            @else
                <x-basic-button wire:click="setCorrectness(1)" class="bg-red-500 dark:bg-red-400">
                {{ __('Incorrect') }}
            </x-basic-button>
            @endif
        @elseif ($output->correctness == 1) 
            <x-basic-button wire:click="setCorrectness(-1)" class="bg-emerald-600 dark:bg-emerald-300">
                {{ __('Correct') }}
            </x-basic-button>
        @else
            <x-basic-button wire:click="setCorrectness(1)" class="bg-slate-600 dark:bg-slate-300">
                {{ __('Correct') }}
            </x-basic-button>
            <x-basic-button wire:click="setCorrectness(-1)" class="bg-slate-600 dark:bg-slate-300">
                {{ __('Incorrect') }}
            </x-basic-button>
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
