<div>
    @if ($conversion->usable == 1)
        @php
            $color = "bg-slate-400 dark:bg-slate-300";
        @endphp
    @else
        @php
            $color = "bg-red-600 dark:bg-red-600 text-white dark:text-slate-200";
        @endphp
    @endif

    <x-basic-button wire:click="toggleUsable()" class="ml-2 pb-1 pt-1 pl-1 pr-1 text-xs {{ $color }}">
        @if ($conversion->usable == 1)
            {{ __('Usable') }}
        @else
            {{ __('Unusable') }}
        @endif
    </x-basic-button>
</div>
