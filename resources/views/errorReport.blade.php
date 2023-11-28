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
                Status: {{ $errorReport->status->name }}
            </p>
            <dl>
                <x-dt>Source</x-dt>
                <x-dd>{{ $errorReport->output->source }}</x-dd>
            </dl>

            @if ($errorReport->output->item_type_id == $errorReport->output->rawOutput->item_type_id)
                @php
                    $rawOutput = $errorReport->output->rawOutput;
                    $output = $errorReport->output;
                @endphp
                <dl>
                    <x-dt>Fields with errors</x-dt>
                    <x-dd>
                        @foreach ($output->itemType->fields as $itemField)
                        @php
                            $outputContent = $output->fields->where('item_field_id', $itemField->id)->first()->content;
                            $rawOutputContent = $rawOutput->fields->where('item_field_id', $itemField->id)->first()->content;
                        @endphp
                        <ul class="ml-6">
                            @if ($outputContent != $rawOutputContent)
                            <li><span class="text-red-600">{{ $itemField->name }} = {{ $rawOutputContent }}</span></li>
                            <li><span class="text-green-700">{{ $itemField->name }} = {{ $outputContent }}</span></li>
                            @endif
                        </ul>
                        @endforeach
                    </x-dd>
                </dl>
            @else
                <dl>
                    <x-dt>BibTeX entry created by script</x-dt>
                    <x-dd>
                        @if ($errorReport->output->rawOutput)
                            <span class="text-red-600">{{ '@' }}{{ $errorReport->output->rawOutput->itemType->name }}</span>{
                                <ul class="ml-6">
                                @foreach ($errorReport->output->rawOutput->fields as $field)
                                    <li>{{ $field->itemField->name }} = {{ '{' }}{{ $field->content }}{{ '}' }}</li>
                                @endforeach
                                </ul>
                                }
                        @endif
                    </x-dd>

                    <x-dt>Corrected BibTeX entry</x-dt>
                    <x-dd>
                        <span class="text-green-700">{{ '@' }}{{ $errorReport->output->itemType->name }}</span>{
                            <ul class="ml-6">
                            @foreach ($errorReport->output->fields as $field)
                                <li>{{ $field->itemField->name }} = {{ '{' }}{{ $field->content }}{{ '}' }}</li>
                            @endforeach
                            </ul>
                            }
                    </x-dd>

                    @if ($errorReport->comment)
                        <x-dt>Comment</x-dt>
                        <x-dd>{{ $errorReport->comment }}</x-dd>
                    @endif
                </dl>
            @endif
        </div>

        <div>
            <livewire:error-feedback :errorReportId="$errorReport->id" />
        </div>


    </div>

</x-app-layout>
