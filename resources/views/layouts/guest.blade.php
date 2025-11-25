<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            // Force brand name to Aathmiya across guest pages
            $appName = 'Aathmiya';
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
            :root{ --aw-primary:#0f4f4b; --aw-accent:#0EA5A1; }
            html, body { height: 100%; }
            body { font-family: 'Figtree', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji'; }
            .aw-hero {
                background: radial-gradient(1200px 600px at 80% -10%, rgba(14,165,161,.20), transparent),
                            radial-gradient(800px 500px at 0% 110%, rgba(15,79,75,.20), transparent),
                            linear-gradient(160deg, #ffffff 0%, #f6fffd 100%);
                min-height: 100vh;
            }
            .aw-nav-brand img { height: 28px; width:auto; }
            .aw-card { backdrop-filter: blur(10px); background: rgba(255,255,255,.85); border: 1px solid rgba(15,79,75,.08); border-radius: 1rem; box-shadow: 0 12px 30px rgba(0,0,0,.08); }
            .aw-btn-primary { background: var(--aw-primary); border-color: var(--aw-primary); }
            .aw-btn-primary:hover { background: #0c4340; border-color: #0c4340; }
            .form-control { border-radius: .75rem; padding: .75rem .9rem; }
            .input-group-text { border-radius: .75rem; }
            .aw-link { color: var(--aw-primary); text-decoration: none; }
            .aw-link:hover { color: #0c4340; text-decoration: underline; }
        </style>
        @stack('styles')
    </head>
    <body class="aw-hero d-flex flex-column">
        <nav class="navbar navbar-expand-md bg-transparent py-3">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center aw-nav-brand">
                    <img src="{{ asset('assets/aathmiya.png') }}" alt="Aathmiya">
                    
                </a>
                <div class="ms-auto d-none d-md-flex align-items-center gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill px-3">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn aw-btn-primary text-white rounded-pill px-3">Create account</a>
                        @endif
                    @else
                        <a href="{{ route('dashboard') }}" class="btn aw-btn-primary text-white rounded-pill px-3">Go to Dashboard</a>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="flex-grow-1 d-flex align-items-center">
            @yield('content')
        </main>

        <footer class="py-4 text-center text-muted small">
            @yield('footer')
            @hasSection('footer')
            @else
                <div>&copy; {{ date('Y') }} AJCE24BCA</div>
            @endif
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
 </html>
