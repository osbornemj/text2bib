<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use App\Models\RequiredResponse;
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
    public $thread;
    public $opUser;
    public $type;

    public function mount()
    {
        $this->comments = Comment::where('thread_id', $this->thread->id)
            ->orderBy('created_at')
            ->get();
    }

    public function submit()
    {
        $this->validate();

        $commentIds = Thread::find($this->thread->id)->comments->pluck('id');

        $user = Auth::user();
        $comment = Comment::create([
            'thread_id' => $this->thread->id,
            'user_id' => $user->id,
            'content' => $this->comment,
        ]);

        // Change status to Open
        $this->thread->update(['status' => 1]);

        // Delete required response, if there is one
        RequiredResponse::where('user_id', $user->id)->whereIn('comment_id', $commentIds)->first()?->delete();

        // Notify admins
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            if ($admin->id != $user->id) {
                $admin->notify(new CommentPosted($comment));
            }
        }

        // Notify OP
        $firstComment = Comment::where('thread_id', $this->thread->id)->oldest()->first();
        $opUser = $firstComment->user;
        if ($user->id != $opUser->id) {
            $opUser->notify(new CommentResponsePosted($this->thread->id, $comment->id));
        }

        $this->comment = '';
        $this->comments = $this->comments->push($comment);
    }

}
