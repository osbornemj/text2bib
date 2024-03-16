<div>
    <x-link href="{{ url('admin/journals/' . $journal->id . '/edit') }}">{{ $journal->name }}</x-link>

    <x-small-button class="ml-1 bg-red-400 dark:bg-red-800" wire:click="delete">
        X
    </x-small-button>

    @if ($journal->checked)
        <x-small-button class="ml-1 bg-green-600 dark:bg-green-700" wire:click="check(0)">
            &check;
        </x-small-button>
    @else
        <x-small-button class="ml-1 bg-slate-300 dark:bg-slate-500" wire:click="check(1)">
            &check;
        </x-small-button>
    @endif

    @if ($journal->distinctive)
        <x-small-button class="ml-1 bg-purple-600 dark:bg-purple-800" wire:click="distinctive(0)">
            &nbsp;&nbsp;
        </x-small-button>
    @else
        <x-small-button class="ml-1 bg-slate-300 dark:bg-slate-500" wire:click="distinctive(1)">
            &nbsp;&nbsp;
        </x-small-button>
    @endif

</div>
