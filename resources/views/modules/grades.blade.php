@extends('layouts.app', ['title' => 'Nilai', 'eyebrow' => 'Rekap akademik'])

@section('content')
<section class="surface rounded-xl p-5">
    <h2 class="text-lg font-bold">Nilai Siswa</h2>
    <p class="mt-2 text-sm text-slate-500">Modul nilai tersambung dengan tabel grades dan courses.</p>
    <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Data contoh tersedia setelah menjalankan seeder.</div>
</section>
@endsection

