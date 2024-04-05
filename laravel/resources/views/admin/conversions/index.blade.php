<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Conversions
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <a name="{{ $conversion->id }}"></a>
                    <x-link href="{{ url('/admin/showConversion/' . $conversion->id . '/' . $conversions->currentPage()) }}">Conversion</x-link>
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }}
                    &nbsp;&bull;&nbsp;
                    user
                    @foreach ($conversion->correctnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-300 dark:bg-red-500 @elseif ($key == 1) bg-emerald-300 dark:bg-emerald-500 @elseif ($key == 2) bg-blue-600 @else bg-slate-300 dark:bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    admin
                    @foreach ($conversion->adminCorrectnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-300 dark:bg-red-500 @elseif ($key == 1) bg-emerald-300 dark:bg-emerald-500 @elseif ($key == 2) bg-blue-600 @else bg-slate-300 dark:bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    @if ($conversion->non_utf8_detected)
                        &nbsp;&bull;&nbsp;
                        <span class="text-red-600 dark:text-red-400">Not UTF-8</span>
                    @endif
                    @if ($conversion->is_bibtex)
                        &nbsp;&bull;&nbsp;
                        <span class="text-red-600 dark:text-red-400">BibTeX file</span>
                    @endif
                    &nbsp;&bull;&nbsp;
                    convert:
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id) }}">line sep</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id . '/cr') }}">cr sep</x-link>
                    <form method="post" action="{{ url('/admin/conversion/' . $conversion->id) }}" class="inline-flex">
                        @csrf
                        @method('delete')
                        <x-danger-button class="ml-2 pb-1 pt-1 pl-1 pr-1 text-xs" onclick="return confirm('Are you sure you want to delete this conversion?');">
                            {{ __('Delete') }}
                        </x-danger-button>
                    </form>
                    <br/>
                    <div class="ml-4">
                        @if ($conversion->user)
                            {{ $conversion->user->fullName() }}
                            &nbsp;&bull;&nbsp;
                        @endif
                        {{ $conversion->created_at }}
                        @if ($conversion->version)
                            &nbsp;&bull;&nbsp;
                            {{ $conversion->version }}
                        @endif
                        @if ($conversion->examined_at)
                            <br/>
                            <span class="text-black bg-emerald-500 px-2 text-sm uppercase rounded">Examined {{ $conversion->examined_at }}</span>
                            @if ($conversion->admin_comment)
                                &nbsp;
                                <span class="text-emerald-500">{{ $conversion->admin_comment }}</span>
                                &nbsp;
                            @endif
                        @endif
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
