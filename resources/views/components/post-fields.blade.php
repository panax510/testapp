<div>
    <label for="name" class="block text-sm font-medium">名前（省略可）</label>
    <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="50" placeholder="{{ \App\Models\Post::ANONYMOUS_NAME }}"
        class="mt-1 w-full rounded border border-stone-300 px-3 py-2 focus:border-stone-500 focus:outline-none">
    @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="body" class="block text-sm font-medium">本文</label>
    <textarea id="body" name="body" rows="5" maxlength="2000" required
        class="mt-1 w-full rounded border border-stone-300 px-3 py-2 focus:border-stone-500 focus:outline-none">{{ old('body') }}</textarea>
    @error('body')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
