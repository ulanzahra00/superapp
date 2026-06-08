@extends('layouts.app', ['title' => 'Profil', 'eyebrow' => 'Identitas pengguna'])

@section('content')
<div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
    <section class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">{{ $user->name }}</h2>
        <div class="mt-4 space-y-3 text-sm">
            <p><span class="font-semibold text-slate-500">Email:</span> {{ $user->email }}</p>
            <p><span class="font-semibold text-slate-500">Role:</span> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
            <p><span class="font-semibold text-slate-500">Kelas:</span> {{ $user->class_name ?? '-' }}</p>
            <p><span class="font-semibold text-slate-500">Telepon:</span> {{ $user->phone ?? '-' }}</p>
        </div>
    </section>
    <section class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">Poin Karakter</h2>
        @if($user->role === 'siswa')
            <p class="mt-4 text-4xl font-bold">{{ $user->characterScore() }}</p>
            <p class="mt-1 text-sm text-slate-500">Total dari riwayat prestasi dan pelanggaran.</p>
        @elseif($user->role === 'orang_tua')
            <div class="mt-4 space-y-3">
                @foreach($user->children as $child)
                    <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">
                        <p class="font-bold">{{ $child->name }}</p>
                        <p class="text-sm text-slate-500">Total poin: {{ $child->characterScore() }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-4 text-sm text-slate-500">Guru dan admin dapat memantau seluruh siswa melalui menu Karakter & Sanksi.</p>
        @endif
    </section>
</div>
@endsection

