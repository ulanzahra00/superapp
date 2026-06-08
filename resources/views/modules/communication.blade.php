@extends('layouts.app', ['title' => 'Komunikasi', 'eyebrow' => 'Pesan guru, siswa, dan orang tua'])

@section('content')
<section class="surface rounded-xl p-5">
    <h2 class="text-lg font-bold">Ruang Komunikasi</h2>
    <p class="mt-2 text-sm text-slate-500">Disiapkan untuk pesan internal, koordinasi wali kelas, dan pengumuman personal.</p>
    <div class="mt-4 space-y-3">
        @forelse($messages as $message)
            <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">{{ $message->body }}</div>
        @empty
            <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada pesan.</div>
        @endforelse
    </div>
</section>
@endsection

