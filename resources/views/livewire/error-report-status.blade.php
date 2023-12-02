<div>
    <form method="POST" wire:submit="submit()" id="status">
        @csrf
        Status: <x-select-input id="status" name="status" class="mt-1" :options="$statusOptions" wire:model.change="setStatus" />
    </form>
</div>
