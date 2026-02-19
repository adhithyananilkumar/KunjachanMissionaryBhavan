<x-app-layout>
	<x-slot name="header">
		<div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
			<h2 class="h5 mb-0">Users</h2>
			<a href="{{ route('system_admin.users.create') }}" class="btn btn-primary btn-sm">New User</a>
		</div>
	</x-slot>
	<div class="d-lg-none d-flex justify-content-end mb-2">
		<button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#userFilters"><span class="bi bi-funnel me-1"></span>Filters</button>
	</div>
	<form method="GET" action="{{ route('system_admin.users.index') }}" class="card card-body mb-3 shadow-sm small collapse d-lg-block" id="userFilters">
		<div class="row g-2 align-items-end">
			<div class="col-md-3">
				<label class="form-label mb-0">Search</label>
				<input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="name or email">
			</div>
	    <div class="col-md-2">
				<label class="form-label mb-0">Role</label>
				<select name="role" class="form-select form-select-sm">
					<option value="">All</option>
		    <option value="system_admin" @selected(request('role')=='system_admin')>System Admin</option>
		    <option value="admin" @selected(request('role')=='admin')>Admin</option>
		    <option value="doctor" @selected(request('role')=='doctor')>Doctor</option>
		    <option value="nurse" @selected(request('role')=='nurse')>Nurse</option>
		    <option value="staff" @selected(request('role')=='staff')>Staff</option>
		    <option value="guardian" @selected(request('role')=='guardian')>Guardian</option>
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label mb-0">Institution</label>
				<select name="institution_id" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach($institutions as $inst)
						<option value="{{ $inst->id }}" @selected(request('institution_id')==$inst->id)>{{ $inst->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2">
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
				<a href="{{ route('system_admin.users.index') }}" class="btn btn-light btn-sm">Reset</a>
			</div>
		</div>
	</form>
	<style>
		.user-item{transition:background-color .15s ease, box-shadow .15s ease, transform .15s ease;}
		.user-item:hover{background:#f8f9fa; box-shadow:0 2px 6px rgba(0,0,0,0.08); transform:translateY(-2px);} 
		.user-avatar{width:52px;height:52px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.1);} 
	</style>
	<div id="usersResults">
		@include('system_admin.users._list', ['users' => $users])
	</div>
</x-app-layout>
<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form method="POST" id="deleteUserForm">
				@csrf
				@method('DELETE')
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title">Delete User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p class="mb-2 fw-semibold text-danger">Permanent deletion.</p>
					<p id="userDeleteWarning" class="small mb-0"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const modal = document.getElementById('deleteUserModal');
	modal.addEventListener('show.bs.modal', function(event){
		const button = event.relatedTarget;
		const userId = button.getAttribute('data-user-id');
		const userName = button.getAttribute('data-user-name');
		const form = document.getElementById('deleteUserForm');
		form.action = '{{ url('system-admin/users') }}/' + userId;
		document.getElementById('userDeleteWarning').innerText = `Delete user "${userName}"? This cannot be undone.`;
	});

	// No admin bug toggles here

	// Live filter + AJAX pagination
	const form = document.getElementById('userFilters');
	const results = document.getElementById('usersResults');
	if(form && results){
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
	}
});
</script>
