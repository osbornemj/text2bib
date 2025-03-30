<div>
    <button wire:click="loadBst({{ $bst->id }})" class="text-blue-500">
        {{ $bst->name }}
    </button>

    @if ($showModal)
        <div wire:click.away="$set('showModal', false)" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" wire:loading.remove>
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h2 class="text-xl font-bold mb-4">bst file details</h2>
                @if ($bst)
                    <p><strong>Name:</strong> {{ $bst->name }}</p>
                    <p><strong>Type:</strong> {{ $bst->type }}</p>
                @endif
                <button wire:click="$set('showModal', false)" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">Close</button>
            </div>
        </div>
    @endif
</div>
