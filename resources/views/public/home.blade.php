@extends('layouts.public', ['title' => ($publicSchool->name ?? 'Super App Sekolah').' - Website Resmi'])

@section('content')
<main>
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(245,158,11,.20),transparent_28%),radial-gradient(circle_at_80%_10%,rgba(14,116,144,.22),transparent_30%)]"></div>
        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-20">
            <div class="flex flex-col justify-center">
                <p class="inline-flex w-fit rounded-full bg-white/75 px-4 py-2 text-sm font-extrabold text-brand shadow-soft ring-1 ring-teal-100">Website publik + portal digital sekolah</p>
                <h1 class="mt-6 max-w-4xl text-4xl font-extrabold leading-tight tracking-tight text-ocean sm:text-6xl">{{ $publicSchool->name }}</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Akses berita, layanan sekolah, absensi, LMS, dan komunikasi internal yang terhubung khusus dengan data {{ $publicSchool->name }}.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.school.news', $schoolQuery) }}" class="btn-primary px-5 py-3">Lihat Berita Sekolah</a>
                    <a href="{{ route('school.login', ['schoolSlug' => $publicSchool->public_slug]) }}" class="btn-soft px-5 py-3">Masuk Portal</a>
                </div>
            </div>
            <div class="surface rounded-2xl p-5">
                <div class="rounded-2xl bg-gradient-to-br from-[#006B3F] via-[#00923F] to-[#8DC63F] p-6 text-white">
                    <p class="text-sm font-bold text-lime-100">Monitoring real-time</p>
                    <h2 class="mt-3 text-2xl font-extrabold">Portal Layanan Sekolah</h2>
                    <p class="mt-3 text-sm leading-6 text-green-50">Absensi, LMS, dan komunikasi internal tersaji dalam panel khusus agar warga sekolah dapat membuka data sesuai kebutuhan.</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-lime-100/25">
                            <p class="text-2xl font-extrabold">{{ $stats['students'] }}</p>
                            <p class="text-xs text-lime-100">Siswa</p>
                        </div>
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-lime-100/25">
                            <p class="text-2xl font-extrabold">{{ $stats['teachers'] }}</p>
                            <p class="text-xs text-lime-100">Guru</p>
                        </div>
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-lime-100/25">
                            <p class="text-2xl font-extrabold">{{ $stats['news'] }}</p>
                            <p class="text-xs text-lime-100">Berita</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Berita Sekolah</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-ocean">Informasi terbaru untuk publik.</h2>
            </div>
            <a href="{{ route('public.school.news', $schoolQuery) }}" class="btn-soft px-4 py-3 text-sm">Semua Berita</a>
        </div>
        <div class="mt-6 grid gap-5 lg:grid-cols-3">
            @forelse($featuredNews as $item)
                <article class="surface overflow-hidden rounded-2xl">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-36 w-full object-cover">
                    @else
                        <div class="h-36 bg-{{ $item->cover_color }}-500"></div>
                    @endif
                    <div class="p-5">
                        <p class="text-sm font-extrabold text-brand">{{ $item->category }}</p>
                        <h3 class="mt-2 text-xl font-extrabold text-ocean">{{ $item->title }}</h3>
                        <p class="mt-2 text-justify text-sm leading-6 text-slate-600">{{ $item->excerpt }}</p>
                        <a href="{{ route('public.school.news.show', array_merge($schoolQuery, ['news' => $item])) }}" class="btn-link mt-4 text-sm">Baca berita</a>
                    </div>
                </article>
            @empty
                <p class="surface rounded-xl p-5 text-sm text-slate-600">Belum ada berita yang dipublikasikan.</p>
            @endforelse
        </div>
    </section>
</main>
@endsection
