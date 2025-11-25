<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appName = config('app.name', 'Aathmiya');
            $user = auth()->user();
            $roleLabel = $user?->role ? ucfirst(str_replace('_',' ', $user->role)) : null;
            $pageTitleSection = trim($__env->yieldContent('title'));
            $finalTitle = $appName;
            if ($roleLabel) { $finalTitle = $roleLabel.' | '.$finalTitle; }
            if ($pageTitleSection) { $finalTitle = $pageTitleSection.' | '.$finalTitle; }
        @endphp
        <title>{{ $finalTitle }}</title>

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/site.webmanifest') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <meta name="theme-color" content="#0f4f4b">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <style>
            /* Global readability: slightly larger base font for middle-aged users */
            html { font-size: 17px; }
            body { line-height: 1.6; }

            /* Make mobile offcanvas the same width as the sidebar and remove extra white band */
            #mobileSidebar{ --bs-offcanvas-width: 260px; background: transparent; }

            /* Global mobile hardening to stop sideways dragging and overflow */
            html, body { max-width: 100%; overflow-x: hidden; }
            main, .container-fluid { max-width: 100vw; }
            img, svg, video, canvas { max-width: 100%; height: auto; }
            /* Avoid aggressive wrapping that broke words inside tables/headers on mobile */
            .alert, .modal-body, .offcanvas-body { word-wrap: break-word; overflow-wrap: anywhere; }
            h1, h2, h3, h4, h5, h6 { word-break: keep-all; overflow-wrap: normal; }
            .table th, .table td { white-space: nowrap; }
            .table-responsive { -webkit-overflow-scrolling: touch; }
            @media (max-width: 576px){
                .card-header h5, .card-header .fw-semibold, .card-header .card-title { white-space: nowrap; }
            }
            .table-responsive { -webkit-overflow-scrolling: touch; }
            .nav.nav-pills, .nav.nav-tabs { flex-wrap: wrap; gap: .25rem; }
            .nav.nav-pills .nav-link, .nav.nav-tabs .nav-link { padding: .375rem .5rem; }
            .btn-group { flex-wrap: wrap; }

            /* Modern subtle rounding and hover effects */
            .btn, .form-control, .card, .dropdown-menu, .modal-content { border-radius: .7rem; }
            .list-group-item-action:hover { background: rgba(13,110,253,.06); }
            .card { box-shadow: 0 2px 8px rgba(0,0,0,.05); }
            .aw-tile { transition: transform .12s ease, box-shadow .12s ease; }
            .aw-tile:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.08); }
            /* Pagination hygiene */
            .pagination{ gap:.25rem; }
            .pagination .page-link{ display:inline-flex; align-items:center; justify-content:center; min-width:2.25rem; height:2.25rem; padding:.375rem .5rem; }
            .pagination .bi{ font-size:1rem; line-height:1; }
            @media (max-width: 576px){ .pagination .page-link{ min-width:2rem; height:2rem; padding:.25rem .4rem; font-size:.9rem; } }
        </style>
    @stack('styles')

    </head>
    <body class="font-sans antialiased bg-light" @auth data-auth="{{ auth()->id() }}" @endauth>
    <div class="d-flex min-vh-100">
        <!-- Desktop Sidebar (locked/fixed) -->
        <div class="d-none d-lg-block position-fixed h-100" style="width:260px; left:0; top:0; z-index:1030;">
            @include('layouts.partials.sidebar')
        </div>
        <!-- Content wrapper adds left margin to avoid overlap -->
        <div class="flex-grow-1 d-flex flex-column" style="margin-left:0; margin-left:260px;">
            <nav class="navbar navbar-light bg-white border-bottom sticky-top d-lg-none">
                <div class="container-fluid">
                    <button class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"><span class="bi bi-list"></span></button>
                    <span class="navbar-brand ms-2 fw-semibold d-flex align-items-center">
                        <img src="{{ asset('assets/aathmiya.png') }}" alt="aathmiya" style="height:22px;width:auto">
                    </span>
                    @auth
                    @php
                        $unreadNotifications = auth()->user()?->unreadNotifications ?? collect();
                        $unreadNotificationsCount = $unreadNotifications->count();
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <!-- Notifications bell (desktop) -->
                        @if(request()->routeIs('dashboard') || request()->routeIs('*.dashboard'))
                        <div class="d-none d-lg-block dropdown">
                            <a class="btn btn-outline-secondary position-relative" href="#" id="notifDropdownDesktop" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                                <span class="bi bi-bell"></span>
                                <span id="notifCountDesktop" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($unreadNotificationsCount ?? 0)>0 ? '' : 'd-none' }}">{{ $unreadNotificationsCount ?? 0 }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifDropdownDesktop" style="min-width:320px; max-height:360px; overflow:auto">
                                <div id="notifListDesktop" class="list-group list-group-flush small">
                                    @forelse(($unreadNotifications ?? collect()) as $n)
                                        @php $data = $n->data; $type = $data['type'] ?? 'ticket_reply'; @endphp
                                        @if($type === 'lab_test_ordered')
                                            <a href="{{ ($data['link'] ?? '#') }}@if(isset($n) && isset($data['link']) && Str::contains($data['link'],'?'))&nid={{ $n->id }}@elseif(isset($n) && isset($data['link']))?nid={{ $n->id }}@endif" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Lab Test Ordered</div>
                                                    <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                                    <div class="text-muted small">by {{ $data['ordered_by'] ?? 'Doctor' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-warning text-dark">New</span>
                                            </a>
                                        @elseif($type === 'lab_result_uploaded')
                                            <a href="{{ ($data['link'] ?? '#') }}@if(isset($n) && isset($data['link']) && Str::contains($data['link'],'?'))&nid={{ $n->id }}@elseif(isset($n) && isset($data['link']))?nid={{ $n->id }}@endif" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Lab Result Ready</div>
                                                    <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                                    <div class="text-muted small">Updated by {{ $data['updated_by'] ?? 'Staff' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-success">New</span>
                                            </a>
                                        @else
                                            <a href="{{ route('tickets.show',$data['ticket_id'] ?? null) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Reply: {{ Str::limit($data['ticket_title'] ?? 'Ticket',40) }}</div>
                                                    <div class="text-muted">{{ Str::limit($data['reply_excerpt'] ?? '',60) }}</div>
                                                    <div class="text-muted fst-italic">by {{ $data['replied_by'] ?? 'User' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-primary">New</span>
                                            </a>
                                        @endif
                                    @empty
                                        <div class="p-3 text-center text-muted">No new notifications</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Notifications bell (mobile) -->
                        <div class="dropdown">
                            <a class="btn btn-outline-secondary position-relative" href="#" id="notifDropdownMobile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                                <span class="bi bi-bell"></span>
                                <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($unreadNotificationsCount ?? 0)>0 ? '' : 'd-none' }}">{{ $unreadNotificationsCount ?? 0 }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifDropdownMobile" style="min-width:320px; max-height:360px; overflow:auto">
                                <div id="notifList" class="list-group list-group-flush small">
                                    @forelse(($unreadNotifications ?? collect()) as $n)
                                        @php $data = $n->data; $type = $data['type'] ?? 'ticket_reply'; @endphp
                                        @if($type === 'lab_test_ordered')
                                            <a href="{{ ($data['link'] ?? '#') }}@if(isset($n) && isset($data['link']) && Str::contains($data['link'],'?'))&nid={{ $n->id }}@elseif(isset($n) && isset($data['link']))?nid={{ $n->id }}@endif" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Lab Test Ordered</div>
                                                    <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                                    <div class="text-muted small">by {{ $data['ordered_by'] ?? 'Doctor' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-warning text-dark">New</span>
                                            </a>
                                        @elseif($type === 'lab_result_uploaded')
                                            <a href="{{ ($data['link'] ?? '#') }}@if(isset($n) && isset($data['link']) && Str::contains($data['link'],'?'))&nid={{ $n->id }}@elseif(isset($n) && isset($data['link']))?nid={{ $n->id }}@endif" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Lab Result Ready</div>
                                                    <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                                    <div class="text-muted small">Updated by {{ $data['updated_by'] ?? 'Staff' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-success">New</span>
                                            </a>
                                        @else
                                            <a href="{{ route('tickets.show',$data['ticket_id'] ?? null) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                                <div class="me-2">
                                                    <div class="fw-semibold">Reply: {{ Str::limit($data['ticket_title'] ?? 'Ticket',40) }}</div>
                                                    <div class="text-muted">{{ Str::limit($data['reply_excerpt'] ?? '',60) }}</div>
                                                    <div class="text-muted fst-italic">by {{ $data['replied_by'] ?? 'User' }}</div>
                                                </div>
                                                <span class="badge rounded-pill bg-primary">New</span>
                                            </a>
                                        @endif
                                    @empty
                                        <div class="p-3 text-center text-muted">No new notifications</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" style="width:34px;height:34px;">
                            @php 
                                $nm = trim(auth()->user()->name ?? '');
                                $parts = preg_split('/\s+/', $nm);
                                $ini = strtoupper(substr($parts[0] ?? 'U',0,1) . substr(end($parts) ?: '',0,1));
                            @endphp
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover" alt="avatar">
                            @else
                                <span style="font-size:.8rem">{{ $ini }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><span class="bi bi-person me-2"></span>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
                                    <button class="dropdown-item text-danger"><span class="bi bi-box-arrow-right me-2"></span>Logout</button>
                                </form>
                            </li>
                        </ul>
                        </div>
                    </div>
                    @endauth
                </div>
            </nav>
            @auth
            <!-- Desktop notifications (fixed top-right) -->
            @if(request()->routeIs('dashboard') || request()->routeIs('*.dashboard'))
            <div class="d-none d-lg-block position-fixed" style="top:1rem; right:1rem; z-index:1060">
                @php
                    $unreadNotifications = auth()->user()?->unreadNotifications ?? collect();
                    $unreadNotificationsCount = $unreadNotifications->count();
                @endphp
                <div class="dropdown">
                    <a class="btn btn-outline-secondary position-relative" href="#" id="notifDropdownFixedDesktop" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                        <span class="bi bi-bell"></span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($unreadNotificationsCount ?? 0)>0 ? '' : 'd-none' }}">{{ $unreadNotificationsCount ?? 0 }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifDropdownFixedDesktop" style="min-width:320px; max-height:360px; overflow:auto">
                        <div class="list-group list-group-flush small">
                            @forelse(($unreadNotifications ?? collect()) as $n)
                                @php $data = $n->data; $type = $data['type'] ?? 'ticket_reply'; @endphp
                                @if($type === 'lab_test_ordered')
                                            <a href="{{ ($data['link'] ?? '#') }}@if(isset($n) && isset($data['link']) && Str::contains($data['link'],'?'))&nid={{ $n->id }}@elseif(isset($n) && isset($data['link']))?nid={{ $n->id }}@endif" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                        <div class="me-2">
                                            <div class="fw-semibold">Lab Test Ordered</div>
                                            <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                            <div class="text-muted small">by {{ $data['ordered_by'] ?? 'Doctor' }}</div>
                                        </div>
                                        <span class="badge rounded-pill bg-warning text-dark">New</span>
                                    </a>
                                @elseif($type === 'lab_result_uploaded')
                                    <a href="{{ $data['link'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                        <div class="me-2">
                                            <div class="fw-semibold">Lab Result Ready</div>
                                            <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                            <div class="text-muted small">Updated by {{ $data['updated_by'] ?? 'Staff' }}</div>
                                        </div>
                                        <span class="badge rounded-pill bg-success">New</span>
                                    </a>
                                @else
                                    <a href="{{ route('tickets.show',$data['ticket_id'] ?? null) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                        <div class="me-2">
                                            <div class="fw-semibold">Reply: {{ Str::limit($data['ticket_title'] ?? 'Ticket',40) }}</div>
                                            <div class="text-muted">{{ Str::limit($data['reply_excerpt'] ?? '',60) }}</div>
                                            <div class="text-muted fst-italic">by {{ $data['replied_by'] ?? 'User' }}</div>
                                        </div>
                                        <span class="badge rounded-pill bg-primary">New</span>
                                    </a>
                                @endif
                            @empty
                                <div class="p-3 text-center text-muted">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endauth
            @if (isset($header))
                <header class="bg-white shadow-sm d-print-none">
                    <div class="container-fluid py-3 px-3">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <main class="flex-grow-1 py-4 px-3">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
                @include('layouts.partials.flash-messages')
            </main>
        </div>
    </div>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-body p-0">
            @include('layouts.partials.sidebar')
        </div>
    </div>

                        @auth
                        <div class="position-fixed top-0 end-0 p-3" style="z-index:1080">
                            <div id="liveToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body" id="toastBody">New reply received.</div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function(){
                                 const notifLinks = document.querySelectorAll('.notification-item');
                                 notifLinks.forEach(el=>{
                                     el.addEventListener('click', function(){
                                         const id=this.getAttribute('data-id');
                                         fetch(`{{ url('/notifications') }}/${id}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
                                     });
                                 });
                                 // Auto show toast for most recent unread notification (if any)
                                 const first = document.querySelector('.notification-item');
                                 if(first){
                                     const toastEl = document.getElementById('liveToast');
                                     const body = document.getElementById('toastBody');
                                     var muted = first.querySelector('.text-muted');
                                     body.textContent = (muted ? muted.textContent : body.textContent);
                                     const t = new bootstrap.Toast(toastEl, {delay:6000}); t.show();
                                 }
                            });
                        </script>
                        @endauth

    @if(auth()->check() && auth()->user()->can_report_bugs)
            <button type="button" class="btn btn-danger rounded-circle position-fixed" style="bottom:2rem; right:2rem; width:3.5rem; height:3.5rem; z-index:1070" data-bs-toggle="modal" data-bs-target="#globalBugModal" title="Report a Bug">
                <span class="bi bi-bug-fill"></span>
            </button>
            <div class="modal fade" id="globalBugModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">@csrf
                            <div class="modal-header"><h5 class="modal-title">Report a Bug</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" required maxlength="255"></div>
                                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" rows="4" class="form-control" required></textarea></div>
                                <div class="mb-3"><label class="form-label">Screenshot (optional)</label><input type="file" name="screenshot" class="form-control" accept="image/*"></div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Submit</button></div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                <!-- jQuery required for toastr (v2.x) -->
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
                <script>
                    // Fallback if toastr failed to attach (e.g., race condition)
                    if(typeof toastr === 'undefined'){
                        window.toastr = {success:console.log, error:console.error, info:console.log, warning:console.warn};
                        console.warn('toastr library not loaded, using console fallback');
                    }
                </script>
                @vite(['resources/js/app.js'])
                @stack('scripts')
    </body>
</html>
