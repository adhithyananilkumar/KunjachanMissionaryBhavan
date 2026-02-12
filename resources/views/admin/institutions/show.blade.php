<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">My Institution</h2>
    </x-slot>

    <div class="row g-4">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        @if($institution->logo)
                            <img src="{{ asset('storage/'.$institution->logo) }}" class="rounded-circle w-100 h-100 object-fit-cover">
                        @else
                            <i class="bi bi-building fs-1 text-secondary"></i>
                        @endif
                    </div>
                    <h5 class="card-title">{{ $institution->name }}</h5>
                    <p class="text-muted small">{{ $institution->address }}</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $institution->users()->count() }} Staff</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $institution->inmates()->count() }} Inmates</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Donation Settings -->
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-currency-rupee me-2"></i>Donate a Meal Pricing</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Set the donation amounts for different meal types. These values will be displayed on the public donation page.</p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.institutions.donations.update', $institution->id) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Breakfast Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" name="breakfast_amount" class="form-control" value="{{ old('breakfast_amount', $settings->breakfast_amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Lunch Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" name="lunch_amount" class="form-control" value="{{ old('lunch_amount', $settings->lunch_amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Dinner Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" name="dinner_amount" class="form-control" value="{{ old('dinner_amount', $settings->dinner_amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Other/Custom Default (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" name="other_amount" class="form-control" value="{{ old('other_amount', $settings->other_amount) }}" placeholder="Optional">
                                </div>
                                <div class="form-text small">Default amount for custom donations.</div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
