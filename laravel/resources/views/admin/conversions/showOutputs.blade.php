<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Outputs
        </h2>
        <x-link href="{{ url('admin/conversions') }}">All conversions</x-link>
    </x-slot>

    <div class="ml-4 -mt-2">
        @include('admin.conversions.searchForm')
    </div>

    <div class="sm:px-0 pt-0 space-y-6">
        <div class="px-4 pt-0 sm:rounded-lg">
        </div>
    </div>

    <div class="px-4 sm:rounded-lg">
        <div class="mt-2">
            {{ number_format($outputs->total()) }} matching {{ Str::plural('output', $outputs) }}
        </div>
        @foreach ($outputs as $i => $output)
            <div class="items-center">
                <div class="mt-4 border-t border-black dark:border-white"></div>
            </div>
            @php
                $conversion = $output->conversion;    
            @endphp
            <div class="mt-4">
                @if ($conversion->user)
                    {{ $conversion->user->fullName() }}
                    &nbsp;&bull;&nbsp;
                @endif
                {{ $conversion->created_at }}
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/downloadSource/' . $conversion->user_file_id) }}">source file</x-link>
                @if ($conversion->version)
                    &nbsp;&bull;&nbsp;
                    code version {{ $conversion->version }}
                @endif
                &nbsp;&bull;&nbsp;
                @if ($conversion->usable)
                    <span class="bg-slate-400 dark:bg-slate-600">usable</span>
                @else
                    <span class="bg-red-400 dark:bg-red-600">unusable</span>
                @endif
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('/admin/showConversion/' . $conversion->id . '/0/normal/1') }}">view</x-link>
                <br/>
                @include('index.partials.settings')

                <div>
                    <livewire:admin-converted-item :output="$output" />
                </div>
            </div>
        @endforeach
        <div class="mt-2">
            {{ $outputs->appends(Request::only(['search_string' => $searchString, 'selectedCorrectness' => $selectedCorrectness, 'selectedAdminCorrectness' => $selectedAdminCorrectness, 'exlude_crossref' => $exclude_crossref, 'cutoff_date' => $cutoffDate]))->links() }}
        </div>
    </div>

</x-app-layout>
