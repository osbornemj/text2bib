<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;

use App\Notifications\CommentPosted;
use App\Notifications\CommentResponsePosted;

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

        // Notify admins
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            if ($admin->id != $user->id) {
                $admin->notify(new CommentPosted($comment));
            }
        }

        // Notify OP
        $firstComment = Comment::where('thread_id', $this->threadId)->oldest()->first();
        $opUser = $firstComment->user;
        if ($user->id != $opUser->id) {
            $opUser->notify(new CommentResponsePosted($this->threadId));
        }

        $this->comment = '';
        $this->comments = $this->comments->push($comment);
    }

}
