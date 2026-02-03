<div class="row g-4 fade-in-up">
    <!-- Settings Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Donation Confirmation Amounts</h5>
                        <small class="text-muted">Set standard amounts for meal sponsorships</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('system_admin.institutions.donations.update', $institution->id) }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Breakfast (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-coffee text-muted"></i></span>
                                <input type="number" step="0.01" name="breakfast_amount" class="form-control border-start-0 ps-0" value="{{ old('breakfast_amount', $settings->breakfast_amount) }}" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Lunch (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-utensils text-muted"></i></span>
                                <input type="number" step="0.01" name="lunch_amount" class="form-control border-start-0 ps-0" value="{{ old('lunch_amount', $settings->lunch_amount) }}" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Dinner (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-moon text-muted"></i></span>
                                <input type="number" step="0.01" name="dinner_amount" class="form-control border-start-0 ps-0" value="{{ old('dinner_amount', $settings->dinner_amount) }}" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label text-muted small text-uppercase fw-bold">Other (₹) <small>(Default)</small></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-donate text-muted"></i></span>
                                <input type="number" step="0.01" name="other_amount" class="form-control border-start-0 ps-0" value="{{ old('other_amount', $settings->other_amount) }}" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Donor Contact Details Section -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Donor Contact Details</h5>
                        <small class="text-muted">Recent donations and donor information</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 text-uppercase small text-muted fw-bold">Date</th>
                                <th class="text-uppercase small text-muted fw-bold">Donor Name</th>
                                <th class="text-uppercase small text-muted fw-bold">Contact Info</th>
                                <th class="text-uppercase small text-muted fw-bold">Amount</th>
                                <th class="text-uppercase small text-muted fw-bold">Message</th>
                                <th class="text-end pe-4 text-uppercase small text-muted fw-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                                <tr>
                                    <td class="ps-4 text-nowrap">
                                        <div class="fw-medium">{{ $donation->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $donation->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $donation->donor_name }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($donation->donor_email)
                                                <small class="mb-1"><i class="fas fa-envelope text-muted me-2" style="width:16px"></i> {{ $donation->donor_email }}</small>
                                            @endif
                                            @if($donation->donor_phone)
                                                <small><i class="fas fa-phone text-muted me-2" style="width:16px"></i> {{ $donation->donor_phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-bold fs-6">₹{{ number_format($donation->amount, 2) }}</span>
                                    </td>
                                    <td style="max-width: 250px;">
                                        @if($donation->message)
                                            <span class="text-muted small text-truncate d-block" title="{{ $donation->message }}">{{ $donation->message }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($donation->status == 'completed')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3">Completed</span>
                                        @elseif($donation->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Pending</span>
                                        @elseif($donation->status == 'failed')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Failed</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ ucfirst($donation->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No donations found for this institution.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($donations->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

