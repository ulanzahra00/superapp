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
    </style>
</head>
<body class="text-ink antialiased">
    @auth
        <div class="min-h-screen lg:flex">
            <aside class="app-sidebar hidden lg:flex lg:w-72 lg:flex-col border-r border-teal-100/80">
                <div class="px-6 py-6">
                    <div class="text-xl font-extrabold tracking-tight text-ocean">Super App Sekolah</div>
                    <div class="mt-2 inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-800">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
                </div>
                <nav class="flex-1 px-4 space-y-1">
                    @include('partials.nav-links')
                </nav>
            </aside>

            <main class="min-w-0 flex-1 pb-24 lg:pb-0">
                <header class="sticky top-0 z-20 border-b border-teal-100 bg-white/85 backdrop-blur-xl">
                    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                        <div>
                            <p class="text-sm font-bold text-brand">{{ $eyebrow ?? 'Monitoring Sekolah' }}</p>
                            <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl">{{ $title ?? 'Dashboard' }}</h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('notifications') }}" class="btn-soft rounded-full px-3 py-2 text-sm">Notifikasi</a>
                            <a href="{{ route('profile') }}" class="hidden rounded-full bg-gradient-to-r from-emerald-100 to-teal-50 px-3 py-2 text-sm font-bold text-ocean ring-1 ring-emerald-200/70 sm:block">{{ auth()->user()->name }}</a>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn-dark rounded-full px-4 py-2 text-sm">Keluar</button>
                            </form>
                        </div>
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
                <div class="grid grid-cols-4 text-center text-xs font-semibold">
                    <a class="mobile-tab py-3 {{ request()->routeIs('dashboard') ? 'text-brand' : 'text-slate-500' }}" href="{{ route('dashboard') }}">Home</a>
                    <a class="mobile-tab py-3 {{ request()->routeIs('news.*') ? 'text-brand' : 'text-slate-500' }}" href="{{ route('news.index') }}">Berita</a>
                    <a class="mobile-tab py-3 {{ request()->routeIs('notifications') ? 'text-brand' : 'text-slate-500' }}" href="{{ route('notifications') }}">Notifikasi</a>
                    <a class="mobile-tab py-3 {{ request()->routeIs('profile') ? 'text-brand' : 'text-slate-500' }}" href="{{ route('profile') }}">Profil</a>
                </div>
            </nav>
        </div>
    @else
        @yield('content')
    @endauth
</body>
</html>

