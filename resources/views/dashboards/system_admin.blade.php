<x-plain-app-layout>
    <x-slot name="header">
    <!-- Header is provided by plain-app-layout topbar; slot kept for compatibility -->
    </x-slot>

    <style>
        .aw-hero{background:#ffffff}
        .aw-tile{
            background:#53d3c7; color:#fff; border:none; border-radius:14px;
            padding:18px 16px; width:100%; text-align:center;
            box-shadow:0 4px 16px rgba(0,0,0,.06);
            transition:transform .15s ease, box-shadow .2s ease, background .2s ease;
        }
        .aw-tile:hover{ transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.12); background:#45c7bb }
        .aw-tile .tile-icon{ font-size:1.6rem; display:inline-block; margin-bottom:.35rem }
        .aw-tile .en{ font-weight:700; letter-spacing:.4px }
        .aw-logo{ height:94px; width:auto }
        @media (min-width: 992px){ .aw-logo{ height:120px } }
    </style>

    <div class="container-fluid aw-hero">
        <div class="d-flex flex-column align-items-center text-center py-4 py-md-5">
            <img class="aw-logo mb-3" src="{{ asset('assets/logo-241x271.png') }}" alt="Aathmiya Logo"
                 onerror="this.onerror=null;this.src='{{ asset('assets/logo-108x121.png') }}'">

        <div class="row g-3 g-sm-4 justify-content-center w-100" style="max-width:980px">
            @php
                $tiles = [
                    ['route' => 'system_admin.inmates.create', 'icon' => 'journal-plus', 'title' => 'ADMISSION', 'subtitle' => 'Register new inmate'],
                    ['route' => 'system_admin.inmates.index', 'icon' => 'person-badge', 'title' => 'INMATES', 'subtitle' => 'View all inmates'],
                    ['route' => 'system_admin.users.index', 'icon' => 'people', 'title' => 'STAFFS', 'subtitle' => 'Manage staff'],
                    ['route' => 'system_admin.institutions.index', 'icon' => 'building', 'title' => 'INSTITUTIONS', 'subtitle' => 'Manage institutions'],
                    ['route' => 'system_admin.medicines.index', 'icon' => 'box-seam', 'title' => 'INVENTORY', 'subtitle' => 'Manage stock'],
                    ['route' => 'system_admin.medications.index', 'icon' => 'capsule-pill', 'title' => 'MEDICINES', 'subtitle' => 'Schedules & logs'],
                    ['route' => 'system_admin.doctors.index', 'icon' => 'person-badge', 'title' => 'DOCTORS', 'subtitle' => 'View schedules'],
                    ['route' => 'system_admin.blocks.index', 'icon' => 'grid-3x3-gap', 'title' => 'ALLOCATION', 'subtitle' => 'Blocks & locations'],
                    ['route' => 'system_admin.guardians.index', 'icon' => 'people-fill', 'title' => 'GUARDIANS', 'subtitle' => 'Manage guardians'],
                ];
            @endphp
            @foreach($tiles as $t)
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route($t['route']) }}">
                        <span class="bi bi-{{ $t['icon'] }} tile-icon"></span>
                        <div class="en">{{ $t['title'] }}</div>
                        <div class="small opacity-75 mt-1">{{ $t['subtitle'] }}</div>
                    </a>
                </div>
            @endforeach
        </div>
        </div>
    </div>
</x-plain-app-layout>
