<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <h2 class="h5 mb-0">Inmates</h2>
        <a href="{{ route('admin.inmates.create') }}" class="btn btn-primary btn-sm"><span class="bi bi-plus-lg me-1"></span>Register</a>
        </div>
    </x-slot>

    <div class="d-lg-none mb-2">
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#inmateFilters">
            <span class="bi bi-funnel me-1"></span>Filters
        </button>
    </div>
    <div class="collapse d-lg-block" id="inmateFilters">
        <form method="GET" action="{{ route('admin.inmates.index') }}" class="card card-body mb-3 shadow-sm small border-0" id="adminInmateFilterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-0">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="name or admission number">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-0">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="juvenile" @selected(request('type')=='juvenile')>Juvenile</option>
                        <option value="adult" @selected(request('type')=='adult')>Adult</option>
                        <option value="senior" @selected(request('type')=='senior')>Senior</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-0">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(($statuses ?? []) as $st)
                            <option value="{{ $st }}" @selected(request('status')==$st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-0">Sort</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="created_desc" @selected(request('sort')=='created_desc')>Newest</option>
                        <option value="created_asc" @selected(request('sort')=='created_asc')>Oldest</option>
                        <option value="name_asc" @selected(request('sort')=='name_asc')>Name A-Z</option>
                        <option value="name_desc" @selected(request('sort')=='name_desc')>Name Z-A</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-secondary btn-sm flex-grow-1">Apply</button>
                    <a href="{{ route('admin.inmates.index') }}" class="btn btn-light btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <style>
        .inmate-item{transition:background-color .15s ease, box-shadow .15s ease, transform .15s ease;}
        .inmate-item:hover{background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px);} 
        .inmate-avatar{width:52px;height:52px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.1);} 
    </style>
    <div id="adminInmatesResults">
        @include('admin.inmates._list', ['inmates' => $inmates])
    </div>

    @push('scripts')
    <script>
    (function(){
        const form = document.getElementById('adminInmateFilterForm');
        const results = document.getElementById('adminInmatesResults');
        if(!form || !results) return;

        let timeout = null;
        let activeController = null;

        function buildUrl(baseUrl){
            const url = new URL(baseUrl || form.action, window.location.origin);
            const fd = new FormData(form);
            fd.delete('page');
            url.search = new URLSearchParams(fd).toString();
            return url;
        }

        async function load(url){
            if(activeController){ activeController.abort(); }
            activeController = new AbortController();
            results.classList.add('opacity-50');
            try{
                const res = await fetch(url.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}, signal: activeController.signal});
                if(!res.ok){ throw new Error('Request failed'); }
                results.innerHTML = await res.text();
                history.replaceState(null,'',url.toString());
            }catch(e){
                if(e.name === 'AbortError') return;
                results.innerHTML = '<div class="alert alert-warning small mb-0">Unable to load results. Please try again.</div>';
            }finally{
                results.classList.remove('opacity-50');
            }
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();
            load(buildUrl());
        });

        const searchInput = form.querySelector('input[name="search"]');
        if(searchInput){
            searchInput.addEventListener('input', function(){
                clearTimeout(timeout);
                timeout = setTimeout(function(){ load(buildUrl()); }, 250);
            });
        }

        const resetLink = form.querySelector('a.btn-light');
        resetLink?.addEventListener('click', function(e){
            e.preventDefault();
            if(searchInput){ searchInput.value = ''; }
            form.querySelectorAll('select').forEach(function(sel){ sel.selectedIndex = 0; });
            load(new URL(form.action, window.location.origin));
        });
        form.querySelectorAll('select').forEach(function(sel){
            sel.addEventListener('change', function(){ load(buildUrl()); });
        });

        results.addEventListener('click', function(e){
            const a = e.target.closest('a');
            if(!a) return;
            if(a.getAttribute('href') && a.getAttribute('href').includes('page=')){
                e.preventDefault();
                load(new URL(a.getAttribute('href'), window.location.origin));
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
