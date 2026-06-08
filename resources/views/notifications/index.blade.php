@extends('layouts.app', ['title' => 'Notifikasi', 'eyebrow' => 'Pelanggaran, sanksi, dan info sekolah'])

@section('content')
<section class="surface rounded-xl p-5">
    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php $color = $notification->level === 'danger' ? 'rose' : ($notification->level === 'warning' ? 'amber' : 'sky'); @endphp
            <article class="rounded-lg border border-{{ $color }}-100 bg-{{ $color }}-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-bold text-{{ $color }}-900">{{ $notification->title }}</h2>
                    <span class="text-xs font-semibold text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-2 text-sm text-{{ $color }}-800">{{ $notification->message }}</p>
            </article>
        @empty
            <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">Belum ada notifikasi.</p>
        @endforelse
    </div>
    <div class="mt-5">{{ $notifications->links() }}</div>
</section>
@endsection

