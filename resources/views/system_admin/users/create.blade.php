<x-app-layout>
	<x-slot name="header">
		<h2 class="h4 font-weight-bold">Add User</h2>
	</x-slot>
	<div class="card my-4"><div class="card-body">
		<form action="{{ route('system_admin.users.store') }}" method="POST">@csrf
			<div class="mb-3"><label class="form-label">Role</label>
				<select class="form-select" id="role" name="role" required>
					@foreach($roles as $r)
						<option value="{{ $r }}">{{ ucfirst($r) }}</option>
					@endforeach
				</select>
			</div>
			<div class="mb-3"><label class="form-label">Institution</label>
				<select class="form-select" id="institution_id" name="institution_id">
					<option value="">-- None --</option>
					@foreach($institutions as $institution)
						<option value="{{ $institution->id }}">{{ $institution->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
			<div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
			<div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
			<div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" class="form-control" name="password_confirmation" required></div>
			<button class="btn btn-primary">Save User</button>
			<a href="{{ route('system_admin.users.index') }}" class="btn btn-secondary">Cancel</a>
		</form>
	</div></div>
	<script>
	 document.addEventListener('DOMContentLoaded', function(){
		const roleSelect=document.getElementById('role');
		const instSelect=document.getElementById('institution_id');
		function sync(){
			if(roleSelect.value==='system_admin' || roleSelect.value==='guardian'){
				instSelect.value=''; instSelect.setAttribute('disabled','disabled');
			}else{instSelect.removeAttribute('disabled');}
		}
		roleSelect.addEventListener('change',sync); sync();
	 });
	</script>
</x-app-layout>
