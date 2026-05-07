<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-heart-pulse"></i> Donation Details
            </h2>
            <a href="{{ route('system_admin.donation-requests.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Request Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold">Amount</label>
                            <div class="h3 text-primary mb-0">₹{{ number_format($donationRequest->amount) }}</div>
                            <div class="text-muted small">{{ $donationRequest->details['meal_type'] ?? 'Custom' }}</div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <label class="text-muted small text-uppercase fw-bold">Date</label>
                            <div class="fw-medium">{{ $donationRequest->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $donationRequest->created_at->format('h:i A') }}</div>
                        </div>
                        <div class="col-12"><hr class="my-2"></div>
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold">Donor Name</label>
                            <div class="fw-medium">{{ $donationRequest->donor_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-muted small text-uppercase fw-bold">Contact Info</label>
                            <div><i class="bi bi-envelope me-1 text-muted"></i> {{ $donationRequest->donor_email ?: 'N/A' }}</div>
                            <div><i class="bi bi-telephone me-1 text-muted"></i> {{ $donationRequest->donor_phone ?: 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small text-uppercase fw-bold">Message</label>
                            <div class="bg-light p-3 rounded text-secondary">{{ $donationRequest->message ?: 'No message provided.' }}</div>
                        </div>
                        <div class="col-12">
                             <label class="text-muted small text-uppercase fw-bold">Internal Status</label>
                             <form action="{{ route('system_admin.donation-requests.update', $donationRequest) }}" method="POST" class="d-flex align-items-center gap-2 mt-1">
                                @csrf @method('PUT')
                                <select name="status" class="form-select form-select-sm w-auto">
                                    <option value="pending" @selected($donationRequest->status=='pending')>Pending</option>
                                    <option value="contacted" @selected($donationRequest->status=='contacted')>Contacted</option>
                                    <option value="completed" @selected($donationRequest->status=='completed')>Completed</option>
                                    <option value="cancelled" @selected($donationRequest->status=='cancelled')>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                             </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
             <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="card-title">Institution</h6>
                    @if($donationRequest->institution)
                        <div class="d-flex align-items-center gap-3">
                            @if($donationRequest->institution->logo)
                            <img src="{{ asset('storage/'.$donationRequest->institution->logo) }}" class="rounded-circle" width="40" height="40">
                            @endif
                            <div>
                                <div class="fw-bold">{{ $donationRequest->institution->name }}</div>
                                <div class="small text-muted">{{ $donationRequest->institution->address }}</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('system_admin.institutions.show', $donationRequest->institution) }}" class="btn btn-outline-secondary btn-sm w-100">View Institution</a>
                        </div>
                    @else
                        <div class="text-muted small">Not requested for a specific institution.</div>
                    @endif
                </div>
            </div>
            
             <div class="d-grid gap-2">
                 @if($donationRequest->donor_email)
                 <a href="mailto:{{ $donationRequest->donor_email }}?subject=Donation Request - Kunjachan Missionary Bhavan" class="btn btn-outline-primary"><i class="bi bi-envelope me-2"></i>Email Donor</a>
                 @endif
                 @if($donationRequest->donor_phone)
                 <a href="tel:{{ $donationRequest->donor_phone }}" class="btn btn-outline-success"><i class="bi bi-telephone me-2"></i>Call Donor</a>
                 @endif
             </div>
        </div>
    </div>
</x-app-layout>
