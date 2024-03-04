<div>
    <div wire:loading.delay>
        <div class="flex justify-center items-center bg-black-300 dark:bg-zinc-300 fixed z-50 inset-y-0 w-full opacity-75">
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
  
    <div wire:loading.remove>
        <form method="POST" wire:submit="process()">
        <div class="pt-4">
            <x-primary-button class="ml-0">
                {{ __('Submit') }}
            </x-primary-button>
        </div>
        </form>
    </div>
</div>
