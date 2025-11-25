<x-plain-app-layout>
    <x-slot name="header">
        <!-- Header provided by plain layout topbar -->
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
                <!-- 1-col mobile, 2-col tablets (md), 3-col desktop (lg) -->
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.inmates.create') }}">
                        <span class="bi bi-journal-plus tile-icon"></span>
                        <div class="en">ADMISSION</div>
                        <div class="small opacity-75 mt-1">Register new inmate</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.inmates.index') }}">
                        <span class="bi bi-person-badge tile-icon"></span>
                        <div class="en">INMATES</div>
                        <div class="small opacity-75 mt-1">View all inmates</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.guardians.index') }}">
                        <span class="bi bi-people-fill tile-icon"></span>
                        <div class="en">GUARDIANS</div>
                        <div class="small opacity-75 mt-1">Manage guardians</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.staff.index') }}">
                        <span class="bi bi-people tile-icon"></span>
                        <div class="en">STAFFS</div>
                        <div class="small opacity-75 mt-1">Manage staff</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.doctors.index') }}">
                        <span class="bi bi-stethoscope tile-icon"></span>
                        <div class="en">DOCTORS</div>
                        <div class="small opacity-75 mt-1">View schedules</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.allocation.index') }}">
                        <span class="bi bi-grid-3x3-gap tile-icon"></span>
                        <div class="en">ALLOCATION</div>
                        <div class="small opacity-75 mt-1">Blocks & locations</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.medicines.index') }}">
                        <span class="bi bi-capsule tile-icon"></span>
                        <div class="en">MEDICINES</div>
                        <div class="small opacity-75 mt-1">Catalog & inventory</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('admin.requests.index') }}">
                        <span class="bi bi-clipboard-check tile-icon"></span>
                        <div class="en">REQUESTS</div>
                        <div class="small opacity-75 mt-1">My action requests</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-plain-app-layout>
