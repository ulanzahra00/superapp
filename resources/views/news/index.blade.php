@extends('layouts.app', ['title' => 'Berita Sekolah', 'eyebrow' => 'Informasi resmi dan kegiatan'])

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold">Berita Terbaru</h2>
        <p class="text-sm text-slate-500">Terintegrasi ke dashboard dan mobile bottom navigation.</p>
    </div>
    @if(auth()->user()->hasRole(['admin','guru']))
        <a href="{{ route('news.create') }}" class="btn-primary px-4 py-3 text-sm">Tulis Berita</a>
    @endif
</div>
<div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach($news as $item)
        <article class="surface overflow-hidden rounded-2xl">
            @if($item->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-32 w-full object-cover">
            @else
                <div class="h-32 bg-{{ $item->cover_color }}-500"></div>
            @endif
            <div class="p-5">
                <p class="text-sm font-bold text-brand">{{ $item->category }}</p>
                <h3 class="mt-2 text-lg font-bold">{{ $item->title }}</h3>
                <p class="mt-2 text-justify text-sm text-slate-500">{{ $item->excerpt }}</p>
                <a href="{{ route('news.show', $item) }}" class="mt-4 inline-flex text-sm font-bold text-slate-900">Baca detail</a>
            </div>
        </article>
    @endforeach
</div>
<div class="mt-5">{{ $news->links() }}</div>
@endsection

