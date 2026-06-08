@extends('layouts.public', ['title' => $news->title])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <article class="surface overflow-hidden rounded-2xl">
        <div class="h-56 bg-{{ $news->cover_color }}-500"></div>
        <div class="mx-auto max-w-3xl p-6 sm:p-8">
            <p class="text-sm font-extrabold text-brand">{{ $news->category }}</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-tight tracking-tight text-ocean">{{ $news->title }}</h1>
            <p class="mt-3 text-sm font-bold text-slate-500">Dipublikasikan {{ optional($news->published_at)->format('d M Y') }} oleh {{ $news->author->name }}</p>
            <p class="mt-6 text-xl leading-8 text-slate-600">{{ $news->excerpt }}</p>
            <div class="mt-7 whitespace-pre-line leading-8 text-slate-700">{{ $news->content }}</div>
        </div>
    </article>

    <section class="mt-8">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-extrabold text-ocean">Berita Lainnya</h2>
            <a href="{{ route('public.news') }}" class="btn-soft px-4 py-2 text-sm">Kembali ke Berita</a>
        </div>
        <div class="mt-5 grid gap-4 md:grid-cols-3">
            @foreach($relatedNews as $item)
                <a href="{{ route('public.news.show', $item) }}" class="surface rounded-xl p-5">
                    <p class="text-sm font-extrabold text-brand">{{ $item->category }}</p>
                    <p class="mt-2 font-extrabold text-ocean">{{ $item->title }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $item->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </section>
</main>
@endsection
