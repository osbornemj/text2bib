<div>
    <div class="flex items-center mt-0">
        Click to rate the quality of this conversion:
        <div class="flex items-center ml-2">

            @for ($i = 0; $i < 5; $i++)
                <svg wire:click="setRating({{ $i+1 }})" class="w-4 h-4 fill-current @if ($i < $this->rating) text-yellow-600 @else text-gray-400 @endif"
                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z">
                    </path>
                </svg>
            @endfor

        </div>
    </div>
    <p>
        Your rating will be visible only to administrators of the system.  It will be used to find conversions that can be improved.  
    </p>
</div>
