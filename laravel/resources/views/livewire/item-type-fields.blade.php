<div>
    <div class="mt-2">
        <ul x-sort="$wire.reorder($item, $position)" role="list" class="ml-4">
            @foreach ($itemType->fields as $i => $field)
                <li x-sort:item="{{ $i }}" class="cursor-grab" wire:key="field-{{ $i }}">
                    {{ $field }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
