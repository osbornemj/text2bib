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
    @else
        <div class="mx-4 mb-2">
            "Waiting" = waiting for the poster of the comment to respond.  Posting a response on a closed thread reopens the thread.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4 border-b mb-1">
        <div class="col-span-6 mb-1">
            <x-link href="{{ url('comments/title') }}">
                @if ($sortBy == 'title')
                    <b>
                @endif
                    Title
                @if ($sortBy == 'title')
                    </b>
                @endif
            </x-link>
        </div>
        <div class="col-span-2 mb-1">
            <x-link href="{{ url('comments/latest') }}">
                @if ($sortBy == 'latest')
                    <b>
                @endif
                Latest
                @if ($sortBy == 'latest')
                    </b>
                @endif
            </x-link>
        </div>
        <div class="col-span-3 mb-1">
            <x-link href="{{ url('comments/poster') }}">
                @if ($sortBy == 'poster')
                    <b>
                @endif
                Poster
                @if ($sortBy == 'poster')
                    </b>
                @endif
            </x-link>
        </div>
        <div class="col-span-1 mb-1">
            <x-link href="{{ url('comments/status') }}">
                @if ($sortBy == 'status')
                <b>
            @endif
                Status
                @if ($sortBy == 'status')
                    </b>
                @endif
            </x-link>
        </div>
    </div>
    @foreach ($threads as $thread)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4">
            <div class="col-span-6">
                <x-link href="{{ url('threads/' . $thread->id) }}">{{ $thread->title }}</x-link> ({{ $thread->comments->count() }})
            </div>
            <div class="col-span-2">
                {{ $thread->comments()->latest()->first()->updated_at->format('Y-m-d') }}
            </div>
            <div class="mt-0 pt-0 col-span-3">
                {{ $thread->comments()->oldest()->first()->user->fullName() }}
            </div>
            <div class="mt-0 pt-0 col-span-1">
                <span class="{{ $thread->status->color() }}">{{ $thread->status->name }}</span>
            </div>
        </div>
    @endforeach

    <div class="sm:mx-4">
        {{ $threads->links() }}
    </div>

</x-app-layout>
