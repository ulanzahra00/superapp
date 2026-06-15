@extends('layouts.app', ['title' => 'Dashboard', 'eyebrow' => 'Ringkasan peran '.ucfirst(str_replace('_', ' ', auth()->user()->role))])

@section('content')
@php
    $characterColor = function ($score) {
        return $score <= -100 ? 'rose' : ($score <= -20 ? 'amber' : 'emerald');
    };
    $showSidePanel = auth()->user()->hasRole('admin');
    $studentModalData = $students->map(function ($student) {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'nis' => $student->nis,
            'class_name' => $student->class_name,
            'total_points' => (int) ($student->total_points ?? 0),
            'pdf_url' => route('character.report', $student),
            'points' => $student->studentPoints->map(function ($point) {
                return [
                    'type' => $point->type,
                    'category' => $point->category,
                    'point' => $point->point,
                    'title' => $point->title,
                    'description' => $point->description,
                    'teacher' => optional($point->teacher)->name,
                    'date' => optional($point->occurred_at)->format('d M Y'),
                ];
            })->values(),
            'sanctions' => $student->sanctions->map(function ($sanction) {
                return [
                    'type' => $sanction->sanction_type,
                    'total_points' => $sanction->total_points,
                    'note' => $sanction->note,
                    'date' => optional($sanction->created_at)->format('d M Y'),
                ];
            })->values(),
        ];
    })->values();
@endphp

@if(auth()->user()->hasRole('admin'))
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
@endif

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

        <p class="mt-3 text-xs text-slate-500">Kolom wajib: nama_siswa, nis, kelas. Kolom opsional: email_siswa, nama_orang_tua, email_orang_tua, telepon, password. Jika email siswa kosong atau duplikat, sistem membuat email otomatis dari NIS.</p>
    </section>
@endif

