@extends('layouts.app', ['title' => 'Karakter & Sanksi', 'eyebrow' => 'Poin, pelanggaran, prestasi, dan tindakan otomatis'])

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold">Rekap Karakter</h2>
        <p class="text-sm text-slate-500">Hijau baik, kuning peringatan, merah bahaya.</p>
    </div>
    @if(auth()->user()->hasRole(['admin','guru']))
        <a href="{{ route('character.create') }}" class="btn-primary px-4 py-3 text-sm">Tambah Prestasi / Pelanggaran</a>
    @endif
</div>

<div class="mt-5 grid gap-4 lg:grid-cols-3">
    @foreach($students as $student)
        @php
            $score = (int) ($student->total_points ?? 0);
            $color = $score <= -100 ? 'rose' : ($score <= -20 ? 'amber' : 'emerald');
            $label = $score <= -100 ? 'Bahaya' : ($score <= -20 ? 'Peringatan' : 'Baik');
            $width = max(5, min(100, 50 + $score));
        @endphp
        <article class="surface rounded-xl p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-bold">{{ $student->name }}</h3>
                    <p class="text-sm text-slate-500">{{ $student->class_name ?? 'Tanpa kelas' }}</p>
                </div>
                <span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-sm font-bold text-{{ $color }}-800">{{ $label }}</span>
            </div>
            <p class="mt-5 text-3xl font-bold">{{ $score }} <span class="text-sm font-semibold text-slate-500">poin</span></p>
            <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-{{ $color }}-500" style="width: {{ $width }}%"></div>
            </div>
            <a href="{{ route('character.report', $student) }}" class="btn-soft mt-4 px-3 py-2 text-sm">Export laporan PDF</a>
        </article>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_.6fr]">
    <section class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">Riwayat Poin</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="text-slate-500">
                    <tr><th class="py-3">Siswa</th><th>Jenis</th><th>Kategori</th><th>Poin</th><th>Deskripsi</th><th>Guru</th><th>Tanggal</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($points as $point)
                        <tr>
                            <td class="py-3 font-semibold">{{ $point->student->name }}</td>
                            <td>{{ ucfirst($point->type) }}</td>
                            <td>{{ $point->category }}</td>
                            <td class="{{ $point->point < 0 ? 'text-rose-700' : 'text-emerald-700' }} font-bold">{{ $point->point }}</td>
                            <td>{{ $point->title }}</td>
                            <td>{{ optional($point->teacher)->name ?? '-' }}</td>
                            <td>{{ $point->occurred_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $points->links() }}</div>
    </section>
    <aside class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">Rule Sanksi Otomatis</h2>
        <div class="mt-4 space-y-3">
            @foreach([['<= -20','Peringatan 1'],['<= -30','Panggilan orang tua'],['<= -100','Skorsing'],['<= -150','Rekomendasi tindakan berat']] as [$point, $label])
                <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">
                    <p class="font-bold">{{ $point }}</p>
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </aside>
</div>
@endsection

