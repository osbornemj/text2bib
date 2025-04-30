<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Conversions
        </h2>
        @if ($user)
        <p>
            by {{ $user->fullName() }}
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('/admin/conversions') }}">Show all</x-link>
        </p>
        @endif
        <p>
            {{ $conversions->total() }} found
        </p>
    </x-slot>

    <div class="m-4 -mt-2">
        @include('admin.conversions.searchForm')
    </div>

    <div class="sm:px-0 lg:px-0 mb-4 pb-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <a name="{{ $conversion->id }}"></a>
                    <x-link href="{{ url('/admin/showConversion/' . $conversion->id . '/' . $conversions->currentPage()) }}">{{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }}</x-link>
                    &nbsp;&bull;&nbsp;
                    @if ($conversion->user)
                        <x-link href="{{ url('/admin/conversions/' . $conversion->user->id) }}">{{ $conversion->user->fullName() }}</x-link>
                        &nbsp;&bull;&nbsp;
                    @endif
                    user
                    @foreach ($conversion->correctnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-300 dark:bg-red-500 @elseif ($key == 1) bg-emerald-300 dark:bg-emerald-500 @elseif ($key == 2) bg-blue-600 @else bg-slate-300 dark:bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    admin
                    @foreach ($conversion->adminCorrectnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-300 dark:bg-red-500 @elseif ($key == 1) bg-emerald-300 dark:bg-emerald-500 @elseif ($key == 2) bg-blue-600 @else bg-slate-300 dark:bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->created_at }}

                    @if ($conversion->language != 'en')
                        &nbsp;&bull;&nbsp;
                        <span class="text-emerald-500">language: {{ $conversion->language }}</span>
                    @endif
                    @if ($conversion->non_utf8_detected)
                        &nbsp;&bull;&nbsp;
                        <span class="text-red-600 dark:text-red-400">Not UTF-8</span>
                    @endif
                    @if ($conversion->file_error)
                        &nbsp;&bull;&nbsp;
                        <span class="text-red-600 dark:text-red-400">{{ $conversion->file_error }} file</span>
                    @endif
                    {{--
                    &nbsp;&bull;&nbsp;
                    convert:
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id) }}">line sep</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id . '/cr') }}">cr sep</x-link>
                    --}}
                    <form method="post" action="{{ url('/admin/conversion/' . $conversion->id) }}" class="inline-flex">
                        @csrf
                        @method('delete')
                        <x-danger-button class="ml-2 pb-1 pt-1 pl-1 pr-1 text-xs" onclick="return confirm('Are you sure you want to delete this conversion?');">
                            {{ __('Delete') }}
                        </x-danger-button>
                    </form>
                    <div class="inline-flex">
                        <livewire:conversion-usability :conversion="$conversion" />
                    </div>
                    <div class="ml-4 dark:text-slate-400">
                        @if ($conversion->version)
                            v. {{ $conversion->version }}
                        @endif
                        @if ($conversion->use)
                            &nbsp;&bull;&nbsp;
                            use: 
                            {{ $conversion->use }}
                            @if ($conversion->use == 'latex')
                                @if ($conversion->bst)
                                    (<code>{{ $conversion->bst->name }}</code>)
                                @endif
                            @elseif ($conversion->use == 'other')
                                ({{ $conversion->other_use }})
                            @endif
                        @endif
                        @if ($conversion->crossref_count || $conversion->crossref_cache_count || $conversion->crossref_quota_remaining)
                            @if ($conversion->crossref_count)
                                &nbsp;&bull;&nbsp;
                                <span class="text-yellow">{{ $conversion->crossref_count }}</span>
                            @endif
                            @if ($conversion->crossref_cache_count)
                                &nbsp;&bull;&nbsp;
                                cache {{ $conversion->crossref_cache_count }}
                            @endif
                            @if ($conversion->crossref_quota_remaining)
                                &nbsp;&bull;&nbsp;
                                quota remaining {{ $conversion->crossref_quota_remaining }}
                            @endif
                        @endif
                    </div>
                    <div class="ml-4">
                        {{ $conversion->firstOutput()?->source }}
                    </div>
                    @if ($conversion->examined_at)
                        <div class="ml-4">
                        <span class="text-black bg-emerald-500 px-2 text-sm uppercase rounded">Examined {{ $conversion->examined_at }}</span>
                        @if ($conversion->admin_comment)
                            &nbsp;
                            <span class="text-emerald-500">{{ $conversion->admin_comment }}</span>
                            &nbsp;
                        @endif
                        </div>
                    @endif
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
