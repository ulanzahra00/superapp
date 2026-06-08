@extends('layouts.app', ['title' => 'Dashboard', 'eyebrow' => 'Ringkasan peran '.ucfirst(str_replace('_', ' ', auth()->user()->role))])

@section('content')
@php
    $characterColor = function ($score) {
        return $score <= -100 ? 'rose' : ($score <= -20 ? 'amber' : 'emerald');
    };
@endphp

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
    @foreach([
        ['Siswa', $stats['students'], 'from-emerald-50 via-green-50 to-teal-100', 'text-ocean'],
        ['Guru', $stats['teachers'], 'from-emerald-50 via-green-50 to-teal-100', 'text-brand'],
        ['Hadir', $stats['attendance'], 'from-emerald-50 via-green-50 to-teal-100', 'text-emerald-700'],
        ['Pelanggaran', $stats['violations'], 'from-emerald-50 via-green-50 to-teal-100', 'text-teal-700'],
        ['Sanksi', $stats['sanctions'], 'from-emerald-50 via-green-50 to-teal-100', 'text-green-800'],
        ['Berita', $stats['news'], 'from-emerald-50 via-green-50 to-teal-100', 'text-teal-700'],
    ] as [$label, $value, $bg, $text])
        <div class="rounded-xl border border-teal-200/70 bg-gradient-to-br {{ $bg }} p-4 shadow-soft transition duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-glow">
            <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-extrabold {{ $text }}">{{ $value }}</p>
        </div>
    @endforeach
</div>

@if(auth()->user()->hasRole('admin'))
    <section class="mt-6 surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold">Import Data Siswa</h2>
                <p class="mt-1 text-sm text-slate-500">Unduh template tabel Excel, isi data siswa pada kolom yang tersedia, lalu upload kembali untuk menambah atau memperbarui data siswa.</p>
            </div>
            <a href="{{ route('students.import.template') }}" class="btn-soft px-4 py-3 text-sm">Unduh Template</a>
        </div>

        @if($errors->has('student_file'))
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first('student_file') }}</div>
        @endif

        <form method="post" action="{{ route('students.import') }}" enctype="multipart/form-data" class="mt-5 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
            @csrf
            <label class="text-sm font-semibold text-slate-700">File Excel/CSV siswa
                <input type="file" name="student_file" accept=".xls,.xlsx,.csv,text/csv" required class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3">
            </label>
            <button class="btn-primary px-5 py-3 text-sm">Import Siswa</button>
        </form>

        <p class="mt-3 text-xs text-slate-500">Kolom wajib: nama_siswa, email_siswa, nis, kelas. Kolom opsional: nama_orang_tua, email_orang_tua, telepon, password.</p>
    </section>
@endif

<div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
    <section class="surface rounded-xl p-5">
        @if(auth()->user()->hasRole('admin'))
            <form method="post" action="{{ route('students.destroy-selected') }}" onsubmit="return confirm('Hapus siswa yang dipilih? Data terkait siswa juga akan ikut terhapus.');">
                @csrf
                @method('delete')
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Monitoring Karakter Siswa</h2>
                <p class="text-sm text-slate-500">Total poin otomatis dari prestasi dan pelanggaran.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(auth()->user()->hasRole('admin'))
                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-ocean">
                        <input type="checkbox" data-check-all-students class="h-4 w-4 rounded border-slate-300 text-brand">
                        Pilih semua
                    </label>
                    <button class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-extrabold text-rose-700 transition hover:-translate-y-0.5 hover:bg-rose-100">Hapus siswa terpilih</button>
                @endif
                @if(auth()->user()->hasRole(['admin','guru']))
                    <a href="{{ route('character.create') }}" class="btn-primary px-4 py-2 text-sm">Input Poin</a>
                @endif
            </div>
        </div>

        @if($errors->has('student_ids'))
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first('student_ids') }}</div>
        @endif

        <div class="mt-5 space-y-4">
            @forelse($students as $student)
                @php
                    $score = (int) ($student->total_points ?? $student->characterScore());
                    $color = $characterColor($score);
                    $width = max(5, min(100, 50 + $score));
                @endphp
                <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            @if(auth()->user()->hasRole('admin'))
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" data-student-checkbox class="mt-1 h-4 w-4 rounded border-slate-300 text-brand">
                            @endif
                            <div>
                                <h3 class="font-bold">{{ $student->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $student->class_name ?? 'Belum ada kelas' }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-sm font-bold text-{{ $color }}-800">{{ $score }} poin</span>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-{{ $color }}-500" style="width: {{ $width }}%"></div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2 text-sm">
                        <a class="btn-link font-bold" href="{{ route('character.report', $student) }}">Export PDF</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-500">{{ $score <= -20 ? 'Perlu tindak lanjut' : 'Kondisi baik' }}</span>
                    </div>
                </div>
            @empty
                <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada data siswa.</p>
            @endforelse
        </div>

        @if(auth()->user()->hasRole('admin'))
            </form>
        @endif
    </section>

    <section class="space-y-6">
        <div class="surface rounded-xl p-5">
            <h2 class="text-lg font-bold">Sanksi Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse($sanctions as $sanction)
                    <div class="rounded-lg border border-rose-100 bg-rose-50 p-4">
                        <p class="font-bold text-rose-900">{{ $sanction->sanction_type }}</p>
                        <p class="text-sm text-rose-700">{{ $sanction->student->name }}: {{ $sanction->total_points }} poin</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada sanksi.</p>
                @endforelse
            </div>
        </div>
        <div class="surface rounded-xl p-5">
            <h2 class="text-lg font-bold">Berita Sekolah</h2>
            <div class="mt-4 space-y-3">
                @foreach($news as $item)
                    <a href="{{ route('news.show', $item) }}" class="block rounded-xl border border-slate-200/80 bg-white/80 p-4 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-soft">
                        <p class="text-sm font-semibold text-brand">{{ $item->category }}</p>
                        <p class="font-bold">{{ $item->title }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>

<section class="mt-6 surface rounded-xl p-5">
    <h2 class="text-lg font-bold">Aktivitas Karakter Terbaru</h2>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="text-slate-500">
                <tr><th class="py-3">Siswa</th><th>Jenis</th><th>Kategori</th><th>Poin</th><th>Catatan</th><th>Tanggal</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentPoints as $point)
                    <tr>
                        <td class="py-3 font-semibold">{{ $point->student->name }}</td>
                        <td>{{ ucfirst($point->type) }}</td>
                        <td>{{ $point->category }}</td>
                        <td class="{{ $point->point < 0 ? 'text-rose-700' : 'text-emerald-700' }} font-bold">{{ $point->point }}</td>
                        <td>{{ $point->title }}</td>
                        <td>{{ $point->occurred_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@if(auth()->user()->hasRole('admin'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkAll = document.querySelector('[data-check-all-students]');
            const checkboxes = Array.from(document.querySelectorAll('[data-student-checkbox]'));

            if (! checkAll) {
                return;
            }

            checkAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = checkAll.checked;
                });
            });

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    checkAll.checked = checkboxes.length > 0 && checkboxes.every(function (item) {
                        return item.checked;
                    });
                });
            });
        });
    </script>
@endif
@endsection

