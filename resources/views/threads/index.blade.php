<x-layout>
    <section>
        <h2 class="mb-3 text-lg font-semibold">スレッド一覧</h2>

        @if ($threads->isEmpty())
            <p class="text-stone-500">まだスレッドがありません。最初のスレッドを立ててみましょう。</p>
        @else
            <ul class="divide-y divide-stone-200 rounded border border-stone-200 bg-white">
                @foreach ($threads as $thread)
                    <li>
                        <a href="{{ route('threads.show', $thread) }}" class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-stone-50">
                            <span class="font-medium">{{ $thread->title }}</span>
                            <span class="shrink-0 text-sm text-stone-500">
                                {{ $thread->posts_count }}件 ・ {{ $thread->updated_at->format('Y/m/d H:i') }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                {{ $threads->links() }}
            </div>
        @endif
    </section>

    <section class="rounded border border-stone-200 bg-white p-4">
        <h2 class="mb-3 text-lg font-semibold">新しいスレッドを立てる</h2>

        <form method="POST" action="{{ route('threads.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium">タイトル</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="100" required
                    class="mt-1 w-full rounded border border-stone-300 px-3 py-2 focus:border-stone-500 focus:outline-none">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <x-post-fields />

            <button type="submit" class="rounded bg-stone-800 px-4 py-2 font-medium text-white hover:bg-stone-700">スレッドを作成</button>
        </form>
    </section>
</x-layout>
