<x-app-layout>
    @if (in_array($style, ['normal', 'unchecked']))
        <x-slot name="header">
            <h2 class="font-semibold text-xl leading-tight">
                Conversions
            </h2>
            @if ($style == 'unchecked')
                <p>
                    Unchecked
                </p>
            @elseif ($user)
                <p>
                    by {{ $user->fullName() }}
                </p>
            @endif
            @if ($style == 'unchecked' || $user)
                <p>
                    <x-link href="{{ url('/admin/conversions') }}">Show all</x-link>
                </p>
            @elseif ($style == 'normal')
            <p>
                <x-link href="{{ url('admin/conversions/0/unchecked') }}">Show only unchecked</x-link>
            </p>
            @endif
            <p>
                {{ $conversions->total() }} found
            </p>
        </x-slot>
    @endif

    @if (in_array($style, ['normal', 'unchecked']))
        <div class="m-4 -mt-2">
            @include('admin.conversions.searchForm')
        </div>
    @endif

    <div class="m-4">
        For each conversion, the first item is shown.  Click on the item count to see all items.
        @if ($style == 'lowercase')
            A conversion is listed if its <code>usable</code> field is 1 and it has at least one <code>output</code> for which the <code>source</code> field starts with a lowercase letter and does not start with 'van ', 'von ', or several other von names.
        @endif
    </div>

    <div class="sm:px-0 lg:px-0 mb-4 mt-2 pb-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <a name="{{ $conversion->id }}"></a>
                    <x-link href="{{ url('/admin/showConversion/' . $conversion->id . '/' . $userId . '/' . $style . '/' . $conversions->currentPage()) }}">{{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }}</x-link>
                    @if ($conversion->user)
                        &nbsp;&bull;&nbsp;
                        <x-link href="{{ url('/admin/conversions/' . $conversion->user->id) }}">{{ $conversion->user->fullName() }}</x-link>
                    @endif

                    @if ($conversion->outputs_count && $style != 'lowercase')

                        &nbsp;&bull;&nbsp;
                        user

                        <div class="inline-flex gap-1">
                            <x-correctness-badge :count="$conversion->correctness_minus1_count" class="bg-red-300 dark:bg-red-500" />
                            <x-correctness-badge :count="$conversion->correctness_0_count" />
                            <x-correctness-badge :count="$conversion->correctness_1_count" class="bg-emerald-300 dark:bg-emerald-500" />
                            <x-correctness-badge :count="$conversion->correctness_2_count" class="bg-blue-300 dark:bg-blue-600" />
                        </div>

                        &nbsp;&bull;&nbsp;
                        admin

                        <div class="inline-flex gap-1">
                            <x-correctness-badge :count="$conversion->admin_correctness_minus1_count" class="bg-red-300 dark:bg-red-500" />
                            <x-correctness-badge :count="$conversion->admin_correctness_0_count" />
                            <x-correctness-badge :count="$conversion->admin_correctness_1_count" class="bg-emerald-300 dark:bg-emerald-500" />
                        </div>

                    @endif

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
                    @if (in_array($style, ['normal', 'unchecked']))
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
                                    <span class="text-yellow-600">crossref {{ $conversion->crossref_count }}</span>
                                @endif
                                @if ($conversion->crossref_cache_count)
                                    &nbsp;&bull;&nbsp;
                                    cache {{ $conversion->crossref_cache_count }}
                                @endif
                                @if ($conversion->crossref_quota_remaining)
                                    &nbsp;&bull;&nbsp;
                                    quota remaining {{ $conversion->crossref_quota_remaining }}
                                @endif
                                &nbsp;&bull;&nbsp;
                                <div class="inline-flex">
                                    <livewire:conversion-checked :conversion="$conversion" :maxCheckedConversionId="$maxCheckedConversionId" />
                                </div>
                            @endif
                        </div>
                    @endif
                    @if ($conversion->outputs_count)
                        @php
                            $currentPage = $conversions->currentPage();
                        @endphp
                        <div class="ml-4">
                            <div class="inline-flex">
                                <livewire:conversion-first-item :conversion="$conversion" :style="$style" :currentPage="$currentPage" />
                            </div>
                        </div>
                    @endif
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
