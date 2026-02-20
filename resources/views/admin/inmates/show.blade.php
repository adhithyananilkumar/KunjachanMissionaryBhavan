<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Inmate Profile</h2></x-slot>

	@if($inmate->critical_alert)
		<div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm mb-3">
			<span class="bi bi-exclamation-triangle-fill fs-5"></span>
			<div>
				<strong>Critical Alert:</strong>
				<div class="mt-1">{!! nl2br(e($inmate->critical_alert)) !!}</div>
			</div>
		</div>
	@endif

	@php $stBanner = $inmate->status ?: \App\Models\Inmate::STATUS_PRESENT; @endphp
	@if($stBanner !== \App\Models\Inmate::STATUS_PRESENT)
		<div class="alert alert-warning d-flex align-items-start gap-2 shadow-sm mb-3">
			<span class="bi bi-shield-lock-fill fs-5"></span>
			<div>
				<strong>Read-only:</strong>
				@if($stBanner === \App\Models\Inmate::STATUS_DECEASED)
					This inmate is marked as deceased. Changes are permanently disabled.
				@else
					This inmate is {{ $stBanner }}. Most changes are disabled until re-joined.
				@endif
			</div>
		</div>
	@endif

	<div class="card shadow-sm mb-3">
		<div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
			<div class="d-flex align-items-center gap-3">
				<img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="avatar">
				<div>
					<div class="h5 mb-0">{{ $inmate->full_name }}</div>
					<div class="text-muted small d-flex flex-wrap align-items-center gap-2">
						<span>Admission No : <strong>{{ $inmate->admission_number }}</strong> · {{ $inmate->institution?->name ?: '—' }}</span>
						@include('partials.inmates._status_badge', ['inmate' => $inmate])
					</div>
					<div class="small mt-1">
						<span class="text-muted">Allocation:</span>
						<span class="fw-semibold">{{ optional($inmate->currentLocation?->location)->name ?? 'Not assigned' }}</span>
					</div>
				</div>
			</div>
			<div class="d-flex flex-wrap gap-2">
				@php $st = $inmate->status ?: \App\Models\Inmate::STATUS_PRESENT; @endphp
				<div class="dropdown">
					<button class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" type="button">
						<span class="bi bi-arrow-repeat me-1"></span>Status
					</button>
					<div class="dropdown-menu dropdown-menu-end shadow-sm">
						@if($st === \App\Models\Inmate::STATUS_PRESENT)
							<button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#statusDischargeModal"><span class="bi bi-box-arrow-right me-2"></span>Discharge</button>
							<button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#statusDeceasedModal"><span class="bi bi-x-octagon me-2"></span>Mark Deceased</button>
						@elseif(in_array($st, [\App\Models\Inmate::STATUS_DISCHARGED, \App\Models\Inmate::STATUS_TRANSFERRED], true))
							<button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#statusRejoinModal"><span class="bi bi-arrow-counterclockwise me-2"></span>Re-Join (Present)</button>
						@else
							<span class="dropdown-item-text text-muted small">Status locked.</span>
						@endif
					</div>
				</div>
				<a href="{{ route('admin.inmates.edit',$inmate) }}" class="btn btn-outline-primary btn-sm"><span class="bi bi-pencil-square me-1"></span>Edit</a>
				<a href="{{ route('admin.inmates.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
			</div>
		</div>
	</div>

	<ul class="nav nav-tabs small" id="inmateTabs" role="tablist">
		<li class="nav-item"><button class="nav-link active" data-tab="overview" type="button">Overview</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="medical" type="button">Medical</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="history" type="button">History</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="documents" type="button">Documents</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="allocation" type="button">Allocation</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="status" type="button">Status</button></li>
		<li class="nav-item ms-auto"><button class="nav-link" data-tab="settings" type="button"><span class="bi bi-gear me-1"></span>Settings</button></li>
	</ul>
	<div id="inmateTabContent" class="card card-body shadow-sm rounded-top-0" data-inmate-id="{{ $inmate->id }}">
		<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function(){
			const container = document.getElementById('inmateTabContent');
			const tabs = document.querySelectorAll('#inmateTabs [data-tab]');
			let active = 'overview';
			let currentController = null;
			let currentSeq = 0;

			function renderLoadError(message, retryFn){
				container.innerHTML = `
					<div class="text-center py-5">
						<div class="text-danger mb-2"><span class="bi bi-exclamation-triangle me-1"></span>${message}</div>
						<button type="button" class="btn btn-sm btn-outline-secondary" data-retry>Retry</button>
					</div>
				`;
				container.querySelector('[data-retry]')?.addEventListener('click', retryFn);
			}

			function load(tab, url){
				active = tab;
				tabs.forEach(b=>b.classList.toggle('active', b.getAttribute('data-tab')===tab));
				container.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>';
				const urlMap = {
					overview: '{{ route('admin.inmates.show',$inmate) }}?partial=overview',
					medical: '{{ route('admin.inmates.show',$inmate) }}?partial=medical',
					history: '{{ route('admin.inmates.show',$inmate) }}?partial=history',
					documents: '{{ route('admin.inmates.show',$inmate) }}?partial=documents',
					allocation: '{{ route('admin.inmates.show',$inmate) }}?partial=allocation',
					status: '{{ route('admin.inmates.show',$inmate) }}?partial=status',
					settings: '{{ route('admin.inmates.show',$inmate) }}?partial=settings',
				};
				const finalUrl = url || urlMap[tab];
				const seq = ++currentSeq;
				if(currentController){
					try{ currentController.abort(); }catch(e){}
				}
				currentController = new AbortController();
				const timeoutMs = 20000;
				const timeoutId = window.setTimeout(()=>{
					try{ currentController.abort(); }catch(e){}
				}, timeoutMs);
				fetch(finalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}, signal: currentController.signal})
					.then(async (r)=>{
						if(!r.ok){
							throw new Error(`Failed to load (HTTP ${r.status})`);
						}
						return r.text();
					})
					.then(html=>{
						if(seq !== currentSeq) return;
						window.clearTimeout(timeoutId);
						container.innerHTML = html;
						if(tab==='allocation'){ initAllocationTab(); }
						if(tab==='documents'){ initDocumentsTab(); }
					})
					.catch((err)=>{
						if(seq !== currentSeq) return;
						window.clearTimeout(timeoutId);
						const msg = (err && err.name === 'AbortError')
							? 'Request timed out. Please retry.'
							: (err?.message || 'Failed to load. Please retry.');
						renderLoadError(msg, ()=> load(tab, url));
					});
				if(location.hash !== '#'+tab) location.hash = '#'+tab;
			}
			tabs.forEach(b=>b.addEventListener('click',()=>load(b.getAttribute('data-tab'))));
			container.addEventListener('click', function(e){
				const a = e.target.closest('a');
				if(!a) return;
				const href = a.getAttribute('href');
				if(!href) return;
				if(href.includes('partial='+active)){
					e.preventDefault();
					load(active, href);
				}
			});
			container.addEventListener('submit', function(e){
				const form = e.target.closest('form[data-ajax]');
				if(!form) return;
				e.preventDefault();
				const data = new FormData(form);
				const url = form.getAttribute('action');
				fetch(url, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body:data})
					.then(r=>r.json()).then(()=>{ const ok=document.createElement('div'); ok.className='alert alert-success small mt-2'; ok.textContent='Saved'; form.appendChild(ok); setTimeout(()=>ok.remove(),2500); })
					.catch(()=>{ const er=document.createElement('div'); er.className='alert alert-danger small mt-2'; er.textContent='Save failed'; form.appendChild(er); setTimeout(()=>er.remove(),3000); });
			});
			const initial = (location.hash||'').replace('#','') || 'overview';
			const allowed=['overview','medical','history','documents','allocation','status','settings'];
			load(allowed.includes(initial)?initial:'overview');
			window.addEventListener('hashchange',()=>{ const h=(location.hash||'').replace('#',''); if(h && h!==active) load(h); });

			function initAllocationTab(){
				const form = document.getElementById('allocAssignForm'); if(!form || form.dataset.bound==='1') return; form.dataset.bound='1';
				const list = document.getElementById('allocRoomsList');
				const q = document.getElementById('allocRoomSearch');
				const reloadBtn = document.getElementById('allocReload');
				const showOcc = document.getElementById('allocShowOcc');
				const hid = document.getElementById('allocLocationId');
				const out = document.getElementById('allocSelected');
				const inst = form.getAttribute('data-inst') || '{{ (int)auth()->user()->institution_id }}';
				let rooms = [];
				async function loadRooms(){
					try{
						const res = await fetch(`{{ url('admin/allocation/api/institutions') }}/${inst}/locations?show_occupied=${showOcc?.checked?1:0}`, {headers:{'Accept':'application/json'}});
						if(!res.ok){ throw new Error('Failed to load rooms'); }
						const data = await res.json(); rooms = data.locations||[]; renderRooms();
					}catch(err){ if(list) list.innerHTML = `<div class="text-danger small py-3 text-center">${err.message || 'Failed to load rooms'}</div>`; }
				}
				function renderRooms(){
					if(!list) return; const term=(q?.value||'').toLowerCase(); list.innerHTML='';
					const items = rooms.filter(r=> (r.name||'').toLowerCase().includes(term));
					if(items.length===0){ list.innerHTML='<div class="text-muted text-center py-3">No rooms</div>'; return; }
					items.forEach(r=>{
						const btn = document.createElement('button'); btn.type='button'; btn.className='list-group-item list-group-item-action d-flex justify-content-between align-items-center';
						const occBadge = r.occupant? `<span class="badge bg-secondary ms-2">${r.occupant}</span>`:'';
						btn.innerHTML = `<span><span class=\"bi bi-door-closed me-2\"></span>${r.name}${occBadge}</span>` +
							`<span class=\"badge ${r.occupied?'bg-secondary':'bg-success'}\">${r.occupied?'Occupied':'Available'}</span>`;
						btn.disabled = r.occupied && !(showOcc?.checked);
						btn.addEventListener('click',()=>{ if(hid) hid.value=r.id; if(out) out.textContent=r.name; container.querySelectorAll('#allocRoomsList .list-group-item').forEach(x=>x.classList.remove('active')); btn.classList.add('active'); });
						list.appendChild(btn);
					});
				}
				q?.addEventListener('input', renderRooms);
				reloadBtn?.addEventListener('click', loadRooms);
				showOcc?.addEventListener('change', loadRooms);
				container.querySelector('#allocClear')?.addEventListener('click', ()=>{ if(hid) hid.value=''; if(out) out.textContent='None'; container.querySelectorAll('#allocRoomsList .list-group-item').forEach(x=>x.classList.remove('active')); });
				form.addEventListener('submit', async (e)=>{
					e.preventDefault(); if(!hid?.value){ const notice=document.createElement('div'); notice.className='alert alert-warning small mt-2'; notice.textContent='Please choose a room to assign.'; form.appendChild(notice); setTimeout(()=>notice.remove(),2500); return; }
					try{
						const fd = new FormData(form);
						const res = await fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: fd});
						if(res.ok){ const ok=document.createElement('div'); ok.className='alert alert-success small mt-2'; ok.textContent='Location updated.'; form.appendChild(ok); setTimeout(()=>location.reload(),500); return; }
						const d = await res.json(); throw new Error(d.message || 'Update failed');
					}catch(err){ const er=document.createElement('div'); er.className='alert alert-danger small mt-2'; er.textContent=err.message || 'Update failed'; form.appendChild(er); setTimeout(()=>er.remove(),3000); }
				});
				loadRooms();
			}

			function initDocumentsTab(){
				const root = container.querySelector('#docSubTabContent');
				if(!root || root.dataset.bound==='1') return; root.dataset.bound='1';
				const uploadUrl = root.getAttribute('data-upload-url');
				const addUrl = root.getAttribute('data-add-url');
				const toast = (type, msg)=>{
					try{
						if(window.toastr && typeof window.toastr[type]==='function') window.toastr[type](msg);
						else if(window.AppNotice) window.AppNotice.show(type, msg);
					}catch(e){}
				};
				const tabs = container.querySelectorAll('#docSubTabs [data-subtab]');
				const panes = root.querySelectorAll('[data-pane]');
				function showPane(name){
					tabs.forEach(b=> b.classList.toggle('active', b.getAttribute('data-subtab')===name));
					panes.forEach(p=>{ const on = p.getAttribute('data-pane')===name; p.classList.toggle('show', on); });
				}
				tabs.forEach(b=> b.addEventListener('click', ()=> showPane(b.getAttribute('data-subtab'))));
				function openDocPreview(url, name){
					if(!url) return;
					const modalEl = document.getElementById('docPreviewModal');
					const body = document.getElementById('docPreviewBody');
					const title = document.getElementById('docPreviewTitle');
					if(title){ title.textContent = name || 'Document'; }
					const clean = (url.split('?')[0] || '');
					const ext = (clean.split('.').pop()||'').toLowerCase();
					let content = '';
					if(['jpg','jpeg','png','gif','webp'].includes(ext)){
						content = `<img src="${url}" alt="${name||'Document'}" style="max-width:100%;max-height:75vh;object-fit:contain;" />`;
					} else if(ext==='pdf'){
						content = `<iframe src="${url}" style="width:100%;height:75vh;border:0;" title="${name||'Document'}"></iframe>`;
					} else {
						content = `<div class='text-center p-3 small'><a href='${url}' target='_blank' rel='noopener'>Open File</a></div>`;
					}
					if(body){ body.innerHTML = content; }
					if(modalEl && typeof bootstrap !== 'undefined'){
						bootstrap.Modal.getOrCreateInstance(modalEl).show();
					} else {
						window.open(url,'_blank');
					}
				}
				// Document click preview (event delegation)
				root.addEventListener('click', (e)=>{
					const openBtn = e.target.closest('.doc-open-btn');
					if(openBtn){
						e.preventDefault();
						e.stopPropagation();
						openDocPreview(openBtn.getAttribute('data-doc-url'), openBtn.getAttribute('data-doc-name') || 'Document');
						return;
					}
					if(e.target.closest('button, a, input, label, select, textarea, form')) return;
					const row = e.target.closest('.doc-row.clickable');
					if(!row) return;
					const url = row.getAttribute('data-doc-url');
					const name = row.getAttribute('data-doc-name') || row.querySelector('.fw-semibold')?.textContent || 'Document';
					openDocPreview(url, name);
				});
				// Pick file buttons
				root.addEventListener('click', (e)=>{
					const btn = e.target.closest('.doc-pick-btn');
					if(!btn) return;
					e.preventDefault();
					e.stopPropagation();
					const form = btn.closest('form');
					const input = form?.querySelector('input[type="file"]');
					if(input) input.click();
				});
				// Auto-submit when a file is picked
				root.addEventListener('change', (e)=>{
					const input = e.target;
					if(!(input instanceof HTMLInputElement) || input.type !== 'file') return;
					const form = input.closest('form');
					if(!form) return;
					if(!form.classList.contains('upload-core-form') && !form.classList.contains('replace-extra-form') && form.id !== 'upload-photo-form') return;
					if(form.requestSubmit){
						form.requestSubmit(form.querySelector('[type="submit"]') || undefined);
					} else {
						form.submit();
					}
				});
				async function postForm(form, url){
					const fd = new FormData(form);
					const btn = form.querySelector('[type="submit"]');
					const prev = btn ? btn.innerHTML : '';
					if(btn){ btn.disabled = true; btn.textContent = 'Uploading...'; }
					try{
						const res = await fetch(url, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: fd});
						let d = {};
						const ct = (res.headers.get('content-type')||'').toLowerCase();
						if(ct.includes('application/json')){
							d = await res.json().catch(()=>({}));
						} else {
							// Often happens for 413/500 where the server returns HTML
							await res.text().catch(()=> '');
						}
						if(res.status === 413){
							throw new Error(d.message || 'Upload too large. Try a smaller file (or increase server upload limits).');
						}
						if(!res.ok || d.ok === false){
							const errs = d && d.errors ? Object.values(d.errors).flat().filter(Boolean) : [];
							const firstErr = errs[0] || null;
							const more = errs.length > 1 ? ` (and ${errs.length-1} more)` : '';
							throw new Error(d.message || (firstErr ? `${firstErr}${more}` : `Upload failed (HTTP ${res.status})`));
						}
						const isReplace = form.classList.contains('replace-extra-form') || form.classList.contains('upload-core-form') || form.id === 'upload-photo-form';
						const docName = fd.get('document_name');
						const field = fd.get('field');
						const msg = d.message
							|| (docName ? `Document "${docName}" added.` : null)
							|| (field ? `${String(field).replace(/_/g,' ')} ${isReplace ? 'updated' : 'uploaded'}.` : null)
							|| (isReplace ? 'Document updated.' : 'Document uploaded.');
						toast('success', msg);
						showPane('available');
						load('documents');
					}catch(err){
						toast('error', err.message || 'Upload failed');
					}finally{
						if(btn){ btn.disabled = false; btn.innerHTML = prev; }
					}
				}
				root.addEventListener('submit', (e)=>{
					const form = e.target;
					if(!(form instanceof HTMLFormElement)) return;
					if(form.id === 'upload-photo-form' || form.classList.contains('upload-core-form')){
						e.preventDefault();
						postForm(form, uploadUrl);
						return;
					}
					if(form.id === 'add-extra-doc-form'){
						e.preventDefault();
						postForm(form, addUrl);
						return;
					}
					if(form.classList.contains('replace-extra-form')){
						e.preventDefault();
						postForm(form, form.action);
						return;
					}
				});
				showPane('available');
			}
		});
	</script>

	<!-- Status modals -->
	<div class="modal fade" id="statusDischargeModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<form class="modal-content" method="POST" action="{{ route('admin.inmates.status.discharge', $inmate) }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-header"><h5 class="modal-title">Discharge Inmate</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
				<div class="modal-body">
					<div class="mb-2"><label class="form-label">Effective at (optional)</label><input type="datetime-local" name="effective_at" class="form-control"></div>
					<div class="mb-2"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
					<div class="mb-2"><label class="form-label">Attachments (optional)</label><input type="file" name="attachments[]" class="form-control" multiple></div>
					<div class="text-muted small">Discharged inmates become read-only.</div>
				</div>
				<div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Confirm Discharge</button></div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="statusDeceasedModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<form class="modal-content" method="POST" action="{{ route('admin.inmates.status.deceased', $inmate) }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-header"><h5 class="modal-title text-danger">Mark Inmate as Deceased</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
				<div class="modal-body">
					<div class="alert alert-warning small mb-2">This action permanently locks the inmate profile for edits.</div>
					<div class="mb-2"><label class="form-label">Effective at (optional)</label><input type="datetime-local" name="effective_at" class="form-control"></div>
					<div class="mb-2"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
					<div class="mb-2"><label class="form-label">Death Certificate (required)</label><input type="file" name="death_certificate" class="form-control" required></div>
					<div class="mb-2"><label class="form-label">Other Attachments (optional)</label><input type="file" name="attachments[]" class="form-control" multiple></div>
				</div>
				<div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" type="submit">Confirm Deceased</button></div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="statusRejoinModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<form class="modal-content" method="POST" action="{{ route('admin.inmates.status.rejoin', $inmate) }}" enctype="multipart/form-data">
				@csrf
				<div class="modal-header"><h5 class="modal-title">Re-Join Inmate</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
				<div class="modal-body">
					<div class="mb-2"><label class="form-label">Effective at (optional)</label><input type="datetime-local" name="effective_at" class="form-control"></div>
					<div class="mb-2"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
					<div class="mb-2"><label class="form-label">Attachments (optional)</label><input type="file" name="attachments[]" class="form-control" multiple></div>
				</div>
				<div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit">Confirm Re-Join</button></div>
			</form>
		</div>
	</div>
</x-app-layout>
