<div>
    @if (in_array($style, ['normal', 'unchecked', 'compact']))
        @if ($firstOutput)
            {{ $firstOutput->source }}

            @if ($firstOutput->detected_encoding != 'UTF-8')
            &nbsp;&bull;&nbsp;
            <span class="text-red-600 dark:text-red-400">
                Detected character encoding: {{ $firstOutput->detected_encoding }}
            </span>
            @endif
            &nbsp;&bull;&nbsp;
            <x-link wire:click="delete" class="text-red-800 dark:text-red-600 cursor-pointer">Delete item</x-link>
        @endif
    @elseif ($style == 'lowercase')
        {{ $conversion->firstLowercaseOutput()?->source }}
    @endif
</div>
