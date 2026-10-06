@props(['title' => null])

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title.' - ' : '' }}{{ config('app.name', 'Laravel') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto max-w-3xl px-4 py-4">
                <a href="{{ route('threads.index') }}" class="text-xl font-bold">{{ config('app.name', 'Laravel') }} 掲示板</a>
            </div>
        </header>

        <main class="mx-auto max-w-3xl space-y-8 px-4 py-8">
            {{ $slot }}
        </main>
    </body>
</html>
