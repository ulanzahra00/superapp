@extends('layouts.app', ['title' => 'LMS', 'eyebrow' => 'Materi dan tugas'])

@section('content')
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
@endsection

