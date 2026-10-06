<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * Display threads ordered by their most recent activity.
     */
    public function index(): View
    {
        $threads = Thread::query()
            ->withCount('posts')
            ->latest('updated_at')
            ->paginate(20);

        return view('threads.index', ['threads' => $threads]);
    }

    /**
     * Create a thread together with its first post.
     */
    public function store(StoreThreadRequest $request): RedirectResponse
    {
        $thread = DB::transaction(function () use ($request): Thread {
            $thread = Thread::create($request->safe()->only(['title']));
            $thread->posts()->create($request->safe()->only(['name', 'body']));

            return $thread;
        });

        return redirect()->route('threads.show', $thread);
    }

    /**
     * Display a thread and all of its posts.
     */
    public function show(Thread $thread): View
    {
        $posts = $thread->posts()->oldest('id')->get();

        return view('threads.show', ['thread' => $thread, 'posts' => $posts]);
    }
}
