<x-app-layout>
	<x-slot name="header">
		<h2 class="h4 font-weight-bold">Edit User</h2>
	</x-slot>
	<div class="card my-4"><div class="card-body">
			<form action="{{ route('system_admin.users.update',$user) }}" method="POST">@csrf @method('PUT')
				<div class="mb-3"><label class="form-label">Role</label><select name="role" id="role" class="form-select" required>
					@foreach($roles as $r)<option value="{{ $r }}" @selected($user->role===$r)>{{ ucfirst($r) }}</option>@endforeach
				</select></div>
				<div class="mb-3"><label class="form-label">Institution (optional)</label><select name="institution_id" id="institution_id" class="form-select">
					<option value="">-- None --</option>
					@foreach($institutions as $inst)<option value="{{ $inst->id }}" @selected($user->institution_id==$inst->id)>{{ $inst->name }}</option>@endforeach
				</select></div>
				<div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name',$user->name) }}" required></div>
				<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
			<hr><p class="small text-muted">Leave password fields blank to keep the current password.</p>
			<div class="row g-3">
				<div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
				<div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
			</div>
			<div class="d-flex gap-2 mt-3"><button class="btn btn-primary">Save Changes</button><a href="{{ route('system_admin.users.index') }}" class="btn btn-secondary">Cancel</a></div>
		</form>
	</div></div>
	<script>
	 document.addEventListener('DOMContentLoaded', function(){
		const roleSelect=document.getElementById('role'); const instSelect=document.getElementById('institution_id');
		function sync(){ if(roleSelect.value==='system_admin' || roleSelect.value==='guardian'){ instSelect.value=''; instSelect.setAttribute('disabled','disabled'); } else { instSelect.removeAttribute('disabled'); } }
		roleSelect.addEventListener('change',sync); sync();
	 });
	</script>
</x-app-layout>
