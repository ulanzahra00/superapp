@extends('layouts.app', ['title' => 'Verifikasi Sekolah', 'eyebrow' => 'Kontrol pendaftaran sekolah'])

@section('content')
<div class="space-y-6">
    <section class="surface rounded-2xl p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-brand">Menunggu persetujuan</p>
                <h2 class="text-2xl font-extrabold text-ocean">{{ $pendingSchools->count() }} sekolah pending</h2>
            </div>
            <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-extrabold text-amber-800">
                Verifikasi sebelum aktif
            </span>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-teal-100 bg-white/75">
            @forelse($pendingSchools as $school)
                @php
                    $admin = $school->users->first();
                @endphp
                <div class="grid gap-4 border-b border-teal-100 px-4 py-4 last:border-b-0 lg:grid-cols-[1.3fr_1fr_auto] lg:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-extrabold text-ink">{{ $school->name }}</h3>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-700">Pending</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $school->address }}</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                            <span class="rounded-full bg-slate-100 px-3 py-1">NPSN: {{ $school->npsn }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">Telp: {{ $school->phone }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">{{ $school->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="rounded-xl bg-emerald-50 px-4 py-3">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Admin pendaftar</p>
                        <p class="mt-1 font-extrabold text-ocean">{{ optional($admin)->name ?? 'Belum ada admin' }}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-600">{{ optional($admin)->email ?? $school->email }}</p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row lg:flex-col">
                        <form method="post" action="{{ route('admin.schools.approve', $school) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn-primary w-full px-4 py-3 text-sm">Setujui</button>
                        </form>
                        <form method="post" action="{{ route('admin.schools.reject', $school) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn-soft w-full px-4 py-3 text-sm text-rose-700">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm font-bold text-slate-500">
                    Belum ada pendaftaran sekolah yang menunggu persetujuan.
                </div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="surface rounded-2xl p-6">
            <h2 class="text-xl font-extrabold text-ocean">Sekolah aktif</h2>
            <p class="mt-1 text-sm font-semibold text-slate-500">Sekolah aktif tampil di login dan halaman publik sekolah.</p>
            <div class="mt-4 space-y-3">
                @forelse($activeSchools as $school)
                    <div class="grid gap-3 rounded-xl border border-teal-100 bg-white/75 px-4 py-3 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div>
                            <p class="font-extrabold text-ink">{{ $school->name }}</p>
                            <p class="text-sm font-semibold text-slate-500">{{ $school->users_count }} user / {{ $school->slug }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-700">Aktif</span>
                            <form method="post" action="{{ route('admin.schools.deactivate', $school) }}" onsubmit="return confirm('Nonaktifkan {{ addslashes($school->name) }}? User sekolah ini tidak dapat login sampai diaktifkan kembali.');">
                                @csrf
                                @method('PATCH')
                                <button class="btn-soft px-3 py-2 text-xs text-rose-700">Nonaktifkan</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-teal-100 bg-white/75 px-4 py-6 text-center text-sm font-bold text-slate-500">
                        Belum ada sekolah aktif.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="surface rounded-2xl p-6">
            <h2 class="text-xl font-extrabold text-ocean">Sekolah nonaktif</h2>
            <p class="mt-1 text-sm font-semibold text-slate-500">Sekolah nonaktif tidak muncul di login dan halaman publiknya tertutup.</p>
            <div class="mt-4 space-y-3">
                @forelse($inactiveSchools as $school)
                    <div class="grid gap-3 rounded-xl border border-amber-100 bg-white/75 px-4 py-3 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div>
                            <p class="font-extrabold text-ink">{{ $school->name }}</p>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $school->users_count }} user / {{ $school->slug }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-800">Nonaktif</span>
                            <form method="post" action="{{ route('admin.schools.reactivate', $school) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn-primary px-3 py-2 text-xs">Aktifkan</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-teal-100 bg-white/75 px-4 py-6 text-center text-sm font-bold text-slate-500">
                        Belum ada sekolah yang dinonaktifkan.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="surface rounded-2xl p-6">
        <h2 class="text-xl font-extrabold text-ocean">Terakhir ditolak</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @forelse($rejectedSchools as $school)
                <div class="rounded-xl border border-rose-100 bg-white/75 px-4 py-3">
                    <p class="font-extrabold text-ink">{{ $school->name }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-500">NPSN: {{ $school->npsn ?: '-' }}</p>
                </div>
            @empty
                <p class="rounded-xl border border-teal-100 bg-white/75 px-4 py-6 text-center text-sm font-bold text-slate-500 md:col-span-2">
                    Belum ada pendaftaran yang ditolak.
                </p>
            @endforelse
        </div>
    </section>
</div>
@endsection