<div class="mt-6 grid gap-6 {{ $showSidePanel ? 'xl:grid-cols-[1.2fr_.8fr]' : '' }}">
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

        <div class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="text-slate-500">
                    <tr>
                        @if(auth()->user()->hasRole('admin'))
                            <th class="py-3 w-10">Pilih</th>
                        @endif
                        <th class="py-3">Siswa</th>
                        <th>Kelas</th>
                        <th>Total Poin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-100/80">
                    @forelse($students as $student)
                        @php
                            $score = (int) ($student->total_points ?? $student->characterScore());
                            $color = $characterColor($score);
                            $label = $score <= -100 ? 'Bahaya' : ($score <= -20 ? 'Perlu tindak lanjut' : 'Kondisi baik');
                        @endphp
                        <tr>
                            @if(auth()->user()->hasRole('admin'))
                                <td class="py-3">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" data-student-checkbox class="h-4 w-4 rounded border-slate-300 text-brand">
                                </td>
                            @endif
                            <td class="py-3">
                                <button type="button" data-student-detail="{{ $student->id }}" class="text-left font-extrabold text-slate-900 transition hover:text-brand">{{ $student->name }}</button>
                                <p class="text-xs font-semibold text-slate-500">NIS: {{ $student->nis ?? '-' }}</p>
                            </td>
                            <td class="font-semibold text-slate-700">{{ $student->class_name ?? 'Belum ada kelas' }}</td>
                            <td>
                                <span class="{{ $score < 0 ? 'text-rose-700' : 'text-emerald-700' }} text-lg font-extrabold">{{ $score }}</span>
                                <span class="text-xs font-semibold text-slate-500">poin</span>
                            </td>
                            <td>
                                <span class="rounded-full bg-{{ $color }}-100 px-3 py-1 text-xs font-extrabold text-{{ $color }}-800">{{ $label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('admin') ? 5 : 4 }}" class="py-6 text-center text-sm font-semibold text-slate-500">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(auth()->user()->hasRole('admin'))
            </form>
        @endif
    </section>

    @if($showSidePanel)
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
                        <a href="{{ route('news.show', $item) }}" class="grid gap-3 rounded-xl border border-slate-200/80 bg-white/80 p-3 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-teal-300 hover:shadow-soft sm:grid-cols-[96px_1fr]">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-20 w-full rounded-lg object-cover">
                            @else
                                <div class="h-20 rounded-lg bg-{{ $item->cover_color }}-500"></div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-brand">{{ $item->category }}</p>
                                <p class="font-bold">{{ $item->title }}</p>
                                <p class="mt-1 line-clamp-2 text-justify text-xs leading-5 text-slate-500">{{ $item->excerpt }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>

@if(auth()->user()->hasRole('guru'))
    <section class="mt-6 surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Respons Tindak Lanjut Perwalian</h2>
                <p class="mt-1 text-sm text-slate-500">Kirim respons pembinaan kepada siswa perwalian yang membutuhkan tindak lanjut. Pesan juga dikirim ke orang tua jika akun orang tua tersedia.</p>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-extrabold text-amber-800">{{ $followUpStudents->count() }} siswa</span>
        </div>

        @if($errors->has('body'))
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first('body') }}</div>
        @endif

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            @forelse($followUpStudents as $student)
                @php
                    $score = (int) ($student->total_points ?? 0);
                    $latestViolation = $student->studentPoints->firstWhere('type', 'pelanggaran');
                    $latestSanction = $student->sanctions->first();
                @endphp
                <article class="rounded-xl border border-amber-100 bg-amber-50/70 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-extrabold text-slate-900">{{ $student->name }}</h3>
                            <p class="text-xs font-semibold text-slate-500">NIS: {{ $student->nis ?? '-' }} / {{ $student->class_name ?? 'Belum ada kelas' }}</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-sm font-extrabold text-rose-700">{{ $score }} poin</span>
                    </div>

                    <div class="mt-3 rounded-lg border border-white/70 bg-white/70 px-3 py-2 text-sm text-slate-700">
                        <p class="font-bold text-slate-900">Ringkasan terakhir</p>
                        <p class="mt-1">
                            {{ optional($latestViolation)->title ?? 'Belum ada catatan pelanggaran terbaru.' }}
                            @if($latestSanction)
                                / {{ $latestSanction->sanction_type }}
                            @endif
                        </p>
                    </div>

                    <form method="post" action="{{ route('dashboard.follow-up.respond', $student) }}" class="mt-4 space-y-3">
                        @csrf
                        <label class="block text-sm font-semibold text-slate-700">Respons wali kelas
                            <textarea name="body" rows="3" class="mt-2 w-full rounded-lg border border-amber-200 bg-white px-4 py-3 text-sm" placeholder="Contoh: Ananda diminta mengikuti pembinaan wali kelas besok pukul 09.00 dan membawa buku catatan. Orang tua mohon memantau kehadiran dan komunikasi anak." required></textarea>
                        </label>
                        <button class="btn-primary w-full px-4 py-3 text-sm">Kirim Respons Tindak Lanjut</button>
                    </form>
                </article>
            @empty
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 lg:col-span-2">
                    Belum ada siswa perwalian yang membutuhkan tindak lanjut saat ini.
                </div>
            @endforelse
        </div>
    </section>
@endif

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

