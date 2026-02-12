<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            // Brand name for guest pages
            $appName = 'Kunjachan Missionary Bhavan';
            $pageTitleSection = trim($__env->yieldContent('title'));
            $finalTitle = $pageTitleSection ? ($pageTitleSection.' | '.$appName) : $appName;
        @endphp
        <title>{{ $finalTitle }}</title>

        <!-- Favicons from public/assets -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon-32x36.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            :root{ 
                --kb-bg: #fafaf9;
                --kb-primary: #5a382f;
                --kb-accent: #a0522d;
                --kb-accent-soft: #c77952;
                --kb-text: #292524;
                --kb-text-dim: #78716c;
                --kb-border: rgba(41, 37, 36, 0.10);
                --kb-surface: #ffffff;
                --kb-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                --kb-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
                --kb-shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            }
            html, body { height: 100%; }
            body { 
                font-family: 'Figtree', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji';
                background: var(--kb-bg);
                color: var(--kb-text);
                -webkit-font-smoothing: antialiased;
                line-height: 1.7;
            }
            .kb-hero {
                background: linear-gradient(to bottom, rgba(250, 250, 249, 0.5), var(--kb-bg));
                min-height: 100vh;
                position: relative;
            }
            .kb-hero::before {
                content: '';
                position: absolute;
                inset: 0;
                background: 
                    radial-gradient(circle at 85% 20%, rgba(160, 82, 45, .06), transparent 50%),
                    radial-gradient(circle at 15% 80%, rgba(130, 96, 85, .08), transparent 55%);
                pointer-events: none;
            }
            .kb-nav-brand img { height: 40px; width:auto; border-radius:50%; box-shadow:var(--kb-shadow); object-fit:cover; }
            .kb-card { 
                backdrop-filter: blur(10px); 
                background: rgba(255,255,255,.95); 
                border: 1px solid var(--kb-border); 
                border-radius: 16px; 
                box-shadow: var(--kb-shadow-md);
            }
            .kb-btn-primary { 
                background: var(--kb-accent); 
                border-color: var(--kb-accent);
                font-weight: 600;
                letter-spacing: .3px;
                transition: all 0.2s ease;
                box-shadow: var(--kb-shadow-sm);
            }
            .kb-btn-primary:hover { 
                background: var(--kb-accent-soft); 
                border-color: var(--kb-accent-soft);
                transform: translateY(-1px);
                box-shadow: var(--kb-shadow);
            }
            .form-control { border-radius: 12px; padding: .75rem .9rem; border-color: var(--kb-border); }
            .form-control:focus { border-color: var(--kb-accent); box-shadow: 0 0 0 3px rgba(160, 82, 45, .2); }
            .input-group-text { border-radius: 12px; background: var(--kb-surface); border-color: var(--kb-border); color: var(--kb-text-dim); }
            .kb-link { color: var(--kb-accent); text-decoration: none; font-weight: 500; }
            .kb-link:hover { color: var(--kb-accent-soft); text-decoration: underline; }
            .navbar { background: rgba(255, 255, 255, .98) !important; backdrop-filter: blur(12px); border-bottom: 1px solid var(--kb-border); box-shadow: var(--kb-shadow-sm); }
            .brand-title { font-weight: 600; letter-spacing: .5px; font-size: 0.95rem; line-height: 1.1; color: var(--kb-primary); text-transform: uppercase; }
            .small-note { font-size: .7rem; letter-spacing: .1em; text-transform: uppercase; color: var(--kb-text-dim); font-weight: 500; }
        </style>
        @stack('styles')
    </head>
    <body class="kb-hero d-flex flex-column">
        <nav class="navbar navbar-expand-md py-3">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 kb-nav-brand">
                    <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo">
                    <span class="brand-title">KUNJACHAN MISSIONARY<br>BHAVAN</span>
                </a>
                <div class="ms-auto d-none d-md-flex align-items-center gap-2">
                    @guest
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Back to Website
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn kb-btn-primary text-white rounded-pill px-3">Go to Dashboard</a>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="flex-grow-1 d-flex align-items-center" style="position: relative; z-index: 1;">
            @yield('content')
        </main>

        <footer class="py-4 text-center text-muted small" style="position: relative; z-index: 1;">
            @yield('footer')
            @hasSection('footer')
            @else
                <div class="small-note">&copy; {{ date('Y') }} AJCE24BCA</div>
            @endif
        </footer>

        @stack('modals')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
 </html>
