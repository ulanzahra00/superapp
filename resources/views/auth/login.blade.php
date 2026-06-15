@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[linear-gradient(135deg,#004225_0%,#006B3F_48%,#00923F_100%)] px-4 py-10 text-white sm:px-6">
    <div class="mx-auto grid min-h-[calc(100vh-5rem)] max-w-5xl items-center gap-8 lg:grid-cols-2 lg:items-stretch">
        <section class="min-h-[420px] overflow-hidden rounded-3xl bg-white/10 shadow-2xl ring-1 ring-white/20 backdrop-blur">
            <div class="relative h-full">
                <img
                    src="{{ asset('images/login-school-panel.jpg') }}"
                    alt="Siswa SD Negeri 1 Molinow menggunakan laptop di lingkungan sekolah"
                    class="absolute inset-0 h-full w-full object-cover object-center"
                >
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-emerald-950/82 via-emerald-950/30 to-transparent px-6 pb-4 pt-20 sm:px-8 sm:pb-5">
                    <p class="inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-lime-100 ring-1 ring-white/20 backdrop-blur">Portal digital terpadu</p>
                    <h1 class="mt-4 max-w-xl text-3xl font-extrabold leading-tight tracking-tight sm:text-5xl">Super App Sekolah</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-emerald-50/90 sm:text-base">Layanan terpadu untuk pembelajaran, administrasi, komunikasi, dan pemantauan perkembangan siswa.</p>
                </div>
            </div>
        </section>
        <section class="surface rounded-2xl p-6 text-slate-900 shadow-2xl lg:flex lg:min-h-[560px] lg:flex-col lg:justify-center">
            <h2 class="text-2xl font-bold">Login Sekolah</h2>
            <p class="mt-1 text-sm text-slate-500">Pilih sekolah terlebih dahulu agar akun masuk ke ruang data yang benar.</p>
            @if(session('status'))
                <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif
            <form class="mt-6 space-y-4" method="post" action="{{ route('login') }}">
                @csrf
                <label class="block text-sm font-semibold">Sekolah
                    @if($selectedSchool)
                        <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                        <div class="mt-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 font-bold text-ocean">
                            {{ $selectedSchool->name }}
                        </div>
                    @else
                        <select name="school_id" data-login-school class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                            <option value="">Pilih sekolah</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ (string) old('school_id') === (string) $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </label>
                <label class="block text-sm font-semibold">Email
                    <input data-login-email name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required autofocus>
                </label>
                <label class="block text-sm font-semibold">Password
                    <input data-login-password name="password" type="password" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                </label>
                @error('school_id')<p class="text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                @error('email')<p class="text-sm font-semibold text-rose-600">{{ $message }}</p>@enderror
                <button class="w-full btn-primary px-4 py-3">Login</button>
                <a href="{{ route('register') }}" class="w-full btn-soft px-4 py-3 text-sm">Daftar Sekolah</a>
                <a href="{{ $selectedSchool ? route('public.school.home', ['schoolSlug' => $selectedSchool->public_slug]) : route('public.home') }}" class="w-full btn-soft px-4 py-3 text-sm">Kembali ke Home</a>
            </form>
        </section>
    </div>
</div>

<script>
    localStorage.removeItem('superapp.active.user');
    localStorage.removeItem('superapp.active.tab');
    localStorage.removeItem('superapp.active.heartbeat');
    sessionStorage.removeItem('superapp.session.tab');

</script>
@endsection
