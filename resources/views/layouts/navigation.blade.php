<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm" role="navigation">
    <div class="container">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <x-application-logo class="me-2" />
            <span class="fw-semibold">{{ config('app.name', 'Aathmiya') }}</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Left Nav Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    @if(auth()->user()->role === 'developer')
                        <li class="nav-item"><a class="nav-link" href="{{ route('developer.requests.index') }}">Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('developer.tickets.index') }}">Tickets</a></li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side User Menu -->
            <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
                @auth
                    @php 
                        $___unread = auth()->user()?->unreadNotifications ?? collect();
                        $___count = $___unread->count();
                    @endphp
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="bi bi-bell"></span>
                            <span id="notifCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ ($___count)>0 ? '' : 'd-none' }}">{{ $___count }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifDropdown" style="min-width:320px; max-height:360px; overflow:auto">
                            <div id="notifList" class="list-group list-group-flush small">
                                @forelse($___unread as $n)
                                    @php $data = $n->data; $type = $data['type'] ?? 'ticket_reply'; @endphp
                                    @if($type === 'lab_test_ordered')
                                        <a href="{{ $data['link'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notification-item" data-id="{{ $n->id }}">
                                            <div class="me-2">
                                                <div class="fw-semibold">Lab Test Ordered</div>
                                                <div>{{ Str::limit($data['test_name'] ?? 'Test',40) }} for {{ Str::limit($data['inmate_name'] ?? 'Inmate',40) }}</div>
                                                <div class="text-muted small">by {{ $data['ordered_by'] ?? 'Doctor' }}</div>
                                                <div class="text-muted">{{ Str::limit(($data['message'] ?? '') ?: ($data['title'] ?? ''), 80) }}</div>
                                            </div>
                                            <span class="badge rounded-pill bg-primary">New</span>
                                        </a>
                                    @endif
                                @empty
                                    <div class="p-3 text-center text-muted">No new notifications</div>
                                @endforelse
                            </div>
                        </div>
                    </li>
                @endauth
                @auth
                    <li class="nav-item dropdown">
                        <a id="userDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="me-1 bi bi-person-circle"></span>
                            <span class="fw-medium">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Log in') }}</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // Mark-as-read then navigate for top navbar notifications
    document.querySelectorAll('#notifList a.notification-item').forEach(function(el){
        el.addEventListener('click', function(e){
            const href = this.getAttribute('href') || '#';
            const id = this.getAttribute('data-id');
            if(!id) return; // nothing to do
            e.preventDefault();
            fetch(`{{ url('/notifications') }}/${id}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || ''}})
                .catch(()=>{})
                .finally(()=>{
                    const c1 = document.getElementById('notifCount');
                    const c2 = document.getElementById('notifCountSidebar');
                    [c1,c2].forEach(c=>{ if(c){ let n = parseInt(c.textContent||'0',10); if(n>0){ n--; c.textContent=String(n); if(n===0){ c.classList.add('d-none'); } } } });
                    if(href && href !== '#') { window.location.assign(href); }
                });
        });
    });
});
</script>