<div data-student-modal class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/55 px-4 py-6">
    <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-100 bg-white px-5 py-4">
            <div>
                <p data-modal-class class="text-sm font-extrabold text-brand"></p>
                <h2 data-modal-name class="text-2xl font-extrabold text-ocean"></h2>
                <p data-modal-nis class="mt-1 text-sm font-semibold text-slate-500"></p>
            </div>
            <button type="button" data-student-modal-close class="btn-soft px-3 py-2 text-sm">Tutup</button>
        </div>
        <div class="space-y-5 p-5">
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-teal-100 bg-emerald-50 p-4">
                    <p class="text-xs font-extrabold uppercase text-slate-500">Total poin</p>
                    <p data-modal-score class="mt-1 text-2xl font-extrabold text-emerald-700"></p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                    <p class="text-xs font-extrabold uppercase text-slate-500">Pelanggaran</p>
                    <p data-modal-violations class="mt-1 text-2xl font-extrabold text-rose-700"></p>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-teal-50 p-4">
                    <p class="text-xs font-extrabold uppercase text-slate-500">Prestasi</p>
                    <p data-modal-achievements class="mt-1 text-2xl font-extrabold text-teal-700"></p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                <div>
                    <p class="text-sm font-extrabold text-slate-900">Laporan karakter siswa</p>
                    <p class="text-xs font-semibold text-slate-500">Unduh riwayat poin dan sanksi dalam format PDF.</p>
                </div>
                <a data-modal-pdf href="#" class="btn-primary px-4 py-2 text-sm">Export PDF</a>
            </div>

            <section>
                <h3 class="text-lg font-extrabold text-slate-900">Riwayat Poin</h3>
                <div data-modal-points class="mt-3 space-y-3"></div>
            </section>

            <section>
                <h3 class="text-lg font-extrabold text-slate-900">Sanksi</h3>
                <div data-modal-sanctions class="mt-3 space-y-3"></div>
            </section>
        </div>
    </div>
</div>

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const students = @json($studentModalData);

        const modal = document.querySelector('[data-student-modal]');
        const closeButton = document.querySelector('[data-student-modal-close]');
        const fields = {
            name: document.querySelector('[data-modal-name]'),
            className: document.querySelector('[data-modal-class]'),
            nis: document.querySelector('[data-modal-nis]'),
            score: document.querySelector('[data-modal-score]'),
            violations: document.querySelector('[data-modal-violations]'),
            achievements: document.querySelector('[data-modal-achievements]'),
            pdf: document.querySelector('[data-modal-pdf]'),
            points: document.querySelector('[data-modal-points]'),
            sanctions: document.querySelector('[data-modal-sanctions]'),
        };

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (char) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
            });
        }

        function emptyState(text) {
            return `<p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-slate-500">${text}</p>`;
        }

        function openModal(student) {
            const violations = student.points.filter((point) => point.type === 'pelanggaran');
            const achievements = student.points.filter((point) => point.type === 'prestasi');

            fields.name.textContent = student.name;
            fields.className.textContent = student.class_name || 'Belum ada kelas';
            fields.nis.textContent = `NIS: ${student.nis || '-'}`;
            fields.score.textContent = `${student.total_points} poin`;
            fields.violations.textContent = violations.length;
            fields.achievements.textContent = achievements.length;
            fields.pdf.href = student.pdf_url;

            fields.points.innerHTML = student.points.length ? student.points.map((point) => {
                const color = point.point < 0 ? 'rose' : 'emerald';
                return `
                    <article class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-extrabold text-slate-900">${escapeHtml(point.title)}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">${escapeHtml(point.category)} / ${escapeHtml(point.teacher || '-')} / ${escapeHtml(point.date || '-')}</p>
                            </div>
                            <span class="rounded-full bg-${color}-100 px-3 py-1 text-sm font-extrabold text-${color}-700">${point.point} poin</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">${escapeHtml(point.description || 'Tidak ada catatan tambahan.')}</p>
                    </article>
                `;
            }).join('') : emptyState('Belum ada riwayat poin.');

            fields.sanctions.innerHTML = student.sanctions.length ? student.sanctions.map((sanction) => `
                <article class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-extrabold text-rose-900">${escapeHtml(sanction.type)}</p>
                            <p class="mt-1 text-xs font-semibold text-rose-700">${escapeHtml(sanction.date || '-')}</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-sm font-extrabold text-rose-700">${sanction.total_points} poin</span>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-rose-700">${escapeHtml(sanction.note || 'Siswa mengikuti pembinaan sesuai total poin pelanggaran.')}</p>
                </article>
            `).join('') : emptyState('Belum ada sanksi.');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        document.querySelectorAll('[data-student-detail]').forEach(function (button) {
            button.addEventListener('click', function () {
                const student = students.find((item) => String(item.id) === String(button.dataset.studentDetail));
                if (student) {
                    openModal(student);
                }
            });
        });

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endsection
