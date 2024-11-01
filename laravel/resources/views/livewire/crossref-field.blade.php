<div>
    @if (isset($convertedItem['item']->$field))
        <li>
            <span class="text-gray-800 dark:text-gray-200">{{ $field }} = {{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }}</span>,
        </li>
    @endif
    <li>
        <x-checkbox-input value="cr" wire:model="checked" wire:click="addCrossrefField()" class="peer/cr" /> 
        &nbsp; 
        <span class="text-orange-800 dark:text-orange-300">{{ $field }} = {{ '{' }}{{ $convertedItem['crossref_item'][$field] }}{{ '}' }}</span>            
    </li>
</div>
