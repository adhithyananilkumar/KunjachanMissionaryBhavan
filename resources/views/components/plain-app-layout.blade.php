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
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#0f4f4b">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
      html { font-size: 17px; }
      body { line-height: 1.6; }
      .btn, .form-control, .card, .dropdown-menu, .modal-content { border-radius: .7rem; }
      .list-group-item-action:hover { background: rgba(13,110,253,.06); }
      .card { box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    </style>
    @stack('styles')
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.dropdown-menu [data-mark-read]').forEach(el=>{
          el.addEventListener('click', function(ev){
            const id = this.getAttribute('data-id');
            const href = this.getAttribute('href');
            if(href){ ev.preventDefault(); }
            fetch(`{{ url('/notifications') }}/${id}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}})
              .catch(()=>{})
              .finally(()=>{
                const c1 = document.getElementById('notifCount');
                const c2 = document.getElementById('notifCountSidebar');
                [c1,c2].forEach(c=>{ if(c){ let n = parseInt(c.textContent||'0',10); if(n>0){ n--; c.textContent=String(n); if(n===0){ c.classList.add('d-none'); } } } });
                if(href){ window.location.assign(href); }
              });
          });
        });
      });
    </script>
  </head>
  <body class="bg-light">
  <nav class="navbar navbar-light bg-white border-bottom sticky-top">
      <div class="container-fluid">
    <span class="navbar-brand fw-semibold d-flex align-items-center me-auto">
          <img src="{{ asset('assets/aathmiya.png') }}" alt="aathmiya" style="height:26px;width:auto">
        </span>
        <div class="d-flex align-items-center gap-2">
          @auth
            @php
              $unreadNotifications = auth()->user()?->unreadNotifications ?? collect();
              $unreadNotificationsCount = $unreadNotifications->count();
            @endphp
            <!-- Notifications dropdown (only on dashboard routes) -->
            @if(request()->routeIs('dashboard') || request()->routeIs('*.dashboard'))
            <div class="dropdown me-1">
              <a class="btn btn-outline-secondary position-relative" href="#" id="notifDropdownPlain" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <span class="bi bi-bell"></span>
                <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($unreadNotificationsCount ?? 0)>0 ? '' : 'd-none' }}">{{ $unreadNotificationsCount ?? 0 }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifDropdownPlain" style="min-width:320px; max-height:360px; overflow:auto">
                <div id="notifList" class="list-group list-group-flush small">
                  @forelse(($unreadNotifications ?? collect()) as $n)
                    @php $data = $n->data; $type = $data['type'] ?? 'notification'; @endphp
                    @if($type === 'lab_test_ordered')
                      <a href="{{ $data['link'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
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
                    @elseif($type === 'emergency_appointment')
                      <a href="{{ $data['link'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                        <div class="me-2">
                          <div class="fw-semibold text-danger">Emergency Appointment</div>
                          <div>{{ Str::limit($data['inmate_name'] ?? 'Patient',40) }} scheduled on {{ $data['scheduled_for'] ?? '' }}</div>
                        </div>
                        <span class="badge rounded-pill bg-danger">New</span>
                      </a>
                    @elseif($type === 'inmate_birthday')
                      <a href="{{ $data['link'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                        <div class="me-2">
                          <div class="fw-semibold">Inmate Birthday</div>
                          <div>{{ Str::limit($data['message'] ?? ($data['inmate_name'] ?? 'Inmate'), 80) }}</div>
                        </div>
                        <span class="badge rounded-pill bg-info text-dark">New</span>
                      </a>
                    @else
                      <div class="list-group-item d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                        <div class="me-2">
                          <div class="fw-semibold">Notification</div>
                          <div class="text-muted">{{ Str::limit(($data['message'] ?? '') ?: ($data['title'] ?? ''), 80) }}</div>
                        </div>
                        <span class="badge rounded-pill bg-primary">New</span>
                      </div>
                    @endif
                  @empty
                    <div class="p-3 text-center text-muted">No new notifications</div>
                  @endforelse
                </div>
              </div>
            </div>
            @endif

            @php
              $name = trim(auth()->user()->name ?? '');
              $parts = preg_split('/\s+/', $name);
              $first = $parts[0] ?? '';
              $last = end($parts) ?: '';
              $initials = strtoupper(substr($first,0,1) . substr($last,0,1));
              $avatar = auth()->user()->avatar_url ?? '';
            @endphp

            <a href="{{ route('profile.edit') }}" class="text-decoration-none d-none d-lg-inline-flex">
              @if($avatar)
                <img src="{{ $avatar }}" class="rounded-circle" style="width:34px;height:34px;object-fit:cover" alt="avatar">
              @else
                <span class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px;">{{ $initials }}</span>
              @endif
            </a>

            <form method="POST" action="{{ route('logout') }}" class="ms-1 d-none d-lg-inline">@csrf
              <button class="btn btn-sm btn-outline-danger"><span class="bi bi-box-arrow-right me-1"></span>logout</button>
            </form>

            <!-- Mobile: 3-dot dropdown (Profile, Logout) -->
            <div class="dropdown d-inline-flex d-lg-none">
              <button class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open menu"><span class="bi bi-three-dots-vertical"></span></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                    <span class="bi bi-person-circle"></span> Profile
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">@csrf
                    <button class="btn btn-link p-0 text-danger d-flex align-items-center gap-2"><span class="bi bi-box-arrow-right"></span> Logout</button>
                  </form>
                </li>
              </ul>
            </div>
          @endauth
        </div>
      </div>
    </nav>

    <main class="py-4 px-3">
      {{ $slot }}
      @include('layouts.partials.flash-messages')
    </main>

    <!-- Desktop Offcanvas Sidebar (hidden by default; opened via menu button) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="globalSidebar">
      <div class="offcanvas-body p-0 bg-dark">
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
        // Mark notifications as read on click
        document.querySelectorAll('.notification-item').forEach(function(el){
          el.addEventListener('click', function(){
            const id = this.getAttribute('data-id');
            if(!id) return;
            fetch(`{{ url('/notifications') }}/${id}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
          });
        });
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

  @if(auth()->check() && auth()->user()->role === 'developer' && auth()->user()->can_report_bugs)
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
              <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="globalBugSubmitBtn">Submit</button></div>
            </form>
          </div>
        </div>
      </div>
    @endif

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    // Prevent accidental bug modal submissions via Enter; require explicit click
    (function(){
      const modalEl = document.getElementById('globalBugModal');
      const submitBtn = document.getElementById('globalBugSubmitBtn');
      if(modalEl){
        modalEl.addEventListener('keydown', function(ev){ if(ev.key==='Enter'){ ev.preventDefault(); }});
      }
      submitBtn?.addEventListener('click', function(){
        const form = modalEl?.querySelector('form');
        if(form){ form.submit(); }
      });
    })();
    </script>
  @vite(['resources/js/app.js'])
    @stack('scripts')
  </body>
</html>
