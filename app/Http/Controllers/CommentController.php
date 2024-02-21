<?php

namespace App\Http\Controllers;

use Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;

use App\Models\Comment;
use App\Models\Thread;
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

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $thread = new Thread;

        return view('threads.create', compact('thread'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThreadRequest $request)
    {
        $request->validated();

        $input = $request->all();

        $thread = Thread::create(['title' => strip_tags($input['title'])]);

        Comment::create(['thread_id' => $thread->id, 'user_id' => Auth::id(), 'content' => strip_tags($input['content'])]);

        return redirect()->route('threads.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    /*
    public function edit(int $id): View
    {
        $thread = Thread::find($id);

        return view('admin.threads.edit', compact('thread'));
    }
    */

    /**
     * Update the specified resource in storage.
     */
    /*
    public function update(UpdateThreadRequest $request, int $id)
    {
        $thread = Thread::find($id);
        $thread->name = $request->name;
        $thread->save();

        return redirect()->route('threads.index');
    }
    */

}
