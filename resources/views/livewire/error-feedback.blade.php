<div>

    <dl class="mb-6">
        @foreach ($comments as $comment)
        <x-dt>{{ $comment->user->fullName() }} posted {{ $comment->created_at }}</x-dt>
        <x-dd>{{ $comment->comment_text }}</x-dd>
        @endforeach        
    </dl>

    <form method="POST" wire:submit="submit({{ $errorReportId }})" id="comment">
        @csrf

        <x-input-label for="comment" value="Comment" />
        <x-textarea-input rows="5" id="comment" class="block mt-1 w-full" name="comment" value="" wire:model="comment"/>
        <div role="alert" class="mt-4 mb-4">
            @error('comment') <span class="bg-red-500 text-white font-bold rounded px-2 py-1">{{ $message }}</span> @enderror 
        </div>

        <x-primary-button class="ml-0 mt-3">
            {{ __('Submit') }}
        </x-primary-button>

    </form>                                            

</div>
