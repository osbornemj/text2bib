<div>

    <div wire:loading.delay.longer wire:target="submit">
        <div class="flex justify-center items-center bg-black-300 dark:bg-zinc-300 fixed z-50 inset-y-0 w-[54rem] opacity-75">
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

    @if ($conversionExists)
    
        @include('livewire.partials.bibtex-output')

    @else

        @include('livewire.partials.file-upload-form')

    @endif

</div>
