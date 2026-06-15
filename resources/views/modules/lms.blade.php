@php
    $isSuperAdminLms = $isSuperAdminLms ?? false;
@endphp

@extends('layouts.app', [
    'title' => 'LMS',
    'eyebrow' => $isSuperAdminLms ? 'Monitoring LMS multi sekolah' : ($isAdminLms ? 'Kontrol LMS sekolah' : ($isStudentLms ? 'Ruang belajar siswa SD' : ($isTeacherLms ? 'Rencana mengajar wali kelas' : 'Materi dan tugas')))
])

@section('content')
@if($isSuperAdminLms)
    <section class="surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Panel pusat LMS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-ocean">Monitoring Pembelajaran Semua Sekolah</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Pantau pemakaian LMS lintas sekolah tanpa masuk ke ruang kelas masing-masing. Data ini membantu melihat sekolah aktif, kelas yang sudah berjalan, tugas guru, dan jawaban siswa.
                </p>
            </div>
            <a href="{{ route('admin.schools.index') }}" class="btn-soft px-4 py-2 text-sm">Verifikasi Sekolah</a>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['Sekolah aktif', $lmsStats['active_schools']],
                ['Menunggu verifikasi', $lmsStats['pending_schools']],
                ['Total siswa', $lmsStats['students']],
                ['Total guru', $lmsStats['teachers']],
                ['Kelas terdata', $lmsStats['classes']],
                ['Tugas LMS', $lmsStats['assignments']],
                ['Jawaban masuk', $lmsStats['submissions']],
                ['Sudah dinilai', $lmsStats['graded_submissions']],
            ] as [$label, $value])
                <div class="rounded-xl border border-teal-100 bg-white/75 p-4">
                    <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-ocean">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6 grid gap-5 xl:grid-cols-[1.2fr_.8fr]">
        <div class="surface rounded-xl p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-brand">Per sekolah</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Kesehatan LMS Sekolah</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Sekolah aktif idealnya sudah punya guru, siswa, kelas, dan aktivitas tugas.</p>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $schoolSummaries->count() }} sekolah</span>
            </div>

            <div class="mt-5 overflow-x-auto rounded-xl border border-teal-100">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead class="bg-emerald-50 text-slate-500">
                        <tr>
                            <th class="py-3 pl-4">Sekolah</th>
                            <th>Status</th>
                            <th>Kelas</th>
                            <th>Guru</th>
                            <th>Siswa</th>
                            <th>Tugas</th>
                            <th>Jawaban</th>
                            <th>Aktivitas terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-100/80 bg-white/40">
                        @forelse($schoolSummaries as $summary)
                            @php
                                $school = $summary['school'];
                                $isHealthy = $school->status === 'active' && $summary['teachers'] > 0 && $summary['students'] > 0 && $summary['assignments'] > 0;
                            @endphp
                            <tr>
                                <td class="py-3 pl-4">
                                    <p class="font-extrabold text-slate-900">{{ $school->name }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $school->slug }}</p>
                                </td>
                                <td>
                                    <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $school->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($school->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ ucfirst($school->status) }}
                                    </span>
                                </td>
                                <td>{{ $summary['classes'] }}</td>
                                <td>{{ $summary['teachers'] }}</td>
                                <td>{{ $summary['students'] }}</td>
                                <td>{{ $summary['assignments'] }}</td>
                                <td>{{ $summary['submissions'] }}</td>
                                <td>
                                    @if($summary['last_assignment'])
                                        <p class="font-bold text-slate-700">{{ $summary['last_assignment']->title }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $summary['last_assignment']->created_at->format('d M Y H:i') }}</p>
                                    @else
                                        <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $isHealthy ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $school->status === 'active' ? 'Belum ada tugas' : 'Belum aktif' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-4 text-sm font-semibold text-slate-500">Belum ada data sekolah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="surface rounded-xl p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-brand">Aktivitas terbaru</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Tugas Lintas Sekolah</h2>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $assignments->count() }} terbaru</span>
            </div>

            <div class="mt-4 max-h-[560px] space-y-3 overflow-auto pr-1">
                @forelse($assignments as $assignment)
                    <article class="rounded-xl border border-teal-100 bg-white/75 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ $schoolNames[$assignment->school_id] ?? 'Sekolah' }}</p>
                                <h3 class="mt-1 font-extrabold text-slate-900">{{ $assignment->title }}</h3>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $assignment->class_name }} / {{ $assignment->subject }} / {{ optional($assignment->teacher)->name ?? 'Guru' }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-slate-600">{{ optional($assignment->due_date)->format('d M Y') ?? 'Tanpa batas' }}</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $assignment->instructions }}</p>
                    </article>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada tugas LMS dari sekolah mana pun.</p>
                @endforelse
            </div>
        </div>
    </section>
@elseif($isAdminLms)
    <section class="surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">Panel admin LMS</p>
                <h2 class="mt-1 text-2xl font-extrabold text-ocean">Kontrol Layanan Belajar</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Pantau kesiapan LMS sekolah, tugas yang diterbitkan guru, dan kirim pengumuman LMS ke guru, siswa, serta orang tua.
                </p>
            </div>
            <span class="rounded-full bg-teal-50 px-4 py-2 text-sm font-extrabold text-teal-800">{{ $lmsStats['assignments'] }} tugas LMS</span>
        </div>
    </section>

    <section class="mt-6 grid gap-5 xl:grid-cols-[.85fr_1.15fr]">
        <form method="post" action="{{ route('lms.announcements.send') }}" class="surface rounded-xl p-5">
            @csrf
            <div>
                <p class="text-sm font-extrabold text-brand">Pengumuman LMS</p>
                <h2 class="mt-1 text-xl font-extrabold text-ocean">Kirim ke Semua User</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Cocok untuk info jadwal belajar, perawatan LMS, atau arahan penggunaan tugas online.</p>
            </div>

            @if($errors->any())
                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
            @endif

            <label class="mt-4 block text-sm font-semibold text-slate-700">Judul
                <input name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Contoh: Jadwal belajar pekan ini" required>
            </label>
            <label class="mt-3 block text-sm font-semibold text-slate-700">Isi pengumuman
                <textarea name="body" rows="5" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Tulis informasi LMS yang perlu diketahui guru, siswa, dan orang tua." required>{{ old('body') }}</textarea>
            </label>
            <button class="btn-primary mt-4 w-full px-4 py-3 text-sm">Kirim Pengumuman LMS</button>
        </form>

        <div class="surface rounded-xl p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-brand">Tugas terbaru</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Aktivitas dari Guru</h2>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $assignments->count() }} terbaru</span>
            </div>

            <div class="mt-4 max-h-[420px] space-y-3 overflow-auto pr-1">
                @forelse($assignments as $assignment)
                    <article class="rounded-xl border border-teal-100 bg-white/75 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ $assignment->class_name }} / {{ $assignment->subject }}</p>
                                <h3 class="mt-1 font-extrabold text-slate-900">{{ $assignment->title }}</h3>
                                <p class="mt-1 text-xs font-semibold text-slate-500">Guru: {{ optional($assignment->teacher)->name ?? '-' }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-slate-600">{{ optional($assignment->due_date)->format('d M Y') ?? 'Tanpa batas' }}</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $assignment->instructions }}</p>
                    </article>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada tugas dari guru.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-sm font-extrabold text-brand">Sebaran kelas</p>
                <h2 class="mt-1 text-xl font-extrabold text-ocean">Monitoring Layanan LMS</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Lihat jumlah siswa, wali kelas, dan tugas LMS per kelas.</p>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $classSummaries->count() }} kelas</span>
        </div>

        <div class="mt-5 overflow-x-auto rounded-xl border border-teal-100">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-emerald-50 text-slate-500">
                    <tr>
                        <th class="py-3 pl-4">Kelas</th>
                        <th>Wali Kelas</th>
                        <th>Siswa</th>
                        <th>Tugas LMS</th>
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
                                    {{ $class['teachers']->isNotEmpty() && $class['assignments'] > 0 ? 'Aktif' : 'Perlu dipantau' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-sm font-semibold text-slate-500">Belum ada data kelas siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@elseif($isStudentLms || $isTeacherLms)
    <section class="surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold text-brand">
                    {{ $isTeacherLms ? 'Kelas perwalian '.(auth()->user()->class_name ?? 'belum diatur') : 'Halo, '.auth()->user()->name }}
                </p>
                <h2 class="mt-1 text-2xl font-extrabold text-ocean">
                    {{ $isTeacherLms ? 'Rencana Mengajar Hari Ini' : 'Belajar Hari Ini' }}
                </h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    @if($isTeacherLms)
                        Materi disesuaikan dengan tampilan LMS siswa. Gunakan checklist untuk persiapan mengajar, pantau aktivitas, lalu catat tindak lanjut pembelajaran.
                    @else
                        Pilih materi sesuai kelas, kerjakan aktivitas singkat, lalu centang jika sudah selesai. Checklist tersimpan di perangkat yang dipakai.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-teal-50 px-4 py-2 text-sm font-extrabold text-teal-800">
                    {{ $studentGrade ? 'Kelas '.$studentGrade : (auth()->user()->class_name ?? 'SD') }}
                </span>
                @if($isTeacherLms)
                    <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-extrabold text-emerald-800">
                        {{ $classStudentCount }} siswa
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-3">
            @foreach($todayTasks as $index => $task)
                <label class="flex items-start gap-3 rounded-xl border border-teal-100 bg-white/75 p-4 text-sm font-semibold text-slate-700">
                    <input type="checkbox" data-lms-check="{{ $isTeacherLms ? 'teacher' : 'student' }}-task-{{ $index }}" class="mt-1 h-4 w-4 rounded border-slate-300 text-brand">
                    <span>{{ $task }}</span>
                </label>
            @endforeach
        </div>
    </section>

    @if($isTeacherLms)
        <section class="mt-6 grid gap-4 md:grid-cols-3">
            <a href="{{ route('communication') }}" class="surface rounded-xl p-4">
                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Komunikasi</p>
                <h2 class="mt-2 text-lg font-extrabold text-slate-900">Kirim pesan kelas</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Hubungi siswa perwalian yang perlu bantuan belajar atau tindak lanjut.</p>
            </a>
            <a href="{{ route('character.create') }}" class="surface rounded-xl p-4">
                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Karakter</p>
                <h2 class="mt-2 text-lg font-extrabold text-slate-900">Input poin siswa</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Catat prestasi, kedisiplinan, atau pelanggaran setelah kegiatan belajar.</p>
            </a>
            <div class="surface rounded-xl p-4">
                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Fokus hari ini</p>
                <h2 class="mt-2 text-lg font-extrabold text-slate-900">Belajar singkat</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Pakai aktivitas 10-20 menit agar siswa SD tetap mudah mengikuti materi.</p>
            </div>
        </section>

        <section class="mt-6 grid gap-4 xl:grid-cols-[.9fr_1.1fr]">
            <form method="post" action="{{ route('lms.assignments.store') }}" class="surface rounded-xl p-5">
                @csrf
                <div>
                    <p class="text-sm font-extrabold text-brand">Input tugas / soal</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Buat Aktivitas LMS Siswa</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Tugas akan tampil di LMS siswa kelas perwalian dan mengirim pemberitahuan ke siswa serta orang tua.</p>
                </div>

                @if($errors->any())
                    <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
                @endif

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm font-semibold text-slate-700">Mata pelajaran
                        <select name="subject" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                            @foreach(['Matematika', 'Bahasa Indonesia', 'IPAS', 'Pendidikan Karakter'] as $subject)
                                <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-slate-700">Jenis
                        <select name="type" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" required>
                            <option value="tugas" @selected(old('type') === 'tugas')>Tugas latihan</option>
                            <option value="soal" @selected(old('type') === 'soal')>Soal singkat</option>
                            <option value="refleksi" @selected(old('type') === 'refleksi')>Refleksi belajar</option>
                        </select>
                    </label>
                </div>

                <label class="mt-3 block text-sm font-semibold text-slate-700">Judul
                    <input name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Contoh: Latihan pecahan sederhana" required>
                </label>

                <label class="mt-3 block text-sm font-semibold text-slate-700">Instruksi untuk siswa
                    <textarea name="instructions" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Contoh: Kerjakan soal di buku tulis. Tulis langkah pengerjaan dengan rapi." required>{{ old('instructions') }}</textarea>
                </label>

                <label class="mt-3 block text-sm font-semibold text-slate-700">Soal / pertanyaan utama
                    <textarea name="question" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Contoh: Ibu membeli 1/2 kg apel dan 1/4 kg jeruk. Berapa kg buah semuanya?">{{ old('question') }}</textarea>
                </label>

                <label class="mt-3 block text-sm font-semibold text-slate-700">Batas waktu
                    <input name="due_date" type="date" value="{{ old('due_date') }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3">
                </label>

                <button class="btn-primary mt-4 w-full px-4 py-3 text-sm">Terbitkan ke LMS Siswa</button>
            </form>

            <div class="surface rounded-xl p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-extrabold text-brand">Riwayat tugas</p>
                        <h2 class="mt-1 text-xl font-extrabold text-ocean">Tugas Kelas Ini</h2>
                    </div>
                    <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $assignments->count() }} tugas</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($assignments as $assignment)
                        <article class="rounded-xl border border-teal-100 bg-white/75 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ $assignment->subject }} / {{ ucfirst($assignment->type) }}</p>
                                    <h3 class="mt-1 font-extrabold text-slate-900">{{ $assignment->title }}</h3>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-slate-600">{{ optional($assignment->due_date)->format('d M Y') ?? 'Tanpa batas' }}</span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $assignment->instructions }}</p>
                            <div class="mt-4 rounded-lg border border-teal-100 bg-teal-50/60 p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Jawaban masuk</p>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-teal-800">{{ $assignment->submissions->count() }} jawaban</span>
                                </div>

                                <div class="mt-3 space-y-3">
                                    @forelse($assignment->submissions as $submission)
                                        <article class="rounded-lg border border-white/80 bg-white/80 p-3">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-extrabold text-slate-900">{{ optional($submission->student)->name ?? 'Siswa' }}</p>
                                                    <p class="text-xs font-semibold text-slate-500">{{ optional($submission->submitted_at)->format('d M Y H:i') ?? '-' }}</p>
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $submission->graded_at ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $submission->graded_at ? 'Sudah dinilai: '.number_format((float) $submission->score, 0) : 'Menunggu nilai' }}
                                                </span>
                                            </div>
                                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $submission->answer }}</p>

                                            <form method="post" action="{{ route('lms.submissions.grade', $submission) }}" class="mt-3 grid gap-3 md:grid-cols-[120px_1fr_auto] md:items-end">
                                                @csrf
                                                <label class="text-sm font-semibold text-slate-700">Nilai
                                                    <input name="score" type="number" min="0" max="100" step="0.01" value="{{ old('score', $submission->score) }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2" required>
                                                </label>
                                                <label class="text-sm font-semibold text-slate-700">Catatan guru
                                                    <input name="feedback" value="{{ old('feedback', $submission->feedback) }}" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2" placeholder="Contoh: Sudah baik, perhatikan langkah kedua.">
                                                </label>
                                                <button class="btn-primary px-4 py-2 text-sm">Kirim Nilai</button>
                                            </form>
                                        </article>
                                    @empty
                                        <p class="rounded-lg bg-white/70 p-3 text-sm font-semibold text-slate-500">Belum ada jawaban siswa untuk tugas ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada tugas yang dibuat.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    @if($isStudentLms)
        <section class="mt-6 surface rounded-xl p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-brand">Tugas dari guru</p>
                    <h2 class="mt-1 text-xl font-extrabold text-ocean">Aktivitas Kelas</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Kerjakan tugas sesuai instruksi guru. Centang selesai sebagai pengingat di perangkatmu.</p>
                </div>
                <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $assignments->count() }} tugas</span>
            </div>

            <div class="mt-4 grid gap-3 lg:grid-cols-2">
                @forelse($assignments as $assignment)
                    @php
                        $submission = $assignment->submissions->first();
                    @endphp
                    <article class="rounded-xl border border-teal-100 bg-white/75 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wide text-brand">{{ $assignment->subject }} / {{ ucfirst($assignment->type) }}</p>
                                <h3 class="mt-1 font-extrabold text-slate-900">{{ $assignment->title }}</h3>
                                <p class="mt-1 text-xs font-semibold text-slate-500">Dari {{ optional($assignment->teacher)->name ?? 'Guru' }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-slate-600">{{ optional($assignment->due_date)->format('d M Y') ?? 'Tanpa batas' }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-700">{{ $assignment->instructions }}</p>
                        @if($assignment->question)
                            <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-amber-700">Soal</p>
                                <p class="mt-1 text-sm leading-6 text-slate-700">{{ $assignment->question }}</p>
                            </div>
                        @endif

                        @if($submission && $submission->graded_at)
                            <div class="mt-3 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-emerald-700">Sudah dinilai</p>
                                <p class="mt-1 text-sm font-bold text-emerald-900">Nilai: {{ number_format((float) $submission->score, 0) }}</p>
                                @if($submission->feedback)
                                    <p class="mt-1 text-sm leading-6 text-emerald-800">{{ $submission->feedback }}</p>
                                @endif
                            </div>
                        @elseif($submission)
                            <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">
                                Jawaban sudah dikirim dan sedang menunggu nilai guru.
                            </div>
                        @endif

                        <form method="post" action="{{ route('lms.assignments.submit', $assignment) }}" class="mt-3">
                            @csrf
                            <label class="block text-sm font-semibold text-slate-700">Jawaban saya
                                <textarea name="answer" rows="4" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" placeholder="Tulis jawaban lengkap di sini..." required>{{ old('answer', optional($submission)->answer) }}</textarea>
                            </label>
                            <button class="btn-primary mt-3 w-full px-4 py-3 text-sm">{{ $submission ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}</button>
                        </form>
                    </article>
                @empty
                    <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500 lg:col-span-2">Belum ada tugas dari guru.</p>
                @endforelse
            </div>
        </section>
    @endif

    <section class="mt-6 grid gap-4 lg:grid-cols-[1fr_.8fr]">
        <div class="surface rounded-xl p-5">
            <h2 class="text-lg font-bold">{{ $isTeacherLms ? 'Catatan Guru' : 'Catatan Belajar' }}</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $isTeacherLms ? 'Tulis catatan pengamatan kelas, siswa yang perlu dibantu, atau rencana tindak lanjut.' : 'Tulis hal yang sudah dipahami atau pertanyaan untuk ditanyakan kepada guru.' }}
            </p>
            <textarea data-lms-note rows="5" class="mt-4 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $isTeacherLms ? 'Contoh: Hari ini fokus pecahan. Tiga siswa perlu pendampingan soal cerita.' : 'Contoh: Saya sudah paham pecahan setengah, tetapi masih bingung soal cerita.' }}"></textarea>
            <p class="mt-2 text-xs font-semibold text-slate-500">Catatan ini tersimpan di perangkat yang dipakai.</p>
        </div>

        <div class="surface rounded-xl p-5">
            <h2 class="text-lg font-bold">{{ $isTeacherLms ? 'Target Pendampingan' : 'Target Baik Hari Ini' }}</h2>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-700">
                @if($isTeacherLms)
                    <p class="rounded-lg bg-white/75 px-3 py-2">Memberi contoh singkat sebelum latihan.</p>
                    <p class="rounded-lg bg-white/75 px-3 py-2">Mendampingi siswa yang belum paham.</p>
                    <p class="rounded-lg bg-white/75 px-3 py-2">Menutup pelajaran dengan refleksi satu kalimat.</p>
                @else
                    <p class="rounded-lg bg-white/75 px-3 py-2">Mengerjakan tugas dengan jujur.</p>
                    <p class="rounded-lg bg-white/75 px-3 py-2">Bertanya jika belum paham.</p>
                    <p class="rounded-lg bg-white/75 px-3 py-2">Merapikan buku setelah belajar.</p>
                @endif
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const prefix = 'superapp.lms.{{ auth()->id() }}.{{ $isTeacherLms ? 'teacher' : 'student' }}.';

            document.querySelectorAll('[data-lms-check]').forEach(function (checkbox) {
                const key = prefix + checkbox.dataset.lmsCheck;
                checkbox.checked = localStorage.getItem(key) === '1';
                checkbox.addEventListener('change', function () {
                    localStorage.setItem(key, checkbox.checked ? '1' : '0');
                });
            });

            const note = document.querySelector('[data-lms-note]');
            if (note) {
                const noteKey = prefix + 'note';
                note.value = localStorage.getItem(noteKey) || '';
                note.addEventListener('input', function () {
                    localStorage.setItem(noteKey, note.value);
                });
            }
        });
    </script>
@else
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($courses as $course)
            <article class="surface rounded-xl p-5">
                <p class="text-sm font-bold text-brand">{{ $course->class_name }}</p>
                <h2 class="mt-2 text-lg font-bold">{{ $course->name }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $course->description }}</p>
            </article>
        @empty
            <p class="surface rounded-xl p-5 text-sm text-slate-500">Belum ada kelas LMS.</p>
        @endforelse
    </div>
@endif
@endsection
