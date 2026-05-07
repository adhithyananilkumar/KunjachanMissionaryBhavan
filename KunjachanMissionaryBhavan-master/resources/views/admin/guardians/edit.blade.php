<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Edit Guardian</h2></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.guardians.update',$guardian) }}">@csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="{{ old('full_name',$guardian->full_name) }}" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input name="phone_number" class="form-control" value="{{ old('phone_number',$guardian->phone_number) }}"></div>
            <div class="mb-3"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address',$guardian->address) }}"></div>
            <div class="mb-3"><label class="form-label">Linked Inmate</label>
                <select name="inmate_id" class="form-select">
                    <option value="">-- Keep Current --</option>
                    @foreach($inmates as $inmate)
                        <option value="{{ $inmate->id }}" @selected(old('inmate_id',$guardian->inmate?->id)==$inmate->id)>{{ $inmate->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <h6 class="mb-3">User Account</h6>
            @php($user = $guardian->user)
            @if($user)
                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
            @else
                <div class="mb-2 small text-muted">No user account linked. Create one below.</div>
                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email') }}"></div>
            @endif
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Update</button>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</x-app-layout>
