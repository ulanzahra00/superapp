@extends('layouts.public', ['title' => 'Maintenance Sekolah'])

@section('content')
<main class="min-h-[calc(100vh-12rem)] px-4 py-12 sm:px-6 lg:px-8">
    <section class="mx-auto grid max-w-5xl items-center gap-8 lg:grid-cols-[.95fr_1.05fr]">
        <div class="surface rounded-2xl p-6 sm:p-8">
            <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-extrabold text-amber-800">
                Maintenance sekolah
            </span>
            <h1 class="mt-5 text-3xl font-extrabold leading-tight text-ocean sm:text-5xl">
                Portal {{ $publicSchool->name }} sedang dinonaktifkan sementara.
            </h1>
            <p class="mt-4 text-base leading-7 text-slate-600">
                Akses publik dan login sekolah ini sedang ditutup oleh admin pusat untuk pengecekan data atau pengelolaan layanan. Data sekolah tetap tersimpan, tetapi pengguna belum dapat masuk sampai sekolah diaktifkan kembali.
            </p>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('public.home') }}" class="btn-primary px-4 py-3 text-sm">Kembali ke Home Utama</a>
                <a href="{{ route('login') }}" class="btn-soft px-4 py-3 text-sm">Login Sekolah Lain</a>
            </div>
        </div>

        <div class="rounded-2xl border border-teal-100 bg-white/75 p-6 shadow-soft">
            <p class="text-sm font-extrabold uppercase tracking-wide text-brand">Status layanan</p>
            <div class="mt-5 space-y-4">
                <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                    <p class="text-sm font-bold text-amber-800">Login sekolah ditutup</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Guru, siswa, orang tua, dan admin sekolah belum dapat masuk ke portal ini.</p>
                </div>
                <div class="rounded-xl border border-teal-100 bg-emerald-50 px-4 py-3">
                    <p class="text-sm font-bold text-emerald-800">Data tetap aman</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Penonaktifan hanya menutup akses sampai super admin mengaktifkan sekolah kembali.</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-white px-4 py-3">
                    <p class="text-sm font-bold text-slate-800">Butuh bantuan?</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Hubungi pengelola aplikasi atau admin pusat untuk informasi aktivasi layanan.</p>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
