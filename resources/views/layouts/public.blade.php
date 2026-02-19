@php($appName = 'Kunjachan Missionary Bhavan')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName) | FOLLOW JESUS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/public.css', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
</head>

<body>
    <div id="page-loader" class="page-loader" aria-hidden="true">
        <div class="loader-inner">
            <div class="loader"></div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}" aria-label="Home">
                <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo" class="logo-img">
                <span class="brand-title">Kunjachan Missionary<br>Bhavan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                    <li class="nav-item"><a href="{{ route('home') }}"
                            class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}"
                            class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                    <li class="nav-item"><a href="{{ route('institutions.index') }}"
                            class="nav-link {{ request()->routeIs('institutions.*') ? 'active' : '' }}">Institutions</a>
                    </li>
                    <li class="nav-item"><a href="{{ route('gallery') }}"
                            class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}">Gallery</a></li>
                    <li class="nav-item"><a href="{{ route('blog.index') }}"
                            class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a></li>
                    <li class="nav-item"><a href="{{ route('contact') }}"
                            class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
                    <li class="nav-item ms-lg-2"><a href="{{ route('donate') }}"
                            class="btn btn-kb rounded-pill px-3">Donate</a></li>
                    @auth
                        <li class="nav-item ms-lg-2"><a href="{{ route('dashboard') }}"
                                class="btn btn-outline-secondary rounded-pill px-3">Dashboard</a></li>
                    @else
                        @if (!request()->routeIs('login'))
                            <li class="nav-item ms-lg-2"><a href="{{ route('login') }}"
                                    class="btn btn-outline-secondary rounded-pill px-3">Login</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if (request()->routeIs('home'))
        <div class="header-accent" aria-hidden="true"></div>
    @endif

    <main class="main">
        @yield('content')
    </main>

    <footer class="site-footer py-4">
        <div class="container footer-grid mb-2">
            <div>
                <a class="d-inline-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
                    <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo"
                        class="logo-img" style="height:40px">
                    <span class="brand-title" style="text-transform:none">{{ $appName }}</span>
                </a>
                <div class="small muted mt-2">Compassion • Dignity • Community</div>
            </div>
            <div>
                <h6 class="text-uppercase small text-muted mb-2">Quick Links</h6>
                <ul class="list-plain small">
                    <li class="mb-1"><a href="{{ route('about') }}">About Us</a></li>
                    <li class="mb-1"><a href="{{ route('institutions.index') }}">Institutions</a></li>
                    <li class="mb-1"><a href="{{ route('gallery') }}">Gallery</a></li>
                    <li class="mb-1"><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="mb-1"><a href="{{ route('timeline') }}">Timeline</a></li>
                    <li class="mb-1"><a href="{{ route('contact') }}">Contact</a></li>
                    <li class="mb-1"><a href="{{ route('donate') }}">Donate</a></li>
                </ul>
            </div>
            <div>
                <h6 class="text-uppercase small text-muted mb-2">Contact</h6>
                <ul class="list-plain small muted">
                    <li class="mb-1">Kunjachan Missionary Bhavan<br>Idiyanal P.O, Ramapuram<br>Kottayam, Kerala</li>
                    <li class="mb-1"><a href="mailto:kunjachanmissionary@gmail.com">kunjachanmissionary@gmail.com</a>
                    </li>
                    <li class="mb-1"><a href="tel:+918281960435">+91 82819 60435</a></li>
                    <li><a target="_blank" rel="noopener" href="https://maps.app.goo.gl/NP69bHdpmoBK2MY37">Google
                            Maps</a></li>
                </ul>
            </div>
            <div>
                <h6 class="text-uppercase small text-muted mb-2">Follow</h6>
                <div class="social">
                    <a aria-label="Facebook" href="#" class="text-muted">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a aria-label="Instagram" href="#" class="text-muted">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a aria-label="Twitter" href="#" class="text-muted">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="container footer-bottom small">
            <div class="small-note">&copy; {{ date('Y') }} Kunjachan Missionary Bhavan</div>
            <div class="text-muted">All rights reserved.</div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>