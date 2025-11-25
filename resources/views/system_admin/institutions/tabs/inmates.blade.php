<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
  <h6 class="mb-0">Institution Inmates</h6>
  <form method="GET" class="d-flex gap-2" onsubmit="event.preventDefault(); this.closest('#tabContent') && window.tabSearchInmates && window.tabSearchInmates(this);">
    <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search name" />
    <button class="btn btn-sm btn-outline-secondary"><span class="bi bi-search"></span></button>
  </form>
</div>
<div class="list-group shadow-sm mb-2">
  @forelse($inmates as $inmate)
    <a href="{{ route('system_admin.inmates.show',$inmate) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><span class="bi bi-person-bounding-box me-2 text-muted"></span>{{ $inmate->last_name }}, {{ $inmate->first_name }}</span>
      <span class="text-muted small">#{{ $inmate->id }}</span>
    </a>
  @empty
    <div class="list-group-item text-center text-muted">No inmates found.</div>
  @endforelse
  @if($inmates->hasPages())
    <div class="list-group-item">{{ $inmates->withQueryString()->links() }}</div>
  @endif
</div>
<script>
  window.tabSearchInmates = function(form){
    const container = document.getElementById('tabContent');
    const url = new URL('{{ route('system_admin.institutions.tabs.inmates', $institution) }}', window.location.origin);
    const q = form.querySelector('[name=q]').value;
    if(q) url.searchParams.set('q', q);
    container.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>';
    fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.text()).then(html=>container.innerHTML=html);
  }
</script>
