<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Add Guardian</h2></x-slot>
    <div class="card"><div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.guardians.store') }}">@csrf
            <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" value="{{ old('full_name') }}" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input name="phone_number" class="form-control" value="{{ old('phone_number') }}"></div>
            <div class="mb-3"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address') }}"></div>
            <div class="mb-3"><label class="form-label">Link To Inmate</label>
                <select name="inmate_id" class="form-select" required>
                    <option value="">-- Select Inmate --</option>
                    @foreach($inmates as $inmate)
                        <option value="{{ $inmate->id }}" @selected(old('inmate_id')==$inmate->id)>{{ $inmate->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <hr>
            <h6 class="mb-3">Login Credentials</h6>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Save</button>
                <a href="{{ route('admin.guardians.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</x-app-layout>
