@php
    $links = [
        ['Dashboard', 'dashboard', 'Dashboard'],
        ['Absensi', 'attendance', 'Absensi'],
        ['LMS', 'lms', 'LMS'],
        ['Nilai', 'grades', 'Nilai'],
        ['Karakter & Sanksi', 'character.index', 'Karakter'],
        ['Komunikasi', 'communication', 'Komunikasi'],
        ['Berita Sekolah', 'news.index', 'Berita'],
    ];
@endphp

@foreach($links as [$label, $route, $match])
    <a href="{{ route($route) }}" class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition duration-200 {{ request()->routeIs($route) || request()->is(strtolower($match).'*') ? 'bg-gradient-to-r from-teal-600 to-cyan-700 text-white shadow-glow' : 'text-slate-600 hover:bg-white hover:text-ocean hover:shadow-soft' }}">
        <span>{{ $label }}</span>
        <span class="h-2 w-2 rounded-full transition {{ request()->routeIs($route) ? 'bg-amber-300' : 'bg-slate-300 group-hover:bg-teal-400' }}"></span>
    </a>
@endforeach

