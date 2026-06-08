@extends('layouts.app', ['title' => 'Absensi', 'eyebrow' => 'Kehadiran siswa'])

@section('content')
<section class="surface rounded-xl p-5">
    <h2 class="text-lg font-bold">Ringkasan Absensi</h2>
    <p class="mt-2 text-sm text-slate-500">Modul absensi siap diperluas untuk input harian dan rekap per kelas.</p>
    <div class="mt-4 grid gap-3 sm:grid-cols-4">
        @foreach(['hadir' => 'Hijau', 'izin' => 'Biru', 'sakit' => 'Kuning', 'alfa' => 'Merah'] as $status => $label)
            <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">
                <p class="font-bold">{{ ucfirst($status) }}</p>
                <p class="text-2xl font-bold">{{ $items->where('status', $status)->count() }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection

