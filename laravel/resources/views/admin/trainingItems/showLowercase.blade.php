<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Training items for which <code>source</code> starts with lowercase letter
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 pt-2 sm:pt-2 sm:rounded-lg">
            <p>
                {{ number_format($trainingItems->total()) }} items found
            </p>
                @foreach ($trainingItems as $trainingItem)
                    <div class="bg-blue-200 dark:bg-blue-900 mt-4 px-2">
                        {{ $trainingItem->id }}.
                        {{ $trainingItem->source }}
                        <x-link href="{{ url('admin/showConversion/' . $trainingItem->conversion_id . '/0/normal/1') }}" target="_blank">conversion</x-link>
                    </div>
                @endforeach
            {{ $trainingItems->links() }}
        </div>
    </div>

</x-app-layout>
