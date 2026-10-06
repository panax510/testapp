<x-layout :title="$thread->title">
    <section>
        <a href="{{ route('threads.index') }}" class="text-sm text-stone-500 hover:underline">← スレッド一覧へ戻る</a>
        <h2 class="mt-2 mb-4 text-2xl font-bold break-words">{{ $thread->title }}</h2>

        <ol class="space-y-3">
            @foreach ($posts as $post)
                <li id="post-{{ $post->id }}" class="rounded border border-stone-200 bg-white p-4">
                    <div class="mb-2 flex flex-wrap items-baseline gap-x-3 text-sm text-stone-500">
                        <span class="font-semibold text-stone-700">{{ $loop->iteration }}</span>
                        <span class="font-semibold text-green-700">{{ $post->display_name }}</span>
                        <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->format('Y/m/d H:i:s') }}</time>
                    </div>
                    <p class="break-words whitespace-pre-wrap">{{ $post->body }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="rounded border border-stone-200 bg-white p-4">
        <h2 class="mb-3 text-lg font-semibold">返信する</h2>

        <form method="POST" action="{{ route('posts.store', $thread) }}" class="space-y-4">
            @csrf

            <x-post-fields />

            <button type="submit" class="rounded bg-stone-800 px-4 py-2 font-medium text-white hover:bg-stone-700">書き込む</button>
        </form>
    </section>
</x-layout>
