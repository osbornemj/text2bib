<div>
    {{ $conversion->firstOutput()?->source }}

    <x-link wire:click="delete()" class="ml-2 text-red-800 dark:text-red-400 cursor-pointer">Delete item</x-link>
</div>
