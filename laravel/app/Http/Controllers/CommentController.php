<?php

namespace App\Http\Controllers;

use Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreThreadRequest;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;

use App\Notifications\CommentPosted;
use Illuminate\Database\Query\JoinClause;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(string $sortBy = 'status'): View
    {
        if ($sortBy == 'latest') {
            $threads = Thread::with('comments')->orderByDesc('updated_at');
        } elseif ($sortBy == 'title') {
            $threads = Thread::orderBy('title');
        } elseif ($sortBy == 'poster') {
            $threads = Thread::with('comments.user')
                ->select('threads.*')
                ->join('comments', function(JoinClause $join) {
                    $join->on('comments.thread_id', '=', 'threads.id')
                         ->whereRaw('comments.created_at = (select min(created_at) from comments where thread_id = threads.id)');
                })
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->orderBy('users.last_name')
            ->orderBy('users.first_name');
        } elseif ($sortBy == 'status') {
            $threads = Thread::with('comments')->orderBy('status')->orderByDesc('updated_at');
        }

        $threads = $threads->paginate(50);

        return view('threads', compact('threads', 'sortBy'));
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
