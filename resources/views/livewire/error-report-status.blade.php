<div>
    @php
        $selected[$errorReport->status->value] = true;
    @endphp
    Status: <x-select-input-plain id="status" name="status" class="mt-1 dark:bg-gray-800 dark:border-gray-800 {{ $errorReport->status->color() }}" :options="$statusOptions" :selected="$selected" wire:model.change="status" />
</div>
