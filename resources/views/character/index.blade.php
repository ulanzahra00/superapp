@extends('layouts.app', ['title' => 'Karakter & Sanksi', 'eyebrow' => 'Poin, pelanggaran, prestasi, dan tindakan otomatis'])

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold">Daftar Siswa dan Poin Karakter</h2>
        <p class="text-sm text-slate-500">
            @if(auth()->user()->role === 'guru' && auth()->user()->class_name)
                Menampilkan siswa perwalian {{ auth()->user()->class_name }}.
            @else
                Menampilkan rekap poin, pelanggaran, prestasi, dan status siswa.
            @endif
        </p>
    </div>
    @if(auth()->user()->hasRole(['admin','guru']))
        <a href="{{ route('character.create') }}" class="btn-primary px-4 py-3 text-sm">Tambah Prestasi / Pelanggaran</a>
    @endif
</div>

<section class="surface mt-5 rounded-xl p-5">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-sm">
            <thead class="text-slate-500">
                <tr>
                    <th class="py-3">Siswa</th>
                    <th>Kelas</th>
                    <th>Total Poin</th>
                    <th>Pelanggaran</th>
                    <th>Pelanggaran Terakhir</th>
                    <th>Prestasi</th>
                     <th>Status</th>
                     <th>Laporan</th>
                     <th>Surat Panggilan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-100/80">
                @forelse($students as $student)
                    @php
                        $score = (int) ($student->total_points ?? 0);
                        $color = $score <= -100 ? 'rose' : ($score <= -20 ? 'amber' : 'emerald');
                        $label = $score <= -100 ? 'Bahaya' : ($score <= -20 ? 'Peringatan' : 'Baik');
                        $latestViolation = $student->relationLoaded('studentPoints')
                            ? $student->studentPoints->firstWhere('type', 'pelanggaran')
                            : null;
                    @endphp
                    <tr class="align-top">
                        <td class="py-4">
                            <p class="font-extrabold text-slate-900">{{ $student->name }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">NIS: {{ $student->nis ?? '-' }}</p>
                        </td>
                        <td class="py-4 font-semibold text-slate-700">{{ $student->class_name ?? 'Tanpa kelas' }}</td>
                        <td class="py-4">
                            <span class="{{ $score < 0 ? 'text-rose-700' : 'text-emerald-700' }} text-xl font-extrabold">{{ $score }}</span>
                            <span class="text-xs font-semibold text-slate-500">poin</span>
                        </td>
                        <td class="py-4">
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-extrabold text-rose-700">{{ $student->violation_count ?? 0 }}</span>
                        </td>
                        <td class="py-4">
                            @if($latestViolation)
                                <p class="font-bold text-slate-800">{{ $latestViolation->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $latestViolation->occurred_at->format('d M Y') }} / {{ $latestViolation->point }} poin</p>
                            @else
                                <span class="text-slate-400">Tidak ada</span>
                            @endif
                        </td>
                        <td class="py-4">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-extrabold text-emerald-700">{{ $student->achievement_count ?? 0 }}</span>
                        </td>
                        <td class="py-4">
                            <span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-sm font-bold text-{{ $color }}-800">{{ $label }}</span>
                        </td>
                        <td class="py-4">
                            <a href="{{ route('character.report', $student) }}" class="btn-soft px-3 py-2 text-xs">PDF</a>
                        </td>
                        <td class="py-4">
                            @php
                                $studentSanc = $studentSanctions->get($student->id);
                                $panggilanSanc = $studentSanc ? $studentSanc->firstWhere('sanction_type', 'Panggilan orang tua') : null;
                            @endphp
                            @if($panggilanSanc && $panggilanSanc->pdf_path)
                                <a href="{{ route('character.surat-panggilan.download', $panggilanSanc) }}" class="btn-soft px-3 py-2 text-xs text-amber-700">
                                    📄 SP
                                </a>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-6 text-center font-semibold text-slate-500">Belum ada siswa yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_.6fr]">
    <section class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">Riwayat Poin</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="text-slate-500">
                    <tr><th class="py-3">Siswa</th><th>Jenis</th><th>Kategori</th><th>Poin</th><th>Deskripsi</th><th>Guru</th><th>Tanggal</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($points as $point)
                        @php
                            $canDeletePoint = auth()->user()->hasRole(['admin','guru']) 
                                && in_array($point->student_id, $followedUpStudentIds)
                                && $point->type === 'pelanggaran';
                        @endphp
                        <tr>
                            <td class="py-3 font-semibold">{{ $point->student->name }}</td>
                            <td>{{ ucfirst($point->type) }}</td>
                            <td>{{ $point->category }}</td>
                            <td class="{{ $point->point < 0 ? 'text-rose-700' : 'text-emerald-700' }} font-bold">{{ $point->point }}</td>
                            <td>{{ $point->title }}</td>
                            <td>{{ optional($point->teacher)->name ?? '-' }}</td>
                            <td>{{ $point->occurred_at->format('d M Y') }}</td>
                            <td>
                                @if($canDeletePoint)
                                    <form method="post" action="{{ route('character.point.destroy', $point) }}" onsubmit="return confirm('Hapus point pelanggaran ini?');">
                                        @csrf
                                        @method('delete')
                                        <button class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-extrabold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                                    </form>
                                @endif
                            </td>
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

