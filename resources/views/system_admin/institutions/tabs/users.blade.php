<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
  <h6 class="mb-0">Institution Staff</h6>
  <form method="GET" class="d-flex gap-2" onsubmit="event.preventDefault(); this.closest('#tabContent') && window.tabSearch && window.tabSearch(this);">
    <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search name/email" />
    <button class="btn btn-sm btn-outline-secondary"><span class="bi bi-search"></span></button>
  </form>
</div>
<div class="list-group shadow-sm mb-2">
  @forelse($users as $user)
    <a href="{{ route('system_admin.users.show',$user) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
      <span><span class="bi bi-person me-2 text-muted"></span>{{ $user->name }} <span class="text-muted small">{{ $user->email }}</span></span>
      <span class="badge text-bg-light border text-capitalize">{{ $user->role }}</span>
    </a>
  @empty
    <div class="list-group-item text-center text-muted">No staff found.</div>
  @endforelse
  @if($users->hasPages())
    <div class="list-group-item">{{ $users->withQueryString()->links() }}</div>
  @endif
</div>
<script>
  window.tabSearch = function(form){
    const container = document.getElementById('tabContent');
    const url = new URL('{{ route('system_admin.institutions.tabs.users', $institution) }}', window.location.origin);
    const q = form.querySelector('[name=q]').value;
    if(q) url.searchParams.set('q', q);
    container.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>';
    fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.text()).then(html=>container.innerHTML=html);
  }
</script>
