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

	<div class="card shadow-sm mb-3">
		<div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
			<div class="d-flex align-items-center gap-3">
				<img src="{{ $inmate->avatar_url }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;" alt="avatar">
				<div>
					<div class="h5 mb-1">{{ $inmate->full_name }}</div>
					<div class="text-muted small">Admission #: <strong>{{ $inmate->admission_number }}</strong> · Reg #: {{ $inmate->registration_number ?: '—' }}</div>
					<div class="text-muted small">Institution: {{ $inmate->institution?->name ?: '—' }} · Type: {{ ucfirst(str_replace('_',' ',$inmate->type)) ?: '—' }}</div>
					<div class="small mt-1">
						<span class="text-muted">Allocation:</span>
						<span class="fw-semibold">{{ optional($inmate->currentLocation?->location)->name ?? 'Not assigned' }}</span>
					</div>
				</div>
			</div>
			<div class="d-flex flex-wrap gap-2">
				<a href="{{ route('system_admin.inmates.report',$inmate) }}" class="btn btn-outline-success btn-sm"><span class="bi bi-file-earmark-pdf me-1"></span>Download Report</a>
				<a href="{{ route('system_admin.inmates.edit',$inmate) }}" class="btn btn-outline-primary btn-sm"><span class="bi bi-pencil-square me-1"></span>Edit</a>
				<a href="{{ route('system_admin.inmates.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
			</div>
		</div>
	</div>

		<ul class="nav nav-tabs small" id="inmateTabs" role="tablist">
		<li class="nav-item"><button class="nav-link active" data-tab="overview" type="button">Overview</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="medical" type="button">Medical</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="payments" type="button">Payments</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="history" type="button">History</button></li>
		<li class="nav-item"><button class="nav-link" data-tab="documents" type="button">Documents</button></li>
			<li class="nav-item"><button class="nav-link" data-tab="allocation" type="button">Allocation</button></li>
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
			function load(tab, url){
				active = tab;
				tabs.forEach(b=>b.classList.toggle('active', b.getAttribute('data-tab')===tab));
				container.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</div>';
				const urlMap = {
					overview: '{{ route('system_admin.inmates.show',$inmate) }}?partial=overview',
					medical: '{{ route('system_admin.inmates.show',$inmate) }}?partial=medical',
					history: '{{ route('system_admin.inmates.show',$inmate) }}?partial=history',
					documents: '{{ route('system_admin.inmates.show',$inmate) }}?partial=documents',
					allocation: '{{ route('system_admin.inmates.show',$inmate) }}?partial=allocation',
					payments: '{{ route('system_admin.inmates.show',$inmate) }}?partial=payments',
					settings: '{{ route('system_admin.inmates.show',$inmate) }}?partial=settings',
				};
				const finalUrl = url || urlMap[tab];
				fetch(finalUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}})
					.then(r=>r.text()).then(html=>{
						container.innerHTML = html;
						// Post-load initializers for known tabs
						if(tab==='allocation'){ initAllocationTab(); }
						if(tab==='documents'){ initDocumentsTab(); }
					});
				if(location.hash !== '#'+tab) location.hash = '#'+tab;
			}
			tabs.forEach(b=>b.addEventListener('click',()=>load(b.getAttribute('data-tab'))));
			// Intercept links within container for in-tab navigation
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
			// Delegated submit for settings (basic details) if any section uses AJAX
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
			const allowed=['overview','medical','history','documents','allocation','payments','settings'];
			load(allowed.includes(initial)?initial:'overview');
			window.addEventListener('hashchange',()=>{ const h=(location.hash||'').replace('#',''); if(h && h!==active) load(h); });

			// Allocation tab initializer moved here so partial stays clean and JS never renders as text
			function initAllocationTab(){
				const form = document.getElementById('allocAssignForm'); if(!form || form.dataset.bound==='1') return; form.dataset.bound='1';
				const list = document.getElementById('allocRoomsList');
				const q = document.getElementById('allocRoomSearch');
				const reloadBtn = document.getElementById('allocReload');
				const showOcc = document.getElementById('allocShowOcc');
				const hid = document.getElementById('allocLocationId');
				const out = document.getElementById('allocSelected');
				const inst = form.getAttribute('data-inst') || '{{ (int)$inmate->institution_id }}';
				let rooms = [];
				async function loadRooms(){
					try{
						const res = await fetch(`{{ url('system-admin/allocation/api/institutions') }}/${inst}/locations?show_occupied=${showOcc?.checked?1:0}`, {headers:{'Accept':'application/json'}});
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
						const occBadge = r.occupant? `<span class=\"badge bg-secondary ms-2\">${r.occupant}</span>`:'';
						const isMaint = (r.status==='maintenance'); const occupied = !!r.occupied; const label = isMaint ? 'Maintenance' : (occupied ? 'Occupied' : 'Available'); const badgeClass = isMaint ? 'bg-warning text-dark' : (occupied ? 'bg-secondary' : 'bg-success');
						btn.innerHTML = `<span><span class=\"bi bi-door-closed me-2\"></span>${r.name}${occBadge}</span>` +
							`<span class=\"badge ${badgeClass}\">${label}</span>`;
						btn.disabled = isMaint || (occupied && !(showOcc?.checked));
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

			// Documents tab initializer: subtabs, clickable rows, AJAX uploads
			function initDocumentsTab(){
				const root = container.querySelector('#docSubTabContent');
				if(!root || root.dataset.bound==='1') return; root.dataset.bound='1';
				const uploadUrl = root.getAttribute('data-upload-url');
				const addUrl = root.getAttribute('data-add-url');
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
						const res = await fetch(url, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: fd});
						const d = await res.json().catch(()=>({}));
						if(!res.ok || !d.ok){ throw new Error(d.message || 'Upload failed'); }
						// Reload just the documents tab
						showPane('available');
						load('documents');
					}catch(err){
						const alert = document.createElement('div'); alert.className='alert alert-danger small mt-2'; alert.textContent = err.message || 'Upload failed'; form.appendChild(alert); setTimeout(()=>alert.remove(),3000);
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
				// default pane stays 'available'; ensure transition classes are applied already
				showPane('available');
			}
		});
	</script>
</x-app-layout>
