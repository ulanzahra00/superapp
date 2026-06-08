@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-100 px-4 py-10">
    <div class="surface mx-auto max-w-2xl rounded-2xl p-6 shadow-xl">
        <h1 class="text-2xl font-bold">Register Pengguna</h1>
        <form method="post" action="{{ route('register') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
            @csrf
            <label class="text-sm font-semibold sm:col-span-2">Nama
                <input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold sm:col-span-2">Email
                <input name="email" type="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold">Role
                <select name="role" class="mt-2 w-full rounded-lg border px-4 py-3">
                    <option value="siswa">Siswa</option>
                    <option value="orang_tua">Orang tua</option>
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                </select>
            </label>
            <label class="text-sm font-semibold">Kelas
                <input name="class_name" value="{{ old('class_name') }}" class="mt-2 w-full rounded-lg border px-4 py-3">
            </label>
            <label class="text-sm font-semibold">NIS
                <input name="nis" value="{{ old('nis') }}" class="mt-2 w-full rounded-lg border px-4 py-3">
            </label>
            <label class="text-sm font-semibold">Telepon
                <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border px-4 py-3">
            </label>
            <label class="text-sm font-semibold">Password
                <input name="password" type="password" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            </label>
            <label class="text-sm font-semibold">Konfirmasi
                <input name="password_confirmation" type="password" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            </label>
            @if($errors->any())<div class="sm:col-span-2 rounded-lg bg-rose-50 p-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
            <button class="sm:col-span-2 btn-dark px-4 py-3">Daftar</button>
        </form>
    </div>
</div>
@endsection

