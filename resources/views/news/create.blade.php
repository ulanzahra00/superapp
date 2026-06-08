@extends('layouts.app', ['title' => 'Tulis Berita Sekolah', 'eyebrow' => 'Publikasi internal'])

@section('content')
<section class="surface rounded-xl p-5">
    <form method="post" action="{{ route('news.store') }}" class="grid gap-4 sm:grid-cols-2">
        @csrf
        <label class="text-sm font-semibold sm:col-span-2">Judul
            <input name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
        </label>
        <label class="text-sm font-semibold">Kategori
            <input name="category" value="{{ old('category', 'Pengumuman') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>
        </label>
        <label class="text-sm font-semibold">Warna cover
            <select name="cover_color" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3">
                <option value="emerald">Hijau</option>
                <option value="sky">Biru</option>
                <option value="amber">Kuning</option>
                <option value="rose">Merah</option>
                <option value="slate">Gelap</option>
            </select>
        </label>
        <label class="text-sm font-semibold sm:col-span-2">Ringkasan
            <textarea name="excerpt" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>{{ old('excerpt') }}</textarea>
        </label>
        <label class="text-sm font-semibold sm:col-span-2">Konten
            <textarea name="content" rows="10" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required>{{ old('content') }}</textarea>
        </label>
        @if($errors->any())<div class="sm:col-span-2 rounded-lg bg-rose-50 p-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
        <button class="sm:col-span-2 btn-primary px-4 py-3">Publikasikan</button>
    </form>
</section>
@endsection

