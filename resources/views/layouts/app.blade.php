<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Super App Sekolah' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    colors: {
                        ink: '#17202a',
                        brand: '#0f766e',
                        ocean: '#0f3d5e',
                        gold: '#10b981'
                    },
                    boxShadow: {
                        soft: '0 18px 45px rgba(15, 61, 94, .10)',
                        glow: '0 16px 36px rgba(15, 118, 110, .28)'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --ring: 0 0 0 4px rgba(20, 184, 166, .18);
        }

        body {
            background:
                linear-gradient(135deg, rgba(236, 253, 245, .94), rgba(240, 253, 244, .9) 42%, rgba(204, 251, 241, .58)),
                #f8fafc;
        }

        .app-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 58%, #ccfbf1 100%);
        }

        .surface {
            background: linear-gradient(135deg, rgba(236, 253, 245, .97), rgba(220, 252, 231, .94) 52%, rgba(204, 251, 241, .92)) !important;
            border: 1px solid rgba(20, 184, 166, .34);
            box-shadow: 0 18px 45px rgba(15, 61, 94, .09);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        [class*="bg-white/70"],
        [class*="bg-white/75"],
        [class*="bg-white/80"] {
            background: linear-gradient(135deg, rgba(240, 253, 244, .95), rgba(204, 251, 241, .9)) !important;
            border-color: rgba(20, 184, 166, .28) !important;
        }

        .surface:hover {
            transform: translateY(-2px);
            border-color: rgba(20, 184, 166, .38);
            box-shadow: 0 22px 55px rgba(15, 61, 94, .13);
        }

        .btn-primary,
        .btn-dark,
        .btn-soft,
        .btn-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            overflow: hidden;
            border-radius: .7rem;
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease, border-color .18s ease;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #047857, #0f766e 58%, #10b981);
            box-shadow: 0 14px 28px rgba(15, 118, 110, .26);
        }

        .btn-dark {
            color: #fff;
            background: linear-gradient(135deg, #0f172a, #0f3d5e);
            box-shadow: 0 14px 28px rgba(15, 23, 42, .22);
        }

        .btn-soft {
            color: #0f3d5e;
            background: linear-gradient(135deg, #ffffff, #ecfeff);
            border: 1px solid rgba(14, 116, 144, .18);
            box-shadow: 0 10px 24px rgba(15, 61, 94, .08);
        }

        .btn-link {
            color: #0f766e;
            border-radius: .35rem;
        }

        .btn-primary::after,
        .btn-dark::after,
        .btn-soft::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-115%) skewX(-20deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.28), transparent);
            transition: transform .45s ease;
        }

        .btn-primary:hover,
        .btn-dark:hover,
        .btn-soft:hover,
        .btn-link:hover {
            transform: translateY(-2px);
            filter: saturate(1.08);
        }

        .btn-primary:hover,
        .btn-dark:hover {
            box-shadow: 0 18px 36px rgba(15, 118, 110, .30);
        }

        .btn-primary:hover::after,
        .btn-dark:hover::after,
        .btn-soft:hover::after {
            transform: translateX(115%) skewX(-20deg);
        }

        .btn-primary:active,
        .btn-dark:active,
        .btn-soft:active,
        .btn-link:active {
            transform: translateY(0) scale(.98);
        }

        button:focus-visible,
        a:focus-visible,
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            box-shadow: var(--ring);
            border-color: #14b8a6;
        }

        input,
        select,
        textarea {
            transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        input:hover,
        select:hover,
        textarea:hover {
            background-color: #f8fafc;
        }

        .mobile-tab {
            transition: color .18s ease, background-color .18s ease, transform .18s ease;
        }

        .mobile-tab:hover {
            transform: translateY(-1px);
            background-color: #f0fdfa;
        }

        details[data-mobile-menu] summary::-webkit-details-marker {
            display: none;
        }

        @media (max-width: 640px) {
            main,
            section,
            article,
            form,
            .surface {
                min-width: 0;
            }

            .overflow-x-auto {
                max-width: 100%;
            }

            table {
                min-width: 100% !important;
                table-layout: auto;
            }

            th,
            td {
                padding: .65rem .5rem !important;
                font-size: .72rem;
                line-height: 1.35;
                white-space: normal;
                overflow-wrap: anywhere;
                vertical-align: top;
            }

            th:first-child,
            td:first-child {
                padding-left: .65rem !important;
            }
        }
    </style>
</head>
<body class="text-ink antialiased">
    @include('partials.single-tab-guard')
    @auth
        @php
            $frontHomeRoute = auth()->user()->school
                ? route('public.school.home', ['schoolSlug' => auth()->user()->school->public_slug])
                : route('public.home');
            $mobileNavLinks = [
                ['Home', 'dashboard', 'dashboard'],
                ['Berita', 'news.index', 'news.*'],
                ['Notifikasi', 'notifications', 'notifications'],
            ];

            if (auth()->user()->hasRole('admin')) {
                $mobileNavLinks[] = ['User', 'admin.users.index', 'admin.users.*'];
            }

            if (auth()->user()->hasRole('super_admin')) {
                $mobileNavLinks[] = ['Sekolah', 'admin.schools.index', 'admin.schools.*'];
            }

            $headerActionLinks = [];

            if (auth()->user()->hasRole('admin')) {
                $headerActionLinks[] = ['Pengaturan User', 'admin.users.index'];
            }

            if (auth()->user()->hasRole('super_admin')) {
                $headerActionLinks[] = ['Verifikasi Sekolah', 'admin.schools.index'];
            }
        @endphp
        <div class="min-h-screen lg:flex">
            <aside class="app-sidebar hidden lg:flex lg:w-72 lg:flex-col border-r border-teal-100/80">
                <div class="px-6 py-6">
                    <div class="text-xl font-extrabold tracking-tight text-ocean">Super App Sekolah</div>
                    <div class="mt-2 inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-800">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
                    <a href="{{ $frontHomeRoute }}" class="btn-soft mt-4 w-full px-4 py-2 text-sm">Panel Depan</a>
                </div>
                <nav class="flex-1 px-4 space-y-1">
                    @include('partials.nav-links')
                </nav>
            </aside>

            <main class="min-w-0 flex-1 pb-24 lg:pb-0">
                <header class="sticky top-0 z-20 border-b border-teal-100 bg-white/85 backdrop-blur-xl">
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-brand">{{ $eyebrow ?? 'Monitoring Sekolah' }}</p>
                            <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
                        </div>
                        <div class="hidden lg:flex lg:w-auto lg:flex-wrap lg:items-center lg:justify-end lg:gap-3">
                            @foreach($headerActionLinks as [$label, $route])
                                <a href="{{ route($route) }}" class="btn-primary min-w-0 px-3 py-2 text-center text-xs sm:text-sm">{{ $label }}</a>
                            @endforeach
                            <a href="{{ $frontHomeRoute }}" class="btn-soft min-w-0 px-3 py-2 text-center text-xs sm:text-sm">Panel Depan</a>
                            <a href="{{ route('notifications') }}" class="btn-soft min-w-0 px-3 py-2 text-center text-xs sm:text-sm">Notifikasi</a>
                            <a href="{{ route('profile') }}" class="min-w-0 truncate rounded-lg bg-gradient-to-r from-emerald-100 to-teal-50 px-3 py-2 text-center text-xs font-bold text-ocean ring-1 ring-emerald-200/70 sm:max-w-44 sm:rounded-full sm:text-sm">{{ auth()->user()->name }}</a>
                            <form method="post" action="{{ route('logout') }}" class="min-w-0">
                                @csrf
                                <button class="btn-dark w-full px-3 py-2 text-xs sm:text-sm">Keluar</button>
                            </form>
                        </div>
                        <details data-mobile-menu class="relative lg:hidden">
                            <summary class="btn-primary flex cursor-pointer list-none items-center justify-between px-4 py-3 text-sm">
                                <span>Menu Aplikasi</span>
                                <span class="text-base leading-none">+</span>
                            </summary>
                            <div class="absolute right-0 top-full z-40 mt-3 max-h-[70vh] w-[calc(100vw-2rem)] overflow-y-auto rounded-2xl border border-teal-100 bg-white p-3 shadow-[0_24px_60px_rgba(15,61,94,.20)]">
                                <p class="px-2 pb-2 text-xs font-extrabold uppercase tracking-wide text-brand">Tab utama</p>
                                <nav class="grid grid-cols-2 gap-2">
                                    @include('partials.nav-links', ['isMobileNav' => true])
                                </nav>

                                <div class="mt-3 border-t border-teal-100 pt-3">
                                    <p class="px-2 pb-2 text-xs font-extrabold uppercase tracking-wide text-brand">Akun</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ $frontHomeRoute }}" class="btn-soft min-w-0 px-3 py-2 text-center text-xs">Panel Depan</a>
                                        <a href="{{ route('notifications') }}" class="btn-soft min-w-0 px-3 py-2 text-center text-xs">Notifikasi</a>
                                        <a href="{{ route('profile') }}" class="min-w-0 truncate rounded-lg bg-gradient-to-r from-emerald-100 to-teal-50 px-3 py-2 text-center text-xs font-bold text-ocean ring-1 ring-emerald-200/70">{{ auth()->user()->name }}</a>
                                        <form method="post" action="{{ route('logout') }}" class="min-w-0">
                                            @csrf
                                            <button class="btn-dark w-full px-3 py-2 text-xs">Keluar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>
                </header>

                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @if(session('status'))
                        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 shadow-soft">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </div>
            </main>

            <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-teal-100 bg-white/95 shadow-[0_-12px_30px_rgba(15,61,94,.10)] backdrop-blur lg:hidden">
                <div class="grid grid-cols-{{ count($mobileNavLinks) + 1 }} text-center text-[11px] font-semibold sm:text-xs">
                    @foreach($mobileNavLinks as [$label, $route, $activePattern])
                        <a class="mobile-tab truncate px-1 py-3 {{ request()->routeIs($activePattern) ? 'text-brand' : 'text-slate-500' }}" href="{{ route($route) }}">{{ $label }}</a>
                    @endforeach
                    <a class="mobile-tab truncate px-1 py-3 text-slate-500" href="{{ $frontHomeRoute }}">Depan</a>
                </div>
            </nav>

            @if(!empty($incomingMessagePopups) && $incomingMessagePopups->isNotEmpty())
                <div id="incoming-message-popup" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 px-4 py-6 backdrop-blur-sm">
                    <div class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl border border-teal-100 bg-white shadow-[0_26px_70px_rgba(15,23,42,.24)]">
                        <div class="rounded-t-2xl bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-brand">Pemberitahuan masuk</p>
                                    <h2 class="mt-1 text-xl font-extrabold text-ink">Ada pesan yang perlu dibaca</h2>
                                </div>
                                <button type="button" data-close-message-popup class="rounded-full border border-teal-100 bg-white px-3 py-1 text-sm font-extrabold text-slate-500 hover:text-ink">
                                    Tutup
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4 px-5 py-5">
                            @foreach($incomingMessagePopups as $messagePopup)
                                <div class="rounded-xl border {{ $messagePopup['eyebrow'] === 'Pengumuman sekolah' ? 'border-amber-200 bg-amber-50/80' : 'border-emerald-100 bg-emerald-50/70' }} px-4 py-3">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-xs font-extrabold uppercase tracking-wide {{ $messagePopup['eyebrow'] === 'Pengumuman sekolah' ? 'text-amber-700' : 'text-brand' }}">
                                                {{ $messagePopup['eyebrow'] }}
                                            </p>
                                            <p class="mt-1 text-sm font-bold text-ocean">
                                                {{ $messagePopup['count'] }} {{ $messagePopup['title'] }}
                                            </p>
                                        </div>
                                        <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-extrabold text-slate-600">
                                            {{ optional($messagePopup['latest']->sender)->name ?? 'Pengirim' }}
                                        </span>
                                    </div>

                                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-600" style="text-align: justify;">
                                        {{ $messagePopup['latest']->body }}
                                    </p>
                                    <p class="mt-2 text-xs font-semibold text-slate-500">
                                        {{ $messagePopup['latest']->created_at->diffForHumans() }}
                                    </p>

                                    @if($messagePopup['count'] > 1)
                                        <div class="mt-3 rounded-lg border border-white/70 bg-white/60 px-3 py-2 text-sm font-semibold text-slate-700">
                                            Ada {{ $messagePopup['count'] - 1 }} {{ $messagePopup['note'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('communication') }}" class="btn-primary px-4 py-3 text-sm">Buka Pesan</a>
                                <button type="button" data-close-message-popup class="btn-soft px-4 py-3 text-sm">Nanti</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.querySelectorAll('[data-close-message-popup]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            var popup = document.getElementById('incoming-message-popup');
                            if (popup) {
                                popup.remove();
                            }
                        });
                    });
                </script>
            @endif
        </div>
    @else
        @yield('content')
    @endauth
</body>
</html>

