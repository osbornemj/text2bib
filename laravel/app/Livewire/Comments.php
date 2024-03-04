<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\Comment;

use Livewire\Component;
use Livewire\Attributes\Rule;

class Comments extends Component
{
    #[Rule('required', message: 'Please enter a comment')]    
    public $comment;

    public $comments;
    public $threadId;

    public function mount()
    {
        $this->comments = Comment::where('thread_id', $this->threadId)
            ->orderBy('created_at')
            ->get();
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();
        $comment = Comment::create([
            'thread_id' => $this->threadId,
            'user_id' => $user->id,
            'content' => $this->comment,
        ]);

        $this->comment = '';
        $this->comments = $this->comments->push($comment);
    }

}
