<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    /**
     * Add a reply to the given thread.
     */
    public function store(StorePostRequest $request, Thread $thread): RedirectResponse
    {
        $post = $thread->posts()->create($request->safe()->only(['name', 'body']));

        return redirect()->to(route('threads.show', $thread).'#post-'.$post->id);
    }
}
