<div>
    @php
        $selected[$thread->status->value] = true;
    @endphp
    Status: <x-select-input-plain id="status" name="status" class="mt-1 border-transparent dark:bg-gray-800 dark:border-gray-800 {{ $thread->status->color() }}" :options="$statusOptions" :selected="$selected" wire:model.change="status" />
</div>
