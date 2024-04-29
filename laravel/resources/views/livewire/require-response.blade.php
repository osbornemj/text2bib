<div>
    @if ($comment->requiredResponse)
        <span class="font-normal">Response required</span>
        <form method="POST" wire:submit="remove({{ $comment->requiredResponse->id }})" id="remove" class="ml-1 inline">
            @csrf
            <x-small-submit-button class="ml-0">
                {{ __('Remove') }}
            </x-small-submit-button>
        </form>
    @else
        <form method="POST" wire:submit="submit({{ $userId }}, {{ $comment->id }})" id="requireResponse" class="mt-0 mb-1 space-y-0">
            @csrf
            <x-small-submit-button class="ml-0">
                {{ __('Require response') }}
            </x-small-submit-button>
        </form>
    @endif
</div>
