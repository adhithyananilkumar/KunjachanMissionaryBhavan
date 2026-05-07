@php($standalone = $standalone ?? false)
@if($standalone)
<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Edit Guardian</h2></x-slot>
    <div class="card"><div class="card-body">
@endif
        <form method="POST" action="{{ route('system_admin.guardians.update',$guardian) }}">@csrf @method('PUT')
            <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="{{ old('full_name',$guardian->full_name) }}" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input name="phone_number" class="form-control" value="{{ old('phone_number',$guardian->phone_number) }}"></div>
            <div class="mb-3"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address',$guardian->address) }}"></div>
                <div class="mb-3"><label class="form-label">Linked Inmate <span class="text-muted small">(optional)</span></label>
                    <select name="inmate_id" class="form-select">
                        <option value="">-- Keep Current --</option>
                        @foreach(($inmates ?? []) as $inmate)
                            <option value="{{ $inmate->id }}" @selected(old('inmate_id',$guardian->inmate?->id)==$inmate->id)>
                                {{ $inmate->full_name ?? ($inmate->first_name.' '.$inmate->last_name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            <hr>
            <h6 class="mb-3">User Account</h6>
            @php($user = $guardian->user)
            @if($user)
                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
                <div class="alert alert-secondary py-2 small">Leave password fields blank to keep existing password.</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                </div>
            @else
                <div class="mb-2 small text-muted">No user account linked. Create one below.</div>
                <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{ old('email') }}"></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                </div>
            @endif
            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary" type="submit">Update</button>
                @if($standalone)
                    <a href="{{ route('system_admin.guardians.index') }}" class="btn btn-secondary">Cancel</a>
                @else
                    <button type="button" class="btn btn-light" onclick="document.getElementById('profile-edit').classList.add('d-none');document.getElementById('profile-view').classList.remove('d-none');">Cancel</button>
                @endif
            </div>
        </form>
@if($standalone)
    </div></div>
</x-app-layout>
@endif
