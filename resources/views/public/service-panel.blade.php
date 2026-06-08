@extends('layouts.public', ['title' => $serviceMeta['label'].' - Super App Sekolah'])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="surface rounded-2xl p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Panel Layanan</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-ocean">{{ $serviceMeta['label'] }}</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $serviceMeta['description'] }}</p>
            </div>
            <a href="{{ route('public.home') }}" class="btn-soft px-4 py-3 text-sm">Kembali</a>
        </div>

        @if($serviceKey === 'layanan')
            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($latestSanctions as $sanction)
                    @php
                        $violation = optional($sanction->student)->studentPoints->first();
                    @endphp
                    <article class="rounded-xl border border-slate-200/80 bg-white/75 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-extrabold text-ocean">{{ optional($sanction->student)->name ?? 'Siswa tidak ditemukan' }}</h2>
                                <p class="text-sm text-slate-500">{{ optional($sanction->student)->class_name ?? 'Tanpa kelas' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-rose-100 px-3 py-1 text-sm font-bold text-rose-700">{{ $sanction->total_points }} poin</span>
                        </div>
                        <p class="mt-4 text-xs font-extrabold uppercase tracking-wide text-slate-400">Pelanggaran</p>
                        <p class="mt-1 font-bold text-slate-800">{{ optional($violation)->title ?? $sanction->sanction_type }}</p>
                        <p class="mt-4 font-bold text-slate-800">{{ $sanction->sanction_type }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $sanction->note ?: 'Yang harus dilakukan: siswa mengikuti pembinaan sesuai total poin pelanggaran.' }}</p>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-600 md:col-span-2 xl:col-span-3">Belum ada siswa yang terkena sanksi.</p>
                @endforelse
            </div>
        @endif

        @if($serviceKey === 'absensi')
            <div class="mt-6 grid gap-3 sm:grid-cols-4">
                @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alfa' => 'Alfa'] as $status => $label)
                    <div class="rounded-xl border border-slate-200/80 bg-white/70 p-5 shadow-sm">
                        <p class="font-extrabold text-slate-900">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $attendanceSummary[$status] ?? 0 }}</p>
                        <div class="mt-4 border-t border-slate-100 pt-3">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-slate-400">Nama siswa</p>
                            <div class="mt-2 max-h-56 space-y-2 overflow-y-auto pr-1">
                                @forelse($attendanceNames[$status] ?? [] as $name)
                                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">{{ $name }}</p>
                                @empty
                                    <p class="text-sm text-slate-500">Belum ada siswa.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($serviceKey === 'lms')
            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($courses as $course)
                    <article class="rounded-xl border border-slate-200/80 bg-white/70 p-5 shadow-sm">
                        <h2 class="font-extrabold text-ocean">{{ $course->name }}</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">{{ $course->class_name }}</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $course->description ?? 'Belum ada deskripsi materi.' }}</p>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-600">Belum ada data kelas LMS.</p>
                @endforelse
            </div>
        @endif

        @if($serviceKey === 'komunikasi')
            <div class="mt-6 space-y-3">
                @forelse($messages as $message)
                    <article class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-extrabold text-ocean">{{ optional($message->sender)->name ?? 'Pengirim tidak ditemukan' }}</p>
                            <p class="text-xs font-semibold text-slate-400">Untuk {{ optional($message->receiver)->name ?? 'penerima tidak ditemukan' }}</p>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $message->body }}</p>
                    </article>
                @empty
                    <div class="rounded-xl bg-slate-50 p-5 text-sm font-semibold text-slate-500">Belum ada pesan.</div>
                @endforelse
            </div>
        @endif
    </section>
</main>
@endsection
