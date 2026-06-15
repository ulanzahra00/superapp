@extends('layouts.app', ['title' => 'Nilai', 'eyebrow' => 'Rekap akademik'])

@section('content')
@php
    $user = auth()->user();
    $scoreColor = function ($score) {
        return $score < 70 ? 'rose' : ($score < 80 ? 'amber' : 'emerald');
    };
    $groupedGrades = $grades->groupBy('student_id');
@endphp

<section class="surface rounded-xl p-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-extrabold text-brand">
                @if($user->hasRole('guru'))
                    Kelas perwalian {{ $user->class_name ?? '-' }}
                @elseif($user->hasRole('siswa'))
                    Rapor ringkas siswa
                @elseif($user->hasRole('orang_tua'))
                    Pantauan nilai anak
                @else
                    Kontrol nilai sekolah
                @endif
            </p>
            <h2 class="mt-1 text-2xl font-extrabold text-ocean">Rekap Nilai Akademik</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                @if($user->hasRole('guru'))
                    Input dan pantau nilai siswa kelas perwalian. Setiap pembaruan nilai akan memberi notifikasi ke siswa dan orang tua.
                @elseif($user->hasRole('siswa'))
                    Lihat nilai per mata pelajaran dan gunakan catatan ini untuk mengetahui bagian yang perlu ditingkatkan.
                @elseif($user->hasRole('orang_tua'))
                    Pantau perkembangan nilai anak secara ringkas agar pendampingan belajar di rumah lebih terarah.
                @else
                    Pantau sebaran nilai, jumlah data, dan siswa yang membutuhkan pendampingan akademik.
                @endif
            </p>
        </div>
        <span class="rounded-full bg-teal-50 px-4 py-2 text-sm font-extrabold text-teal-800">{{ $gradeStats['average'] ?: 0 }} rata-rata</span>
    </div>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Siswa', $gradeStats['students']],
            ['Data nilai', $gradeStats['grades']],
            ['Rata-rata', $gradeStats['average'] ?: 0],
            ['Perlu bantuan', $gradeStats['needs_support']],
        ] as [$label, $value])
            <div class="rounded-xl border border-teal-100 bg-white/75 p-4">
                <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-extrabold text-ocean">{{ $value }}</p>
            </div>
        @endforeach
    </div>
</section>

@if($user->hasRole('guru'))
    <section class="mt-6 surface rounded-xl p-5">
        <form method="post" action="{{ route('grades.store') }}" class="surface rounded-xl p-5">
            @csrf
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-brand">Input nilai</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Tambah / Perbarui Nilai</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Pilih siswa perwalian, mata pelajaran, semester, lalu simpan nilai.</p>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $students->count() }} siswa</span>
            </div>

            @if($errors->any())
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
            @endif

            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-[1.5fr_1fr_.8fr_.7fr] xl:items-end">
                <label class="text-sm font-semibold text-slate-700">Siswa
                    <select name="student_id" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                        <option value="">Pilih siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>{{ $student->name }} - {{ $student->nis ?? '-' }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Mata pelajaran
                    <select name="subject" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Semester
                    <select name="semester" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                        <option value="Ganjil" @selected(old('semester') === 'Ganjil')>Ganjil</option>
                        <option value="Genap" @selected(old('semester') === 'Genap')>Genap</option>
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Nilai
                    <input name="score" type="number" min="0" max="100" step="0.01" value="{{ old('score') }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="85" required>
                </label>
                <button class="btn-primary px-4 py-3 text-sm">Simpan Nilai</button>
            </div>
        </form>
    </section>

    <section class="mt-6 surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-sm font-extrabold text-brand">Kelas perwalian</p>
                <h2 class="mt-1 text-xl font-extrabold text-ocean">Ringkasan Siswa</h2>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $students->count() }} siswa</span>
        </div>

        <div class="mt-4 max-h-[430px] overflow-auto rounded-xl border border-teal-100">
            <table class="w-full min-w-[620px] text-left text-sm">
                <thead class="sticky top-0 bg-emerald-50 text-slate-500">
                    <tr><th class="py-3 pl-4">Siswa</th><th>NIS</th><th>Rata-rata</th><th>Status</th></tr>
                </thead>
                <tbody class="divide-y divide-teal-100/80 bg-white/40">
                    @forelse($students as $student)
                        @php
                            $studentAverage = round((float) ($student->average_score ?? $groupedGrades->get($student->id, collect())->avg('score')), 1);
                            $color = $scoreColor($studentAverage);
                        @endphp
                        <tr>
                            <td class="py-3 pl-4 font-extrabold text-slate-900">{{ $student->name }}</td>
                            <td>{{ $student->nis ?? '-' }}</td>
                            <td class="font-extrabold text-{{ $color }}-700">{{ $studentAverage ?: '-' }}</td>
                            <td><span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-xs font-extrabold text-{{ $color }}-800">{{ $studentAverage < 70 && $studentAverage > 0 ? 'Perlu bantuan' : 'Baik' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-sm font-semibold text-slate-500">Belum ada siswa di kelas perwalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endif

