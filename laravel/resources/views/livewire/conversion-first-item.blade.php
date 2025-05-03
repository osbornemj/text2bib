<div>
    @if ($style == 'compact')
        {{ $conversion->firstOutput()?->source }}

        @if ($conversion->firstOutput())
            <x-link wire:click="delete()" class="pl-2 text-red-800 dark:text-red-400 cursor-pointer">Delete item</x-link>
        @endif
    @elseif ($style == 'lowercase')
        {{ $conversion->firstLowercaseOutput()?->source }}

        @if ($conversion->firstLowercaseOutput())
            <x-link wire:click="delete()" class="pl-2 text-red-800 dark:text-red-400 cursor-pointer">Delete item</x-link>
        @endif
    @endif
</div>
