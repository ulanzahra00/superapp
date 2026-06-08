@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[linear-gradient(135deg,#0f172a_0%,#0f3d5e_48%,#0f766e_100%)] px-4 py-10 text-white sm:px-6">
    <div class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-6xl items-center gap-8 lg:grid-cols-[1.1fr_.9fr]">
        <section>
            <p class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-emerald-200 ring-1 ring-white/15">Portal digital terpadu</p>
            <h1 class="mt-5 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">Super App Sekolah</h1>
            <p class="mt-5 max-w-2xl text-lg text-slate-300">Absensi, LMS, nilai, komunikasi, berita sekolah, dan monitoring karakter siswa dalam satu sistem Laravel.</p>
            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur transition duration-200 hover:-translate-y-1 hover:bg-white/15"><div class="text-2xl font-bold">4</div><div class="text-sm text-slate-300">Role akses</div></div>
                <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur transition duration-200 hover:-translate-y-1 hover:bg-white/15"><div class="text-2xl font-bold">Auto</div><div class="text-sm text-slate-300">Sanksi siswa</div></div>
                <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur transition duration-200 hover:-translate-y-1 hover:bg-white/15"><div class="text-2xl font-bold">PDF</div><div class="text-sm text-slate-300">Laporan karakter</div></div>
            </div>
        </section>
        <section class="surface rounded-2xl p-6 text-slate-900 shadow-2xl">
            <h2 class="text-2xl font-bold">Masuk</h2>
            <p class="mt-1 text-sm text-slate-500">Demo: admin@sekolah.test, guru@sekolah.test, siswa@sekolah.test, ortu@sekolah.test. Password: password.</p>
            <form class="mt-6 space-y-4" method="post" action="{{ route('login') }}">
                @csrf
                <label class="block text-sm font-semibold">Email
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required autofocus>
                </label>
                <label class="block text-sm font-semibold">Password
                    <input name="password" type="password" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                </label>
                @error('email')<p class="text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                <button class="w-full btn-primary px-4 py-3">Login</button>
            </form>
            <a href="{{ route('register') }}" class="btn-link mt-4 w-full py-2 text-center text-sm">Buat akun baru</a>
        </section>
    </div>
</div>
@endsection

