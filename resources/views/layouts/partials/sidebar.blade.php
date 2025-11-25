<aside class="aw-sidebar d-flex flex-column h-100 text-white position-relative" style="overflow:auto;">
    <style>
    .aw-sidebar{--aw-bg:#0f4f4b; --aw-bg-2:#0c4340; --aw-accent:#0EA5A1; background:var(--aw-bg); width:252px; min-width:252px}
        .aw-brand{height:28px;width:auto}
        .aw-link{color:#e6fbf7}
        .aw-link:hover{background:rgba(255,255,255,.10); color:#fff}
        .aw-link.active{background:rgba(14,165,161,.30); color:#fff; border-left:4px solid var(--aw-accent); border-radius:.6rem}
        .aw-section{border-bottom:1px solid rgba(255,255,255,.10)}
    .aw-sidebar .nav-link{white-space:nowrap; padding:.6rem .9rem}
    .aw-sidebar .nav-link .bi{display:inline-block; width:1.35rem; font-size:1.05rem; text-align:center; opacity:1; filter: drop-shadow(0 0 1px rgba(0,0,0,.15));}
    .aw-sidebar .nav-link .badge{font-size:.7rem}
    </style>
    <div class="p-3 aw-section d-flex align-items-center gap-2">
        @php
            $role = auth()->user()->role ?? null;
            $homeRoute = match($role){
                'system_admin' => 'system_admin.dashboard',
                'admin' => 'admin.dashboard',
                'doctor' => 'doctor.dashboard',
                'nurse' => 'nurse.dashboard',
                'staff' => 'staff.dashboard',
                'developer' => 'developer.dashboard',
                'guardian' => 'guardian.dashboard',
                default => 'dashboard',
            };
        @endphp
        <a href="{{ route($homeRoute) }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
            <img src="{{ asset('assets/aathmiya.png') }}" class="aw-brand" alt="aathmiya">
        </a>
    </div>

    <nav class="flex-grow-1 overflow-auto small" data-simplebar>
        <ul class="nav nav-pills flex-column py-2">
            @auth
                @php 
                    $role = auth()->user()->role; 
                    $unreadNotificationsCount = auth()->user()?->unreadNotifications()->count() ?? 0;
                @endphp
                <!-- Global Notifications link for all roles -->
                <li class="nav-item">
                    <a class="nav-link aw-link d-flex align-items-center justify-content-between {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                        <span><span class="bi bi-bell me-2"></span>Notifications</span>
                        <span id="notifCountSidebar" class="badge rounded-pill bg-danger {{ ($unreadNotificationsCount ?? 0)>0 ? '' : 'd-none' }}">{{ $unreadNotificationsCount ?? 0 }}</span>
                    </a>
                </li>
                @if($role==='developer')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.users.*') ? 'active' : '' }}" href="{{ route('developer.users.index') }}"><span class="bi bi-people me-2"></span>Users</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.institutions.*') ? 'active' : '' }}" href="{{ route('developer.institutions.index') }}"><span class="bi bi-buildings me-2"></span>Institutions</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.inmates.*') ? 'active' : '' }}" href="{{ route('developer.inmates.index') }}"><span class="bi bi-person-vcard me-2"></span>Inmates</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.requests.*') ? 'active' : '' }}" href="{{ route('developer.requests.index') }}"><span class="bi bi-clipboard-check me-2"></span>Requests</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.tickets.*') ? 'active' : '' }}" href="{{ route('developer.tickets.index') }}"><span class="bi bi-life-preserver me-2"></span>Tickets</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.doctors.*') ? 'active' : '' }}" href="{{ route('developer.doctors.index') }}"><span class="bi bi-stethoscope me-2"></span>Doctors</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('developer.allocation.*') ? 'active' : '' }}" href="{{ route('developer.allocation.index') }}"><span class="bi bi-grid-3x3-gap me-2"></span>Allocation</a></li>
                @elseif($role==='doctor')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('doctor.inmates.*') ? 'active' : '' }}" href="{{ route('doctor.inmates.index') }}"><span class="bi bi-person-badge me-2"></span>Patients</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}" href="{{ route('doctor.appointments.index') }}"><span class="bi bi-calendar-event me-2"></span>Schedule</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('doctor.lab-tests.*') ? 'active' : '' }}" href="{{ route('doctor.lab-tests.index') }}"><span class="bi bi-clipboard2-check me-2"></span>Lab Tests</a></li>
                @elseif($role==='nurse')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('nurse.inmates.*') ? 'active' : '' }}" href="{{ route('nurse.inmates.index') }}"><span class="bi bi-person-badge me-2"></span>Patients</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('nurse.lab-tests.*') ? 'active' : '' }}" href="{{ route('nurse.lab-tests.index') }}"><span class="bi bi-clipboard2-check me-2"></span>Lab Tests</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('nurse.meds.*') ? 'active' : '' }}" href="{{ route('nurse.meds.schedule') }}"><span class="bi bi-capsule-pill me-2"></span>Medications Today</a></li>
                @elseif($role==='staff')
                    <li class="nav-item"><a class="nav-link aw-link {{ (request()->routeIs('staff.inmates.index') || request()->routeIs('staff.inmates.show')) ? 'active' : '' }}" href="{{ route('staff.inmates.index') }}"><span class="bi bi-person-badge me-2"></span>Patients</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('staff.inmates.create') ? 'active' : '' }}" href="{{ route('staff.inmates.create') }}"><span class="bi bi-journal-plus me-2"></span>Admissions</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('staff.lab-tests.*') ? 'active' : '' }}" href="{{ route('staff.lab-tests.index') }}"><span class="bi bi-clipboard2-check me-2"></span>Lab Tests</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('staff.meds.*') ? 'active' : '' }}" href="{{ route('staff.meds.schedule') }}"><span class="bi bi-capsule-pill me-2"></span>Medications Today</a></li>
                @elseif($role==='admin')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.inmates.*') ? 'active' : '' }}" href="{{ route('admin.inmates.index') }}"><span class="bi bi-person-badge me-2"></span>Inmates</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}"><span class="bi bi-stethoscope me-2"></span>Doctors</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}"><span class="bi bi-person-badge me-2"></span>Staff</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.guardians.*') ? 'active' : '' }}" href="{{ route('admin.guardians.index') }}"><span class="bi bi-people me-2"></span>Guardians</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.medicines.*') ? 'active' : '' }}" href="{{ route('admin.medicines.index') }}"><span class="bi bi-box-seam me-2"></span>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.medications.*') ? 'active' : '' }}" href="{{ route('admin.medications.index') }}"><span class="bi bi-capsule-pill me-2"></span>Medicines</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('admin.allocation.*') ? 'active' : '' }}" href="{{ route('admin.allocation.index') }}"><span class="bi bi-grid-3x3-gap me-2"></span>Allocation</a></li>
                @elseif($role==='system_admin')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.inmates.*') ? 'active' : '' }}" href="{{ route('system_admin.inmates.index') }}"><span class="bi bi-person-badge me-2"></span>Inmates</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.institutions.*') ? 'active' : '' }}" href="{{ route('system_admin.institutions.index') }}"><span class="bi bi-building me-2"></span>Institutions</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.medicines.*') ? 'active' : '' }}" href="{{ route('system_admin.medicines.index') }}"><span class="bi bi-box-seam me-2"></span>Inventory</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.medications.*') ? 'active' : '' }}" href="{{ route('system_admin.medications.index') }}"><span class="bi bi-capsule-pill me-2"></span>Medicines</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.users.*') ? 'active' : '' }}" href="{{ route('system_admin.users.index') }}"><span class="bi bi-people me-2"></span>Users</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.doctors.*') ? 'active' : '' }}" href="{{ route('system_admin.doctors.index') }}"><span class="bi bi-person-badge me-2"></span>Doctors</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.blocks.*') ? 'active' : '' }}" href="{{ route('system_admin.blocks.index') }}"><span class="bi bi-grid-3x3-gap me-2"></span>Allocation</a></li>
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('system_admin.guardians.*') ? 'active' : '' }}" href="{{ route('system_admin.guardians.index') }}"><span class="bi bi-people-fill me-2"></span>Guardians</a></li>
                @elseif($role==='guardian')
                    <li class="nav-item"><a class="nav-link aw-link {{ request()->routeIs('guardian.dashboard') ? 'active' : '' }}" href="{{ route('guardian.dashboard') }}"><span class="bi bi-speedometer2 me-2"></span>Overview</a></li>
                @endif
            @endauth
            <li class="mt-3 px-3 text-uppercase small text-white-50">Account</li>
            @php 
                $u = auth()->user();
                $nm = trim($u->name ?? '');
                $parts = preg_split('/\s+/', $nm);
                $first = $parts[0] ?? '';
                $last = end($parts) ?: '';
                $ini = strtoupper(substr($first,0,1) . substr($last,0,1));
            @endphp
            <li class="nav-item">
                <a class="nav-link aw-link {{ request()->routeIs('profile.edit') ? 'active' : '' }} d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                    @if($u?->avatar_url)
                        <img src="{{ $u->avatar_url }}" class="rounded-circle" style="width:22px;height:22px;object-fit:cover" alt="avatar">
                    @else
                        <span class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center" style="width:22px;height:22px; font-size:.7rem">{{ $ini }}</span>
                    @endif
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
                    <button class="nav-link aw-link w-100 text-start border-0 bg-transparent"><span class="bi bi-box-arrow-right me-2"></span>Logout</button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="p-2 border-top small text-center text-white-50" style="border-color:rgba(255,255,255,.08)!important">&copy; {{ date('Y') }} Aathmiya</div>
</aside>
