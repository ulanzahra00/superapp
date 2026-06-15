@extends('layouts.public', ['title' => 'Berita '.$publicSchool->name])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-extrabold text-brand">Berita Sekolah</p>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-ocean">Informasi resmi {{ $publicSchool->name }}.</h1>
            <p class="mt-3 max-w-2xl text-slate-600">Berita ini tampil sesuai sekolah aktif dan dapat diakses publik tanpa login.</p>
        </div>
        <a href="{{ route('school.login', ['schoolSlug' => $publicSchool->public_slug]) }}" class="btn-primary px-4 py-3 text-sm">Login Portal</a>
    </div>

    <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($news as $item)
            <article class="surface overflow-hidden rounded-2xl">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-36 w-full object-cover">
                @else
                    <div class="h-36 bg-{{ $item->cover_color }}-500"></div>
                @endif
                <div class="p-5">
                    <p class="text-sm font-extrabold text-brand">{{ $item->category }}</p>
                    <h2 class="mt-2 text-xl font-extrabold text-ocean">{{ $item->title }}</h2>
                    <p class="mt-2 text-justify text-sm leading-6 text-slate-600">{{ $item->excerpt }}</p>
                    <div class="mt-4 flex items-center justify-between gap-3 text-xs font-bold text-slate-500">
                        <span>{{ optional($item->published_at)->format('d M Y') }}</span>
                        <a href="{{ route('public.school.news.show', array_merge($schoolQuery, ['news' => $item])) }}" class="btn-link text-sm">Baca</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $news->links() }}</div>
</main>
@endsection
