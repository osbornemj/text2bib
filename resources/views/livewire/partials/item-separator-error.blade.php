<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('Item separator error') }}
    </h2>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                You have chosen a blank line as the item separator, but the items in your file appear not to be separated by blank lines.  The first entry starts
            </p>
            <ul class="mt-4 ml-6">
                <li>
                    {{ substr($entry, 0, 500) }} ...
                </li>
            </ul>
            <p class="mt-4">
                <form method="POST" accept="txt" wire:submit="submit({{ $conversionId }})" class="mt-0 space-y-0">
                    @csrf
                    <div class="pt-4">
                        <x-primary-button wire:click="submit" class="ml-0">
                            {{ __('Resubmit with carriage return as item separator') }}
                        </x-primary-button>
                    </div>
                </form>
            </p>
        </div>
    </div>

</div>

