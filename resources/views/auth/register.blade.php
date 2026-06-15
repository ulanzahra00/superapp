@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[linear-gradient(135deg,#004225_0%,#006B3F_48%,#00923F_100%)] px-4 py-10 sm:px-6">
    <div class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-5xl items-center gap-8 lg:grid-cols-2">
        <section class="text-white">
            <p class="inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-lime-100 ring-1 ring-white/20">Pendaftaran sekolah</p>
            <h1 class="mt-5 max-w-xl text-4xl font-extrabold leading-tight sm:text-5xl">Mulai portal sekolah tanpa mencampur data.</h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-emerald-50/90">Setiap sekolah memiliki ruang data sendiri untuk user, siswa, nilai, LMS, komunikasi, absensi, dan berita sekolah. Pendaftaran baru akan ditinjau sebelum akun dapat digunakan.</p>
        </section>

        <section class="surface rounded-2xl p-6 text-slate-900 shadow-2xl">
            <h2 class="text-2xl font-bold">Daftar Sekolah</h2>
            <form method="post" action="{{ route('register') }}" class="mt-6 grid gap-4">
                @csrf
                <label class="text-sm font-semibold">Nama sekolah
                    <input name="school_name" value="{{ old('school_name') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required autofocus>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold">NPSN
                        <input name="npsn" value="{{ old('npsn') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                    <label class="text-sm font-semibold">Telepon sekolah
                        <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                </div>
                <label class="text-sm font-semibold">Alamat
                    <textarea name="address" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>{{ old('address') }}</textarea>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold">Nama admin
                        <input name="admin_name" value="{{ old('admin_name') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                    <label class="text-sm font-semibold">Email admin
                        <input name="admin_email" type="email" value="{{ old('admin_email') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold">Password
                        <input name="password" type="password" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                    <label class="text-sm font-semibold">Konfirmasi password
                        <input name="password_confirmation" type="password" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    </label>
                </div>
                @if($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
                @endif
                <button class="btn-primary px-4 py-3">Kirim Pendaftaran</button>
                <a href="{{ route('login') }}" class="btn-soft px-4 py-3 text-sm">Sudah punya akun</a>
            </form>
        </section>
    </div>
</div>
@endsection
