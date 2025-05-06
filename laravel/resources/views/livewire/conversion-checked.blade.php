<div>
    @if ($conversion->id > $maxCheckedConversionId)
        @php
            $color = "";
        @endphp
    @else
        @php
            $color = "text-white dark:text-slate-200";
        @endphp
    @endif

    <x-link wire:click="setMaxChecked()" class="{{ $color }} cursor-pointer">
        @if ($conversion->id > $maxCheckedConversionId)
            {{ __('Set as most recent checked conversion') }}
        @else
            {{ __('Checked') }}
        @endif
    </x-link>
</div>
