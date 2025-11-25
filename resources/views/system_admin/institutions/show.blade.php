<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">Institution Profile</h2>
    </x-slot>

    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle border border-success text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <span class="bi bi-building fs-4"></span>
                </div>
                <div>
                    <div class="h5 mb-0">{{ $institution->name }}</div>
                    <div class="text-muted small">{{ $institution->address }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 small">
                <span class="badge text-bg-light border"><span class="bi bi-people me-1"></span>{{ $institution->users_count }} Staff</span>
                <span class="badge text-bg-light border"><span class="bi bi-person-bounding-box me-1"></span>{{ $institution->inmates_count }} Inmates</span>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs small" id="instTabs" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" data-tab="overview" type="button" role="tab">Overview</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-tab="users" type="button" role="tab">Users</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-tab="inmates" type="button" role="tab">Inmates</button></li>
        <li class="nav-item ms-auto" role="presentation"><button class="nav-link" data-tab="settings" type="button" role="tab"><span class="bi bi-gear me-1"></span>Settings</button></li>
    </ul>
    <div id="tabContent" class="card card-body shadow-sm rounded-top-0" data-institution-id="{{ $institution->id }}">
        <div class="text-center text-muted py-5" id="tabLoading"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const container = document.getElementById('tabContent');
        const tabs = document.querySelectorAll('#instTabs [data-tab]');
        let active = 'overview';
        function setHash(tab){
            if(location.hash !== `#${tab}`){
                location.hash = `#${tab}`;
            }
        }
    function load(tab, url){
            active = tab;
            tabs.forEach(b=>b.classList.toggle('active', b.getAttribute('data-tab')===tab));
            container.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>';
            const urlMap = {
                overview: '{{ route('system_admin.institutions.tabs.overview', $institution) }}',
                users: '{{ route('system_admin.institutions.tabs.users', $institution) }}',
                inmates: '{{ route('system_admin.institutions.tabs.inmates', $institution) }}',
                settings: '{{ route('system_admin.institutions.tabs.settings', $institution) }}',
            };
            const finalUrl = url || urlMap[tab];
            fetch(finalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r=>r.text()).then(html=>{ container.innerHTML = html; });
        }
        tabs.forEach(b=>b.addEventListener('click',()=>{ const t=b.getAttribute('data-tab'); setHash(t); load(t); }));
        // Handle in-tab pagination/links to keep SPA-like feel
        container.addEventListener('click', function(e){
            const a = e.target.closest('a');
            if(!a) return;
            const href = a.getAttribute('href');
            if(!href) return;
            // Intercept links that point to the current tab routes (e.g., pagination)
            if(href.includes('/tabs/'+active)){
                e.preventDefault();
                load(active, href);
            }
        });
        // Delegated AJAX save for Settings form (scripts inside injected HTML don't auto-run)
        const updateUrl = '{{ route('system_admin.institutions.update', $institution) }}';
        const csrfToken = '{{ csrf_token() }}';
        container.addEventListener('submit', function(e){
            const form = e.target.closest('#settingsForm');
            if(!form) return; // not settings form
            e.preventDefault();
            const data = new FormData(form);
            data.append('_method','PUT');
            form.querySelectorAll('.alert').forEach(n=>n.remove());
            const saving = document.createElement('div');
            saving.className = 'alert alert-info small mt-2';
            saving.textContent = 'Saving…';
            form.appendChild(saving);
            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: data
            }).then(r=>r.json()).then(resp=>{
                saving.remove();
                const ok = document.createElement('div');
                ok.className = 'alert alert-success small mt-2';
                ok.textContent = (resp && resp.message) ? resp.message : 'Saved.';
                form.appendChild(ok);
                setTimeout(()=>ok.remove(), 3000);
            }).catch(err=>{
                saving.remove();
                const errEl = document.createElement('div');
                errEl.className = 'alert alert-danger small mt-2';
                errEl.textContent = 'Save failed';
                form.appendChild(errEl);
                setTimeout(()=>errEl.remove(), 4000);
            });
        });
        // Restore active tab from hash
        const initial = (location.hash||'').replace('#','') || 'overview';
        const allowed = ['overview','users','inmates','settings'];
        load(allowed.includes(initial)?initial:'overview');
        // React to hash change (e.g., after refresh/back)
        window.addEventListener('hashchange', ()=>{
            const h = (location.hash||'').replace('#','');
            if(h && h!==active){ load(h); }
        });
    });
    </script>
</x-app-layout>
