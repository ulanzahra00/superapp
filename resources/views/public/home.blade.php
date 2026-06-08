@extends('layouts.public', ['title' => 'Super App Sekolah - Website Resmi'])

@section('content')
<main>
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(245,158,11,.20),transparent_28%),radial-gradient(circle_at_80%_10%,rgba(14,116,144,.22),transparent_30%)]"></div>
        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-20">
            <div class="flex flex-col justify-center">
                <p class="inline-flex w-fit rounded-full bg-white/75 px-4 py-2 text-sm font-extrabold text-brand shadow-soft ring-1 ring-teal-100">Website publik + portal digital sekolah</p>
                <h1 class="mt-6 max-w-4xl text-4xl font-extrabold leading-tight tracking-tight text-ocean sm:text-6xl">Sekolah modern dengan layanan digital terpadu.</h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">Akses berita sekolah untuk publik, sementara siswa, orang tua, guru, dan admin tetap memiliki portal aman untuk absensi, LMS, dan komunikasi internal.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.news') }}" class="btn-primary px-5 py-3">Lihat Berita Sekolah</a>
                    <a href="{{ route('login') }}" class="btn-soft px-5 py-3">Masuk Portal</a>
                </div>
            </div>
            <div class="surface rounded-2xl p-5">
                <div class="rounded-2xl bg-gradient-to-br from-ocean via-cyan-800 to-brand p-6 text-white">
                    <p class="text-sm font-bold text-emerald-100">Monitoring real-time</p>
                    <h2 class="mt-3 text-2xl font-extrabold">Portal Layanan Sekolah</h2>
                    <p class="mt-3 text-sm leading-6 text-cyan-50">Absensi, LMS, dan komunikasi internal tersaji dalam panel khusus agar warga sekolah dapat membuka data sesuai kebutuhan.</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-white/15">
                            <p class="text-2xl font-extrabold">{{ $stats['students'] }}</p>
                            <p class="text-xs text-cyan-100">Siswa</p>
                        </div>
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-white/15">
                            <p class="text-2xl font-extrabold">{{ $stats['teachers'] }}</p>
                            <p class="text-xs text-cyan-100">Guru</p>
                        </div>
                        <div class="rounded-xl bg-white/12 p-4 ring-1 ring-white/15">
                            <p class="text-2xl font-extrabold">{{ $stats['news'] }}</p>
                            <p class="text-xs text-cyan-100">Berita</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="surface rounded-2xl p-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-extrabold text-brand">Siswa Kena Sanksi</p>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-ocean">Pelanggaran siswa dan yang harus dilakukan.</h2>
                </div>
                <a href="{{ route('login') }}" class="btn-soft px-4 py-3 text-sm">Masuk Portal</a>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($latestSanctions as $sanction)
                    @php
                        $violation = optional($sanction->student)->studentPoints->first();
                    @endphp
                    <article class="rounded-xl border border-slate-200/80 bg-white/75 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-extrabold text-ocean">{{ optional($sanction->student)->name ?? 'Siswa tidak ditemukan' }}</h3>
                                <p class="text-sm text-slate-500">{{ optional($sanction->student)->class_name ?? 'Tanpa kelas' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-rose-100 px-3 py-1 text-sm font-bold text-rose-700">{{ $sanction->total_points }} poin</span>
                        </div>
                        <p class="mt-4 text-xs font-extrabold uppercase tracking-wide text-slate-400">Pelanggaran</p>
                        <p class="mt-1 font-bold text-slate-800">{{ optional($violation)->title ?? $sanction->sanction_type }}</p>
                        <p class="mt-4 font-bold text-slate-800">{{ $sanction->sanction_type }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $sanction->note ?: 'Yang harus dilakukan: siswa mengikuti pembinaan sesuai total poin pelanggaran.' }}</p>
                        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $sanction->created_at->format('d M Y') }}</p>
                    </article>
                @empty
                    <p class="rounded-xl border border-slate-200/80 bg-white/75 p-5 text-sm text-slate-600 md:col-span-2 xl:col-span-3">Belum ada siswa yang terkena sanksi.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Berita Sekolah</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-ocean">Informasi terbaru untuk publik.</h2>
            </div>
            <a href="{{ route('public.news') }}" class="btn-soft px-4 py-3 text-sm">Semua Berita</a>
        </div>
        <div class="mt-6 grid gap-5 lg:grid-cols-3">
            @forelse($featuredNews as $item)
                <article class="surface overflow-hidden rounded-2xl">
                    <div class="h-36 bg-{{ $item->cover_color }}-500"></div>
                    <div class="p-5">
                        <p class="text-sm font-extrabold text-brand">{{ $item->category }}</p>
                        <h3 class="mt-2 text-xl font-extrabold text-ocean">{{ $item->title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item->excerpt }}</p>
                        <a href="{{ route('public.news.show', $item) }}" class="btn-link mt-4 text-sm">Baca berita</a>
                    </div>
                </article>
            @empty
                <p class="surface rounded-xl p-5 text-sm text-slate-600">Belum ada berita yang dipublikasikan.</p>
            @endforelse
        </div>
    </section>
</main>
@endsection
