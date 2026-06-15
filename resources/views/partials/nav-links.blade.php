@php
    $isMobileNav = $isMobileNav ?? false;
    $links = [
        ['Dashboard', 'dashboard', 'Dashboard'],
        ['Absensi', 'attendance', 'Absensi'],
        ['LMS', 'lms', 'LMS'],
        ['Nilai', 'grades', 'Nilai'],
        ['Karakter & Sanksi', 'character.index', 'Karakter'],
        ['Komunikasi', 'communication', 'Komunikasi'],
        ['Berita Sekolah', 'news.index', 'Berita'],
    ];

    if (auth()->user()->hasRole('admin')) {
        $links[] = ['Pengaturan User', 'admin.users.index', 'Pengaturan'];
    }

    if (auth()->user()->hasRole('super_admin')) {
        $links[] = ['Verifikasi Sekolah', 'admin.schools.index', 'Sekolah'];
    }

    $linkClass = $isMobileNav
        ? 'group flex min-w-0 items-center justify-between rounded-lg px-3 py-2.5 text-xs font-bold transition duration-200'
        : 'group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-bold transition duration-200';
    $labelClass = $isMobileNav ? 'min-w-0 truncate' : '';
    $dotClass = $isMobileNav
        ? 'ml-2 h-2 w-2 shrink-0 rounded-full transition'
        : 'h-2 w-2 rounded-full transition';
@endphp

@foreach($links as [$label, $route, $match])
    <a href="{{ route($route) }}" class="{{ $linkClass }} {{ request()->routeIs($route) || request()->is(strtolower($match).'*') ? 'bg-gradient-to-r from-teal-600 to-cyan-700 text-white shadow-glow' : 'text-slate-600 hover:bg-white hover:text-ocean hover:shadow-soft' }}">
        <span class="{{ $labelClass }}">{{ $label }}</span>
        <span class="{{ $dotClass }} {{ request()->routeIs($route) ? 'bg-amber-300' : 'bg-slate-300 group-hover:bg-teal-400' }}"></span>
    </a>
@endforeach
