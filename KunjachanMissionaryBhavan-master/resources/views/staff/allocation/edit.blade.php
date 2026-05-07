<x-app-layout>
    <x-slot name="header"><h2 class="h5 mb-0">Allocation for {{ $inmate->full_name }}</h2></x-slot>
    <div class="card"><div class="card-body">
        @if(session('status'))<div class="alert alert-success py-2">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('staff.allocation.update',$inmate) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-12 col-md-8">
                <label class="form-label">Select Room/Bed</label>
                <select name="location_id" class="form-select">
                    <option value="">— Remove current allocation —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected($inmate->currentLocation?->location_id===$loc->id)>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end">
                <button class="btn btn-primary">Save Allocation</button>
            </div>
        </form>
        <div class="mt-3 small text-muted">Note: Staff can only assign/transfer. Creating or editing blocks/locations is restricted.</div>
    </div></div>
</x-app-layout>
