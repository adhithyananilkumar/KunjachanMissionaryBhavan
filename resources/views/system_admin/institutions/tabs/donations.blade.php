<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3">Donation Settings</h5>
        <form method="POST" action="{{ route('system_admin.institutions.donations.update', $institution->id) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Breakfast Amount (₹)</label>
                    <input type="number" step="0.01" name="breakfast_amount" class="form-control" value="{{ old('breakfast_amount', $settings->breakfast_amount) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lunch Amount (₹)</label>
                    <input type="number" step="0.01" name="lunch_amount" class="form-control" value="{{ old('lunch_amount', $settings->lunch_amount) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dinner Amount (₹)</label>
                    <input type="number" step="0.01" name="dinner_amount" class="form-control" value="{{ old('dinner_amount', $settings->dinner_amount) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Other Amount (₹) <span class="text-muted small">(Optional default)</span></label>
                    <input type="number" step="0.01" name="other_amount" class="form-control" value="{{ old('other_amount', $settings->other_amount) }}">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>
