@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row g-4">
    <div class="col-12 col-lg-8 col-xl-7">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="mb-0" style="white-space:nowrap">Notifications</h5>
          <div class="d-flex gap-2 flex-wrap">
            <button id="markAllBtn" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="unreadOnly" checked>
              <label class="form-check-label" for="unreadOnly">Unread only</label>
            </div>
          </div>
        </div>
        <div class="list-group list-group-flush" id="notifFeed">
          <div class="p-3 text-muted">Loading…</div>
        </div>
        <div class="card-footer small text-muted">
          Updates automatically every 6s via polling
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-4 col-xl-5">
      <div class="card shadow-sm">
        <div class="card-header"><h6 class="mb-0">Tips</h6></div>
        <div class="card-body small text-muted">
          - Click a notification to open the related page and mark it read.<br>
          - Use the toggle to view all vs. unread only.<br>
          - The bell badges update when items are marked read here.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const feedEl = document.getElementById('notifFeed');
  const unreadOnlyEl = document.getElementById('unreadOnly');
  const markAllBtn = document.getElementById('markAllBtn');
  let lastFetchedAt = null;
  let timer = null;

  function render(items){
    if(!items || items.length===0){
      feedEl.innerHTML = '<div class="p-3 text-muted">No notifications</div>';
      return;
    }
    feedEl.innerHTML = '';
    items.forEach(item => {
      const a = document.createElement(item.link ? 'a' : 'div');
      if(item.link){ a.href = item.link; a.rel = 'noopener'; }
      a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-start';
      a.dataset.id = item.id;
      a.innerHTML = `
        <div class="me-2">
          <div class="fw-semibold">${item.title || 'Notification'}</div>
          <div class="text-muted">${item.message || ''}</div>
          <div class="text-muted small">${new Date(item.created_at).toLocaleString()}</div>
        </div>
        <span class="badge rounded-pill ${item.read ? 'bg-secondary' : 'bg-primary'}">${item.read ? 'Read' : 'New'}</span>
      `;
      a.addEventListener('click', function(ev){
        // Mark-read first, then navigate if this is a link
        ev.preventDefault();
        const href = a.getAttribute('href');
        fetch(`{{ url('/notifications') }}/${item.id}/mark-read`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}})
          .catch(()=>{})
          .finally(()=>{
            const badge = a.querySelector('.badge');
            if(badge){ badge.classList.remove('bg-primary'); badge.classList.add('bg-secondary'); badge.textContent = 'Read'; }
            const c1 = document.getElementById('notifCount');
            const c2 = document.getElementById('notifCountSidebar');
            [c1,c2].forEach(c=>{ if(c){ let n = parseInt(c.textContent||'0',10); if(n>0){ n--; c.textContent=String(n); if(n===0){ c.classList.add('d-none'); } } } });
            if(href){ window.location.assign(href); }
          });
      });
      feedEl.appendChild(a);
    });
  }

  function load(){
    const params = new URLSearchParams();
    if (unreadOnlyEl.checked) params.set('unread_only','1');
    if (lastFetchedAt) params.set('since', lastFetchedAt);
    fetch(`{{ route('notifications.feed') }}?${params.toString()}`, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(r=>r.json())
      .then(({items})=>{
        lastFetchedAt = new Date().toISOString();
        render(items);
      })
      .catch(()=>{ /* keep silent on transient errors */ });
  }

  unreadOnlyEl.addEventListener('change', ()=>{ lastFetchedAt=null; load(); });
  markAllBtn.addEventListener('click', ()=>{
    fetch(`{{ route('notifications.mark-all') }}`, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}})
      .then(()=>{ lastFetchedAt=null; load();
        // Zero out badges
        const c1=document.getElementById('notifCount'); const c2=document.getElementById('notifCountSidebar');
        [c1,c2].forEach(c=>{ if(c){ c.textContent='0'; c.classList.add('d-none'); } });
      });
  });

  // Pause polling while user is reading the list (hover)
  feedEl.addEventListener('mouseenter', ()=>{ if(timer){ clearInterval(timer); timer=null; } });
  feedEl.addEventListener('mouseleave', ()=>{ if(!timer){ timer=setInterval(load,6000); } });

  // initial load and polling every 6s
  load();
  timer = setInterval(load, 6000);
  document.addEventListener('visibilitychange', function(){
    if(document.hidden){ clearInterval(timer); timer=null; }
    else if(!timer){ timer = setInterval(load, 6000); load(); }
  });
})();
</script>
@endpush
