<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Edit item type
        </h2>
        <p>
            Drag and drop fields to re-order them.
        </p>
</x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">

            <div>
                <h3 class="text-xl font-bold">{{ $itemType->name }}</h3>
            </div>

            <div>
                <livewire:item-type-fields 
                    :itemType="$itemType" 
                />
            </div>


            <div class="flex items-center mt-4">
                <x-link href="{{ url('/admin/itemTypes') }}">All item types</x-link>
            </div>

        </div>
    </div>
</x-app-layout>
