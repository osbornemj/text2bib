<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Comments') }}
        </h2>
    </x-slot>

    <div class="mx-4 mb-2">
        <x-link href="threads/create">Post comment</x-link>
    </div>

    @if (!count($threads))
        <div class="mx-4">
            None so far.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4 border-b mb-1">
        <div class="col-span-10 mb-1">
            Topic
        </div>
        <div class="col-span-2 text-right mb-1">
            Latest comment
        </div>
    </div>
    @foreach ($threads as $thread)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4">
            <div class="col-span-10">
                <x-link href="{{ url('threads/' . $thread->id) }}">{{ $thread->title }}</x-link> ({{ $thread->comments->count() }})
            </div>
            <div class="col-span-2 text-right">
                {{ $thread->comments->last()->created_at->format('Y-m-d') }}
            </div>
        </div>
    @endforeach

    <div class="sm:mx-4">
        {{ $threads->links() }}
    </div>

</x-app-layout>
