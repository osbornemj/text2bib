<div>
    <button wire:click="loadBst({{ $bst->id }})" class="text-blue-800 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
        {{ $bst->name }}
    </button>

    @if ($showModal)
        <div wire:click.away="$set('showModal', false)" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" wire:loading.remove>
            <div class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 p-2 rounded-lg shadow-lg w-1/2">
                <h2 class="text-xl font-bold mb-4">{{ $bst->name }}</h2>
                <div class="mb-0">
                    @include('index.partials.bstProperties')
                </div>
                <button wire:click="$set('showModal', false)" class="mt-2 px-2 py-2 bg-blue-500 text-white rounded">Close</button>
            </div>
        </div>
    @endif
</div>