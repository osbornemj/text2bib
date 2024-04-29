<x-app-layout>

    <div class="mt-6 px-4 sm:px-4 lg:px-4">
        <x-paragraph class="my-4">
            Before proceeding, please respond to the following {{ Str::plural('message', $requiredResponses) }}.
        </x-paragraph>

        <ul>
            @foreach ($requiredResponses as $requiredResponse)
                <li class="ml-4">
                    @if ($requiredResponse->comment_id)
                        <x-link href="{{ url('comment/' . $requiredResponse->comment->thread->id) }}">Reply to your comment on the thread "{{ $requiredResponse->comment->thread->title }}"</x-link>
                    @elseif ($requiredResponse->error_report_comment_id)
                        <x-link href="{{ url('errorReport/' . $requiredResponse->errorReportComment->errorReport->id) }}">Reply to your error report regarding the conversion of "{{ $requiredResponse->errorReportComment->errorReport->output->source }}"</x-link>
                    @endif
                </li>
            @endforeach
        </ul>


    </div>

</x-app-layout>