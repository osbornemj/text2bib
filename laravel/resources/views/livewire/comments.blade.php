<div>

    <div class="ml-4">

        <dl class="mb-6">
            @foreach ($comments as $comment)
                <x-dt>
                    {{ $comment->user->fullName() }} posted {{ $comment->created_at }}
                    @if ($loop->last && $comment->user->is_admin && Auth::user()->is_admin)
                        <div>
                            <livewire:require-response 
                                :comment="$comment"
                                :userId="$opUser->id" 
                                :type="$type"
                            />
                        </div>
                    @endif
                </x-dt>
                <x-dd><p style="white-space: pre-line;">{{ $comment->content }}</p></x-dd>
            @endforeach        
        </dl>

        <form method="POST" wire:submit="submit()" id="comment">
            @csrf

            <x-input-label for="comment" value="Response" />
            <x-textarea-input rows="10" id="comment" class="block mt-1 w-full" name="comment" value="" wire:model="comment"/>
            <div role="alert" class="mt-4 mb-4">
                @error('comment') <span class="text-red-500">{{ $message }}</span> @enderror 
            </div>

            <x-primary-button class="ml-0 mt-3">
                {{ __('Submit') }}
            </x-primary-button>

        </form>                                            
    </div>
</div>
