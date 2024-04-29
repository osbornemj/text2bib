<?php

namespace App\Http\Controllers;

use Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreThreadRequest;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;

use App\Notifications\CommentPosted;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(): View
    {
        $threads = Thread::latest()->paginate();

        return view('threads', compact('threads'));
    }

    public function show($id): View
    {
        $thread = Thread::where('id', $id)
            ->with('comments')
            ->first();

        $opUser = Comment::where('thread_id', $id)->oldest()->first()->user;
        $type = 'comment';
        
        return view('threads.show', compact('thread', 'opUser', 'type'));
    }

    public function create(): View
    {
        $thread = new Thread;

        return view('threads.create', compact('thread'));
    }

    public function store(StoreThreadRequest $request)
    {
        $request->validated();

        $input = $request->all();

        $thread = Thread::create(['title' => strip_tags($input['title'])]);

        $comment = Comment::create(
            [
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'content' => strip_tags($input['content'])
            ]);

        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new CommentPosted($comment));
        }

        return redirect()->route('threads.index');
    }
}
