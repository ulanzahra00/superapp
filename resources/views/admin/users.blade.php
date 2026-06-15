@extends('layouts.app', ['title' => 'Pengaturan User', 'eyebrow' => 'Tambah akun guru, siswa, dan orang tua'])

@section('content')
<div class="space-y-6">
    <section class="surface rounded-xl p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Tambah User</h2>
                <p class="mt-1 text-sm text-slate-500">Password boleh dikosongkan, sistem akan memakai password default: password.</p>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-extrabold text-teal-800">Admin</span>
        </div>

        <form method="post" action="{{ route('admin.users.store') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <label class="text-sm font-semibold">Role
                <select name="role" data-user-role class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="orang_tua" {{ old('role') === 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                </select>
            </label>

            <label class="text-sm font-semibold">Nama
                <input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
            </label>

            <label class="text-sm font-semibold">Email
                <input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
            </label>

            <label class="text-sm font-semibold" data-siswa-field>NIS
                <input name="nis" value="{{ old('nis') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3">
            </label>

            <label class="text-sm font-semibold" data-class-field>Kelas / Perwalian
                <input name="class_name" list="class-options" value="{{ old('class_name') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" placeholder="Contoh: Kelas 4 A">
                <datalist id="class-options">
                    @foreach($classes as $class)
                        <option value="{{ $class }}"></option>
                    @endforeach
                </datalist>
            </label>

            <label class="text-sm font-semibold md:col-span-2 xl:col-span-3" data-parent-field>Orang tua siswa
                <select name="parent_id" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3">
                    <option value="">Tidak dihubungkan dulu</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ (string) old('parent_id') === (string) $parent->id ? 'selected' : '' }}>{{ $parent->name }} - {{ $parent->email }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-semibold">Telepon
                <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3">
            </label>

            <label class="text-sm font-semibold">Password
                <input name="password" type="password" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" placeholder="Minimal 6 karakter">
            </label>

            @if($errors->any())
                <div class="md:col-span-2 xl:col-span-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">{{ $errors->first() }}</div>
            @endif

            <button class="btn-primary px-4 py-3 md:col-span-2 xl:col-span-3">Simpan User</button>
        </form>
    </section>

    <section class="surface rounded-xl p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold">Daftar User</h2>
                <p class="text-sm text-slate-500">Guru dan siswa dipisahkan agar data lebih mudah dibaca.</p>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $users->count() }} user</span>
        </div>

        <div class="mt-5 grid gap-5 xl:grid-cols-2">
            <div class="rounded-xl border border-teal-100 bg-white/40 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-ocean">Guru / Wali Kelas</h3>
                        <p class="mt-1 text-sm text-slate-500">Akun guru dan kelas perwalian.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-extrabold text-emerald-800">{{ $teachers->count() }} guru</span>
                </div>

                <div class="mt-4 max-h-[52vh] overflow-auto rounded-lg border border-teal-100">
                    <table class="w-full min-w-[620px] text-left text-sm">
                        <thead class="sticky top-0 bg-emerald-50 text-slate-500">
                            <tr>
                                <th class="py-3 pl-3">Nama</th>
                                <th>Email</th>
                                <th>Kelas Perwalian</th>
                                <th>Telepon</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-teal-100/80 bg-white/40">
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="py-3 pl-3 font-extrabold text-slate-900">{{ $teacher->name }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>{{ $teacher->class_name ?? '-' }}</td>
                                    <td>{{ $teacher->phone ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-sm font-semibold text-slate-500">Belum ada akun guru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-teal-100 bg-white/40 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-ocean">Siswa</h3>
                        <p class="mt-1 text-sm text-slate-500">Akun siswa, NIS, kelas, dan kontak.</p>
                    </div>
                    <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-extrabold text-teal-800">{{ $students->count() }} siswa</span>
                </div>

                <div class="mt-4 max-h-[52vh] overflow-auto rounded-lg border border-teal-100">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="sticky top-0 bg-emerald-50 text-slate-500">
                            <tr>
                                <th class="py-3 pl-3">Nama</th>
                                <th>Email</th>
                                <th>Kelas</th>
                                <th>NIS</th>
                                <th>Telepon</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-teal-100/80 bg-white/40">
                            @forelse($students as $student)
                                <tr>
                                    <td class="py-3 pl-3 font-extrabold text-slate-900">{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->class_name ?? '-' }}</td>
                                    <td>{{ $student->nis ?? '-' }}</td>
                                    <td>{{ $student->phone ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-sm font-semibold text-slate-500">Belum ada akun siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-teal-100 bg-white/40 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="font-extrabold text-ocean">Orang Tua</h3>
                    <p class="mt-1 text-sm text-slate-500">Akun pendamping siswa untuk komunikasi dan pemantauan.</p>
                </div>
                <span class="rounded-full bg-cyan-50 px-3 py-1 text-sm font-extrabold text-cyan-800">{{ $parentsList->count() }} orang tua</span>
            </div>

            <div class="mt-4 max-h-64 overflow-auto rounded-lg border border-teal-100">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="sticky top-0 bg-emerald-50 text-slate-500">
                        <tr>
                            <th class="py-3 pl-3">Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-100/80 bg-white/40">
                        @forelse($parentsList as $parent)
                            <tr>
                                <td class="py-3 pl-3 font-extrabold text-slate-900">{{ $parent->name }}</td>
                                <td>{{ $parent->email }}</td>
                                <td>{{ $parent->phone ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-sm font-semibold text-slate-500">Belum ada akun orang tua.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const role = document.querySelector('[data-user-role]');
        const siswaFields = document.querySelectorAll('[data-siswa-field]');
        const classFields = document.querySelectorAll('[data-class-field]');
        const parentFields = document.querySelectorAll('[data-parent-field]');

        function setVisible(nodes, visible) {
            nodes.forEach(function (node) {
                node.classList.toggle('hidden', ! visible);
            });
        }

        function syncFields() {
            const value = role.value;
            setVisible(siswaFields, value === 'siswa');
            setVisible(parentFields, value === 'siswa');
            setVisible(classFields, value === 'siswa' || value === 'guru');
        }

        if (role) {
            role.addEventListener('change', syncFields);
            syncFields();
        }
    });
</script>
@endsection
