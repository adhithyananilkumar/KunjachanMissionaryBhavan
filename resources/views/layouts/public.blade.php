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
    <!-- <link rel="stylesheet" href="{{ asset('css/public.css') }}"> -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
</head>

<body>
    <div id="page-loader" class="page-loader" aria-hidden="true">
        <div class="loader-inner">
            <div class="loader"></div>
            <div class="loader-brand d-flex align-items-center gap-2 mt-2">
                <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo" height="28">
                <span>{{ $appName }}</span>
            </div>
        </div>
    </div>
    <nav class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur-lg border-b border-kb-border shadow-sm transition-all duration-300">
    <div class="container py-4">
        <div class="navbar navbar-expand-lg p-0">
            <a class="navbar-brand d-flex align-items-center gap-3 group" href="{{ route('home') }}" aria-label="Home">
                <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo" class="h-12 w-auto transition-transform duration-300 group-hover:scale-105">
                <span class="font-display font-bold text-lg leading-tight text-kb-primary tracking-wide uppercase">{{ $appName }}</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none focus:ring-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list text-2xl text-kb-primary"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 gap-2 mt-3 mt-lg-0">
                    <li class="nav-item"><a href="{{ route('home') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('home') ? 'font-semibold text-kb-accent' : '' }}">Home</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('about') ? 'font-semibold text-kb-accent' : '' }}">About</a></li>
                    <li class="nav-item"><a href="{{ route('institutions.index') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('institutions.*') ? 'font-semibold text-kb-accent' : '' }}">Institutions</a></li>
                    <li class="nav-item"><a href="{{ route('gallery') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('gallery') ? 'font-semibold text-kb-accent' : '' }}">Gallery</a></li>
                    <li class="nav-item"><a href="{{ route('blog.index') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('blog.*') ? 'font-semibold text-kb-accent' : '' }}">Blog</a></li>
                    <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link px-3 py-2 rounded-full text-kb-text hover:text-kb-accent hover:bg-kb-bg-alt transition-colors {{ request()->routeIs('contact') ? 'font-semibold text-kb-accent' : '' }}">Contact</a></li>
                    <li class="nav-item ms-lg-2"><a href="{{ route('donate') }}" class="btn-kb shadow-lg shadow-kb-accent/20">Donate</a></li>
                    @auth
                        <li class="nav-item ms-lg-2"><a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full border border-kb-border text-kb-text hover:bg-kb-bg-alt transition-all">Dashboard</a></li>
                    @else
                        <li class="nav-item ms-lg-2"><a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full border border-kb-border text-kb-text hover:bg-kb-bg-alt transition-all">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="main min-h-screen">
    @yield('content')
</main>

<footer class="bg-white border-t border-kb-border pt-16 pb-8 mt-auto">
    <div class="container">
        <div class="row g-5 mb-12">
            <div class="col-lg-4 col-md-6">
                <a class="flex items-center gap-3 text-decoration-none mb-4" href="{{ route('home') }}">
                    <img src="{{ asset('assets/kunjachanMissionaryLogo.png') }}" alt="{{ $appName }} Logo" class="h-10 w-auto grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition-all">
                    <span class="font-display font-bold text-lg text-kb-primary">{{ $appName }}</span>
                </a>
                <p class="text-kb-text-dim leading-relaxed mb-6">
                    A sanctuary of compassion and care, dedicated to serving the community with dignity, faith, and unwavering support.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-kb-bg flex items-center justify-center text-kb-primary hover:bg-kb-accent hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-kb-bg flex items-center justify-center text-kb-primary hover:bg-kb-accent hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-kb-bg flex items-center justify-center text-kb-primary hover:bg-kb-accent hover:text-white transition-all transform hover:-translate-y-1">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="font-display font-bold text-kb-primary mb-6">Quick Links</h6>
                <ul class="space-y-3">
                    <li><a href="{{ route('about') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">About Us</a></li>
                    <li><a href="{{ route('institutions.index') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Institutions</a></li>
                    <li><a href="{{ route('gallery') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Gallery</a></li>
                    <li><a href="{{ route('donate') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Donate</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="font-display font-bold text-kb-primary mb-6">Resources</h6>
                <ul class="space-y-3">
                    <li><a href="{{ route('blog.index') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Blog</a></li>
                    <li><a href="{{ route('timeline') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Timeline</a></li>
                    <li><a href="{{ route('contact') }}" class="text-kb-text-dim hover:text-kb-accent transition-colors">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-12">
                <h6 class="font-display font-bold text-kb-primary mb-6">Contact Us</h6>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-kb-text-dim">
                        <i class="bi bi-geo-alt mt-1 text-kb-accent"></i>
                        <span>Kunjachan Missionary Bhavan<br>Idiyanal P.O, Ramapuram<br>Kottayam, Kerala</span>
                    </li>
                    <li class="flex items-center gap-3 text-kb-text-dim">
                        <i class="bi bi-envelope text-kb-accent"></i>
                        <a href="mailto:kunjachanmissionary@gmail.com" class="hover:text-kb-accent transition-colors">kunjachanmissionary@gmail.com</a>
                    </li>
                    <li class="flex items-center gap-3 text-kb-text-dim">
                        <i class="bi bi-telephone text-kb-accent"></i>
                        <a href="tel:+918281960435" class="hover:text-kb-accent transition-colors">+91 82819 60435</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-kb-border pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-kb-text-dim">
            <div>&copy; {{ date('Y') }} Kunjachan Missionary Bhavan. All rights reserved.</div>
            <div class="flex gap-6">
                <a href="#" class="hover:text-kb-accent transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-kb-accent transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/public.js') }}" defer></script>
    @stack('scripts')
</body>

</html>