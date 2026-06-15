@extends('layouts.app', ['title' => $news->title, 'eyebrow' => $news->category])

@section('content')
<article class="surface overflow-hidden rounded-2xl">
    @if($news->image_url)
        <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="h-64 w-full object-cover">
    @else
        <div class="h-48 bg-{{ $news->cover_color }}-500"></div>
    @endif
    <div class="mx-auto max-w-3xl p-6">
        <p class="text-sm font-semibold text-slate-500">Ditulis oleh {{ $news->author->name }} pada {{ optional($news->published_at)->format('d M Y') }}</p>
        <h2 class="mt-3 text-3xl font-bold">{{ $news->title }}</h2>
        <p class="mt-4 text-justify text-lg text-slate-600">{{ $news->excerpt }}</p>
        <div class="mt-6 whitespace-pre-line text-justify leading-7 text-slate-700">{{ $news->content }}</div>
    </div>
</article>
@endsection