<section class="mt-6 surface rounded-xl p-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-sm font-extrabold text-brand">Daftar nilai</p>
            <h2 class="mt-1 text-xl font-extrabold text-ocean">
                {{ $user->hasRole(['siswa','orang_tua']) ? 'Rapor Mata Pelajaran' : 'Nilai Terbaru' }}
            </h2>
        </div>
        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $grades->count() }} data</span>
    </div>

    @if($user->hasRole(['siswa','orang_tua']))
        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($grades as $grade)
                @php $color = $scoreColor((float) $grade->score); @endphp
                <article class="rounded-xl border border-teal-100 bg-white/75 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ optional($grade->course)->name ?? 'Mata pelajaran' }}</p>
                            <h3 class="mt-1 font-extrabold text-slate-900">{{ optional($grade->student)->name ?? 'Siswa' }}</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Semester {{ $grade->semester }}</p>
                        </div>
                        <span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-sm font-extrabold text-{{ $color }}-800">{{ number_format((float) $grade->score, 0) }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        {{ $grade->score < 70 ? 'Perlu latihan dan pendampingan lagi.' : 'Pertahankan kebiasaan belajar yang baik.' }}
                    </p>
                </article>
            @empty
                <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500 md:col-span-2 xl:col-span-3">Belum ada nilai yang tercatat.</p>
            @endforelse
        </div>
    @else
        <div class="mt-5 overflow-x-auto rounded-xl border border-teal-100">
            <table class="w-full min-w-[780px] text-left text-sm">
                <thead class="bg-emerald-50 text-slate-500">
                    <tr><th class="py-3 pl-4">Siswa</th><th>Kelas</th><th>Mata Pelajaran</th><th>Semester</th><th>Nilai</th><th>Status</th></tr>
                </thead>
                <tbody class="divide-y divide-teal-100/80 bg-white/40">
                    @forelse($grades as $grade)
                        @php $color = $scoreColor((float) $grade->score); @endphp
                        <tr>
                            <td class="py-3 pl-4 font-extrabold text-slate-900">{{ optional($grade->student)->name ?? '-' }}</td>
                            <td>{{ optional($grade->student)->class_name ?? '-' }}</td>
                            <td>{{ optional($grade->course)->name ?? '-' }}</td>
                            <td>{{ $grade->semester }}</td>
                            <td class="font-extrabold text-{{ $color }}-700">{{ number_format((float) $grade->score, 0) }}</td>
                            <td><span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-xs font-extrabold text-{{ $color }}-800">{{ $grade->score < 70 ? 'Perlu bantuan' : 'Baik' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-4 text-sm font-semibold text-slate-500">Belum ada nilai yang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
