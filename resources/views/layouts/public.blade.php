<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Super App Sekolah' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                    colors: { ink: '#17202a', brand: '#0f766e', ocean: '#0f3d5e', gold: '#10b981' },
                    boxShadow: {
                        soft: '0 18px 45px rgba(15, 61, 94, .10)',
                        glow: '0 16px 36px rgba(15, 118, 110, .28)'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background:
                linear-gradient(135deg, rgba(236, 253, 245, .94), rgba(240, 253, 244, .92) 42%, rgba(204, 251, 241, .68)),
                #f8fafc;
        }
        .surface {
            background: linear-gradient(135deg, rgba(236,253,245,.97), rgba(220,252,231,.94) 52%, rgba(204,251,241,.92)) !important;
            border: 1px solid rgba(20,184,166,.34);
            box-shadow: 0 18px 45px rgba(15,61,94,.09);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        [class*="bg-white/70"],
        [class*="bg-white/75"],
        [class*="bg-white/80"] {
            background: linear-gradient(135deg, rgba(240,253,244,.95), rgba(204,251,241,.9)) !important;
            border-color: rgba(20,184,166,.28) !important;
        }
        .surface:hover {
            transform: translateY(-2px);
            border-color: rgba(20,184,166,.38);
            box-shadow: 0 22px 55px rgba(15,61,94,.13);
        }
        .btn-primary, .btn-dark, .btn-soft, .btn-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            overflow: hidden;
            border-radius: .7rem;
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #047857, #0f766e 58%, #10b981);
            box-shadow: 0 14px 28px rgba(15,118,110,.26);
        }
        .btn-dark {
            color: #fff;
            background: linear-gradient(135deg, #0f172a, #0f3d5e);
            box-shadow: 0 14px 28px rgba(15,23,42,.22);
        }
        .btn-soft {
            color: #0f3d5e;
            background: linear-gradient(135deg, #ffffff, #ecfeff);
            border: 1px solid rgba(14,116,144,.18);
            box-shadow: 0 10px 24px rgba(15,61,94,.08);
        }
        .btn-link { color: #0f766e; }
        .btn-primary::after, .btn-dark::after, .btn-soft::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-115%) skewX(-20deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.28), transparent);
            transition: transform .45s ease;
        }
        .btn-primary:hover, .btn-dark:hover, .btn-soft:hover, .btn-link:hover {
            transform: translateY(-2px);
            filter: saturate(1.08);
        }
        .btn-primary:hover::after, .btn-dark:hover::after, .btn-soft:hover::after {
            transform: translateX(115%) skewX(-20deg);
        }
        .btn-primary:active, .btn-dark:active, .btn-soft:active, .btn-link:active {
            transform: translateY(0) scale(.98);
        }
        .nav-effect {
            position: relative;
            display: inline-flex;
            align-items: center;
            border-radius: .65rem;
            border: 1px solid rgba(20, 184, 166, .28);
            background: #fff;
            padding: .55rem .8rem;
            color: #0f3d5e;
            box-shadow: 0 1px 2px rgba(15, 61, 94, .08);
            transition: transform .18s ease, color .18s ease, background-color .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .nav-effect::after {
            content: "";
            position: absolute;
            left: .8rem;
            right: .8rem;
            bottom: .35rem;
            height: 2px;
            transform: scaleX(0);
            transform-origin: left;
            border-radius: 999px;
            background: linear-gradient(90deg, #006B3F, #8DC63F);
            transition: transform .2s ease;
        }
        .nav-effect:hover {
            transform: translateY(-2px);
            border-color: rgba(20, 184, 166, .62);
            color: #006B3F;
            background-color: rgba(236, 253, 245, .92);
            box-shadow: 0 10px 24px rgba(15, 118, 110, .12);
        }
        .nav-effect:hover::after {
            transform: scaleX(1);
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
    @php
        $schoolQuery = $schoolQuery ?? [];
        $schoolName = $publicSchool->name ?? 'Super App Sekolah';
        $schoolLoginRoute = isset($publicSchool) ? route('school.login', ['schoolSlug' => $publicSchool->public_slug]) : route('login');
    @endphp
    <header class="sticky top-0 z-30 border-b border-teal-100 bg-white/88 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:flex-nowrap lg:px-8">
            <a href="{{ route('public.school.home', $schoolQuery) }}" class="text-lg font-extrabold tracking-tight text-ocean sm:text-xl">{{ $schoolName }}</a>
            <nav class="order-3 -mx-4 flex w-[calc(100%+2rem)] items-center gap-3 overflow-x-auto px-4 pb-1 text-sm font-bold text-slate-600 sm:-mx-6 sm:w-[calc(100%+3rem)] sm:px-6 lg:order-none lg:mx-0 lg:w-auto lg:flex-none lg:overflow-visible lg:px-0 lg:pb-0">
                @foreach([
                    ['Layanan', 'layanan'],
                    ['Absensi', 'absensi'],
                    ['LMS', 'lms'],
                    ['Komunikasi', 'komunikasi'],
                ] as [$service, $key])
                    <a href="{{ route('public.school.service', array_merge($schoolQuery, ['service' => $key])) }}" class="shrink-0 rounded-lg border border-teal-100 bg-white px-3 py-2 text-ocean shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:bg-cyan-50 hover:text-brand focus:outline-none focus:ring-4 focus:ring-teal-100">{{ $service }}</a>
                @endforeach
                <a class="nav-effect shrink-0" href="{{ route('public.school.news', $schoolQuery) }}">Berita</a>
                <a class="nav-effect shrink-0" href="{{ route('public.school.home', $schoolQuery) }}#kontak">Kontak</a>
            </nav>
            <a href="{{ auth()->check() ? route('dashboard') : $schoolLoginRoute }}" class="btn-primary shrink-0 px-4 py-2 text-sm hover:scale-[1.03]">Portal Sekolah</a>
        </div>
    </header>

    @yield('content')

    <footer id="kontak" class="mt-12 border-t border-teal-100 bg-white/70">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-8 text-sm text-slate-600 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div>
                <p class="font-extrabold text-ocean">{{ $schoolName }}</p>
                <p class="mt-2">Website sekolah publik dan portal digital untuk siswa, guru, admin, serta orang tua.</p>
            </div>
            <div>
                <p class="font-bold text-slate-900">Layanan</p>
                <p class="mt-2">Berita, Absensi, LMS, dan Komunikasi.</p>
            </div>
            <div>
                <p class="font-bold text-slate-900">Akses Internal</p>
                <a href="{{ $schoolLoginRoute }}" class="btn-link mt-2">Login portal sekolah</a>
            </div>
        </div>
    </footer>
</body>
</html>
