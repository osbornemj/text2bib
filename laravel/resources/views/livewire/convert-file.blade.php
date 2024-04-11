<div>

    <div wire:loading.delay.longer wire:target="submit">
        <div class="flex justify-center items-center bg-zinc-600 dark:bg-zinc-300 fixed z-50 inset-y-0 w-[54rem] opacity-75">
            <div class="la-ball-spin-clockwise-fade la-3x">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @if ($itemSeparatorError)

        @include('livewire.partials.item-separator-error')

    @elseif ($this->fileError)

        @include('livewire.partials.file-error')

    @elseif (count($this->unknownEncodingEntries))

        @include('livewire.partials.non-utf8-entries')

    @elseif ($conversionExists)
    
        @include('livewire.partials.bibtex-output')

    @else

        @include('livewire.partials.file-upload-form')

    @endif

</div>

