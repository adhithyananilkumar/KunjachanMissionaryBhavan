<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h2 class="h4 mb-0">Developer Mission Control</h2>
                <p class="text-muted small mb-0">Global platform diagnostics & orchestration</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('developer.institutions.create') }}" class="btn btn-primary btn-sm"><span class="bi bi-building-add me-1"></span>New Institution</a>
                <a href="{{ route('developer.users.create') }}" class="btn btn-outline-secondary btn-sm"><span class="bi bi-person-plus me-1"></span>New User</a>
            </div>
        </div>
    </x-slot>

    <style>.stat-card{position:relative;overflow:hidden}.stat-icon{position:absolute;right:.75rem;top:.75rem;font-size:2.25rem;opacity:.15}</style>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><x-stat-card icon="building" label="Institutions" :value="$stats['institutions']"/></div>
        <div class="col-6 col-md-3"><x-stat-card icon="people" label="Users" :value="$stats['users']"/></div>
        <div class="col-6 col-md-3"><x-stat-card icon="person-badge" label="Inmates" :value="$stats['inmates']"/></div>
        <div class="col-6 col-md-3"><x-stat-card icon="clipboard-data" label="Pending Requests" :value="$stats['pending_requests'] ?? 0"/></div>
        <div class="col-6 col-md-3"><x-stat-card icon="bug" label="Open Tickets" :value="$stats['open_bugs'] ?? 0"/></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Inmates by Type</span>
                    <span class="small text-muted">Live distribution</span>
                </div>
                <div class="card-body">
                    <canvas id="inmatesTypeChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span>Recent Activity</span>
                    <span class="small text-muted">Last 10</span>
                </div>
                <ul class="list-group list-group-flush small" style="max-height:340px;overflow:auto">
                    @forelse($recent as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-2">
                                <div class="fw-semibold text-capitalize"><span class="bi bi-@if($item['type']=='institution')building@elseif($item['type']=='user')person@else person-badge @endif me-1"></span>{{ $item['label'] }}</div>
                                <div class="text-muted text-capitalize">{{ $item['type'] }}</div>
                            </div>
                            <span class="badge bg-light text-muted border">{{ optional($item['when'])->diffForHumans(null,true) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No recent items</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">System Health</div>
        <div class="card-body small">
            <div class="row g-3">
                <div class="col-md-4"><span class="bi bi-lightning-charge text-warning me-2"></span>Performance nominal</div>
                <div class="col-md-4"><span class="bi bi-bell text-info me-2"></span>{{ auth()->user()->unreadNotifications->count() }} unread notifications</div>
                <div class="col-md-4"><span class="bi bi-git text-danger me-2"></span>Branch: master</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha256-kmK1JtLqUqOS9sd8mrrhJX7E0UuBiOPr9DgM00HgI1g=" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const ctx = document.getElementById('inmatesTypeChart');
        const labels = @json($typeCounts->keys());
        const data = @json($typeCounts->values());
        new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets:[{ label:'Inmates', data, backgroundColor:'#0d6efd', borderRadius:4 }] },
            options: { responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } }
        });
    });
    </script>
</x-app-layout>
