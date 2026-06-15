@extends('layouts.public', ['title' => $serviceMeta['label'].' - '.$publicSchool->name])

@section('content')
<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="surface rounded-2xl p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Panel Layanan</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-ocean">{{ $serviceMeta['label'] }}</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $serviceMeta['description'] }}</p>
            </div>
            <a href="{{ route('public.school.home', $schoolQuery) }}" class="btn-soft px-4 py-3 text-sm">Kembali</a>
        </div>

        @if($serviceKey === 'layanan')
            <form method="get" action="{{ route('public.school.service', array_merge($schoolQuery, ['service' => 'layanan'])) }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama siswa..."
                    class="min-h-[44px] flex-1 rounded-xl border border-slate-200 bg-white/80 px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm"
                >
                <button class="btn-primary px-5 py-3 text-sm">Cari</button>
                @if($search !== '')
                    <a href="{{ route('public.school.service', array_merge($schoolQuery, ['service' => 'layanan'])) }}" class="btn-soft px-5 py-3 text-sm">Reset</a>
                @endif
            </form>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                @forelse($students as $student)
                    @php
                        $total = (int) ($student->total_points ?? 0);
                    @endphp
                    <article class="rounded-lg border border-slate-200/80 bg-white/75 p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h2 class="truncate text-sm font-extrabold text-ocean" title="{{ $student->name }}">{{ $student->name }}</h2>
                                <p class="mt-0.5 text-xs font-semibold text-slate-500">{{ $student->class_name ?? 'Tanpa kelas' }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-extrabold {{ $total < 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $total }} poin</span>
                        </div>
                        <p class="mt-3 text-xs font-extrabold uppercase tracking-wide text-slate-400">Catatan siswa</p>
                        <p class="mt-1 text-sm font-bold text-slate-800">Masuk untuk melihat detail</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600">
                            Prestasi, pelanggaran, dan tindak lanjut hanya tampil untuk akun yang berwenang.
                        </p>
                        <a href="{{ auth()->check() ? route('dashboard') : route('school.login', ['schoolSlug' => $publicSchool->public_slug]) }}" class="btn-soft mt-3 w-full px-3 py-2 text-xs">
                            Lihat di panel login
                        </a>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-600 md:col-span-2 xl:col-span-3">Tidak ada siswa yang cocok dengan pencarian.</p>
                @endforelse
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-500">
                    Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa
                </p>
                <div class="flex gap-2">
                    @if($students->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-300">Kembali</span>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="btn-soft px-4 py-2 text-sm">Kembali</a>
                    @endif

                    @if($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="btn-primary px-4 py-2 text-sm">Next</a>
                    @else
                        <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-300">Next</span>
                    @endif
                </div>
            </div>
        @endif

        @if($serviceKey === 'absensi')
            <div class="mt-6 grid gap-3 sm:grid-cols-4">
                @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alfa' => 'Alfa'] as $status => $label)
                    <div class="rounded-xl border border-slate-200/80 bg-white/70 p-5 shadow-sm">
                        <p class="font-extrabold text-slate-900">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-slate-950">{{ $attendanceSummary[$status] ?? 0 }}</p>
                        <div class="mt-4 border-t border-slate-100 pt-3">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-slate-400">Nama siswa</p>
                            <div class="mt-2 max-h-56 space-y-2 overflow-y-auto pr-1">
                                @forelse($attendanceNames[$status] ?? [] as $name)
                                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">{{ $name }}</p>
                                @empty
                                    <p class="text-sm text-slate-500">Belum ada siswa.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($serviceKey === 'lms')
            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach([
                    ['Kelas', $lmsStats['classes'] ?? 0],
                    ['Guru', $lmsStats['teachers'] ?? 0],
                    ['Siswa', $lmsStats['students'] ?? 0],
                    ['Tugas LMS', $lmsStats['assignments'] ?? 0],
                    ['Jawaban', $lmsStats['submissions'] ?? 0],
                ] as [$label, $value])
                    <div class="rounded-xl border border-teal-100 bg-white/75 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-extrabold text-ocean">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-5 xl:grid-cols-[1.1fr_.9fr]">
                <section class="rounded-xl border border-teal-100 bg-white/70 p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-extrabold text-brand">Kelas LMS</p>
                            <h2 class="mt-1 text-xl font-extrabold text-ocean">Kesiapan Pembelajaran Digital</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Ringkasan kelas, wali kelas, dan jumlah tugas yang sudah diterbitkan di sekolah ini.</p>
                        </div>
                        <a href="{{ route('school.login', ['schoolSlug' => $publicSchool->public_slug]) }}" class="btn-primary px-4 py-2 text-sm">Masuk LMS</a>
                    </div>

                    <div class="mt-5 overflow-x-auto rounded-xl border border-teal-100">
                        <table class="w-full min-w-[680px] text-left text-sm">
                            <thead class="bg-emerald-50 text-slate-500">
                                <tr>
                                    <th class="py-3 pl-4">Kelas</th>
                                    <th>Wali kelas</th>
                                    <th>Siswa</th>
                                    <th>Tugas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-teal-100/80 bg-white/40">
                                @forelse($classSummaries as $class)
                                    <tr>
                                        <td class="py-3 pl-4 font-extrabold text-slate-900">{{ $class['name'] }}</td>
                                        <td>{{ $class['teachers']->isNotEmpty() ? $class['teachers']->join(', ') : 'Belum ada wali kelas' }}</td>
                                        <td>{{ $class['students'] }} siswa</td>
                                        <td>{{ $class['assignments'] }} tugas</td>
                                        <td>
                                            <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $class['teachers']->isNotEmpty() && $class['assignments'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $class['teachers']->isNotEmpty() && $class['assignments'] > 0 ? 'Aktif' : 'Perlu dilengkapi' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-sm font-semibold text-slate-500">Belum ada data kelas LMS.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-xl border border-teal-100 bg-white/70 p-5 shadow-sm">
                    <div>
                        <p class="text-sm font-extrabold text-brand">Tugas terbaru</p>
                        <h2 class="mt-1 text-xl font-extrabold text-ocean">Aktivitas dari Guru</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Daftar singkat tugas yang baru diterbitkan untuk siswa sekolah ini.</p>
                    </div>

                    <div class="mt-4 max-h-[430px] space-y-3 overflow-auto pr-1">
                        @forelse($assignments as $assignment)
                            <article class="rounded-xl border border-slate-200/80 bg-white/80 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ $assignment->class_name }} / {{ $assignment->subject }}</p>
                                        <h3 class="mt-1 font-extrabold text-slate-900">{{ $assignment->title }}</h3>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">Guru: {{ optional($assignment->teacher)->name ?? '-' }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-extrabold text-slate-600">{{ optional($assignment->due_date)->format('d M Y') ?? 'Tanpa batas' }}</span>
                                </div>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $assignment->instructions }}</p>
                            </article>
                        @empty
                            <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada tugas LMS yang diterbitkan guru.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-xl border border-teal-100 bg-white/70 p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-extrabold text-brand">Materi terdata</p>
                        <h2 class="mt-1 text-xl font-extrabold text-ocean">Mata Pelajaran dan Kelas</h2>
                    </div>
                    <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $courses->count() }} materi</span>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                    @forelse($courses as $course)
                        <article class="rounded-xl border border-slate-200/80 bg-white/80 p-4">
                            <h3 class="font-extrabold text-ocean">{{ $course->name }}</h3>
                            <p class="mt-1 text-sm font-semibold text-slate-500">{{ $course->class_name }}</p>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $course->description ?? 'Materi dikelola oleh guru sekolah.' }}</p>
                        </article>
                    @empty
                        <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-600 md:col-span-2 xl:col-span-4">Belum ada materi yang terdata.</p>
                    @endforelse
                </div>
            </section>
        @endif

        @if($serviceKey === 'komunikasi')
            <div class="mt-6 space-y-3">
                @forelse($messages as $message)
                    <article class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-extrabold text-ocean">{{ optional($message->sender)->name ?? 'Pengirim tidak ditemukan' }}</p>
                            <p class="text-xs font-semibold text-slate-400">Untuk {{ optional($message->receiver)->name ?? 'penerima tidak ditemukan' }}</p>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $message->body }}</p>
                    </article>
                @empty
                    <div class="rounded-xl bg-slate-50 p-5 text-sm font-semibold text-slate-500">Belum ada pesan.</div>
                @endforelse
            </div>
        @endif
    </section>
</main>
@endsection
