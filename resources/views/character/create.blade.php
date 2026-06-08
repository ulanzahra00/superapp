@extends('layouts.app', ['title' => 'Input Karakter Siswa', 'eyebrow' => 'Prestasi dan pelanggaran'])

@section('content')
<div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
    <aside class="surface rounded-xl p-5">
        <h2 class="text-lg font-bold">Contoh Cepat</h2>
        <div class="mt-4 space-y-3">
            @foreach($templates as $template)
                <div class="rounded-xl border border-slate-200/80 bg-white/70 p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-soft">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-bold">{{ $template['title'] }}</p>
                        <span class="{{ $template['point'] < 0 ? 'text-rose-700' : 'text-emerald-700' }} font-bold">{{ $template['point'] }}</span>
                    </div>
                    <p class="text-sm text-slate-500">{{ ucfirst($template['type']) }} / {{ $template['category'] }}</p>
                </div>
            @endforeach
        </div>
    </aside>
    <section class="surface rounded-xl p-5">
        <form method="post" action="{{ route('character.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <label class="text-sm font-semibold sm:col-span-2">Nama siswa
                <select name="student_id" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->class_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold">Jenis
                <select name="type" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    <option value="pelanggaran">Pelanggaran</option>
                    <option value="prestasi">Prestasi</option>
                </select>
            </label>
            <label class="text-sm font-semibold">Kategori
                <select name="category" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
                    <option>Disiplin</option>
                    <option>Tanggung Jawab</option>
                    <option>Kejujuran</option>
                    <option>Kerjasama</option>
                </select>
            </label>
            <label class="text-sm font-semibold">Judul
                <input name="title" value="{{ old('title', 'Terlambat') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold">Poin
                <input name="point" type="number" value="{{ old('point', -5) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold">Tanggal
                <input name="occurred_at" type="date" value="{{ old('occurred_at', now()->toDateString()) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold sm:col-span-2">Catatan
                <textarea name="description" rows="5" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3">{{ old('description') }}</textarea>
            </label>
            @if($errors->any())<div class="sm:col-span-2 rounded-lg bg-rose-50 p-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
            <button class="sm:col-span-2 btn-primary px-4 py-3">Simpan dan cek sanksi otomatis</button>
        </form>
    </section>
</div>
@endsection

