<x-plain-app-layout>
    <x-slot name="header"></x-slot>

    <style>
        .aw-hero{background:#ffffff}
        .aw-tile{background:#53d3c7; color:#fff; border:none; border-radius:14px; padding:18px 16px; width:100%; text-align:center; box-shadow:0 4px 16px rgba(0,0,0,.06); transition:transform .15s ease, box-shadow .2s ease, background .2s ease}
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
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('staff.inmates.create') }}">
                        <span class="bi bi-journal-plus tile-icon"></span>
                        <div class="en">ADMISSION</div>
                        <div class="small opacity-75 mt-1">Register new inmate</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('staff.inmates.index') }}">
                        <span class="bi bi-person-badge tile-icon"></span>
                        <div class="en">INMATES</div>
                        <div class="small opacity-75 mt-1">Browse inmates</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('staff.lab-tests.index') }}">
                        <span class="bi bi-clipboard2-check tile-icon"></span>
                        <div class="en">LAB TESTS</div>
                        <div class="small opacity-75 mt-1">Update results and notes</div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a class="aw-tile text-decoration-none d-block" href="{{ route('staff.meds.schedule') }}">
                        <span class="bi bi-capsule-pill tile-icon"></span>
                        <div class="en">MEDICATIONS TODAY</div>
                        <div class="small opacity-75 mt-1">Administer and log</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-plain-app-layout>
