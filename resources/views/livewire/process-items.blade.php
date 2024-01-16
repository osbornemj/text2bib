<div>

    <form method="POST" wire:submit="process()" method="POST">
    <div class="pt-4">
        <x-primary-button class="ml-0">
            {{ __('Submit') }}
        </x-primary-button>
    </div>
    </form>

    <div id="reportNumberProcessed">
    </div>

    @script
    <script>
        document.getElementById("reportNumberProcessed").innerText = 'Items processed: 0';

        document.addEventListener('livewire:init', () => {
            Livewire.on('update-number-processed', (event) => {
                /*document.getElementById("reportNumberProcessed").innerText = event.numberProcessed;*/
                document.getElementById("reportNumberProcessed").innerText = 'Items processed: 1';
            });
        });
    </script>
    @endscript

</div>
