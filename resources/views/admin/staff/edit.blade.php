<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Edit Staff Member</h2></x-slot>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.staff.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        @foreach(['doctor','nurse','staff','admin'] as $r)
                            <option value="{{ $r }}" @selected(old('role',$user->role)==$r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
