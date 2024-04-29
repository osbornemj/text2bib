<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Error report') }}
        </h2>
    </x-slot>

    <div class="ml-4 max-w-7xl space-y-6">
        <div class="mt-0">
            <h3 class="font-bold text-sky-800 dark:text-sky-600">{{ $errorReport->title }}</h3>
            <p>
                Submitted by {{ $errorReport->output->conversion->user->fullName() }} {{ $errorReport->created_at }}
            </p>
            <p>
                @if (Auth::user()->is_admin)
                    <div>
                        <livewire:error-report-status :errorReport="$errorReport" />
                    </div>
                @else
                    Status: <span class="{{ $errorReport->status->color() }}">{{ $errorReport->status->name }}</span>
                @endif
            </p>
            @if (Auth::user()->is_admin)
                <div class="mt-4">
                    <x-link href="{{ url('admin/formatExample/' . $errorReport->output->id)}}" target="_blank">Format for Examples Seeder</x-link>
                </div>
            @endif
            <dl>
                <x-dt>Source</x-dt>
                <x-dd>
                    {{ $errorReport->output->source }}
                    @if (Auth::user()->is_admin)
                        <br/>
                        <x-link href="{{ url('convertErrorSource/' . $errorReport->id) }}" target="_blank">Convert (verbose)</x-link> (in new tab/window)
                    @endif
                </x-dd>
            </dl>

            @php
                $rawOutput = $errorReport->output->rawOutput;
                $output = $errorReport->output;
            @endphp
            @if ($errorReport->output->item_type_id == $errorReport->output->rawOutput->item_type_id)
                <dl>
                    <x-dt>BibTeX fields</x-dt>
                    <x-dd>
                        <span>{{ '@' }}{{ $output->itemType->name }}</span>{{ '{' }}
                            @foreach ($output->itemType->fields as $fieldName)
                            @php
                                $outputContent = ($output->item)[$fieldName] ?? null;
                                $rawOutputContent = ($rawOutput->item)[$fieldName] ?? null;
                            @endphp
                            <ul class="ml-4">
                                @if ($outputContent != $rawOutputContent)
                                    <li>{{ $fieldName }} = <span class="text-red-600">{{ $rawOutputContent ?: '[null]' }}</span> &nbsp;&rarr;&nbsp; <span class="text-green-700">{{ $outputContent }}</span></li>
                                @elseif ($rawOutputContent)
                                    <li>{{ $fieldName }} = {{ $rawOutputContent }}</li>
                                @endif
                            </ul>
                        @endforeach
                        {{ '}' }}
                    </x-dd>
                </dl>
            @else
                <dl>
                    <x-dt>BibTeX entry created by script</x-dt>
                    <x-dd>
                        @if ($rawOutput)
                            <span class="text-red-600">{{ '@' }}{{ $rawOutput->itemType->name }}</span>{
                                <ul class="ml-4">
                                @foreach ($rawOutput->item as $name => $field)
                                    <li>{{ $name }} = {{ '{' }}{{ $field }}{{ '}' }}</li>
                                @endforeach
                                </ul>
                                {{ '}' }}
                        @endif
                    </x-dd>

                    <x-dt>Corrected BibTeX entry</x-dt>
                    <x-dd>
                        <span class="text-green-700">{{ '@' }}{{ $output->itemType->name }}</span>{
                            <ul class="ml-4">
                            @foreach ($output->item as $name => $field)
                                <li>{{ $name }} = {{ '{' }}{{ $field }}{{ '}' }}</li>
                            @endforeach
                            </ul>
                            {{ '}' }}
                    </x-dd>

                    @if ($errorReport->comment)
                        <x-dt>Comment</x-dt>
                        <x-dd>{{ $errorReport->comment }}</x-dd>
                    @endif
                </dl>
            @endif
        </div>

        <div>
            <livewire:error-feedback :errorReportId="$errorReport->id" :opUser="$opUser" type="errorReport" />
        </div>


    </div>

</x-app-layout>
