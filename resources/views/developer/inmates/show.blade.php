<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Inmate Profile</h2></x-slot>
	@if($inmate->critical_alert)
		<div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm mb-4">
			<span class="bi bi-exclamation-triangle-fill fs-4"></span>
			<div>
				<strong>Critical Alert:</strong>
				<div class="mt-1">{!! nl2br(e($inmate->critical_alert)) !!}</div>
			</div>
		</div>
	@endif
	<div class="row g-4 mb-4">
		<div class="col-md-4">
			<div class="card h-100">
				<div class="card-body text-center">
					@php $disk = Storage::disk(config('filesystems.default')); $photo = $inmate->photo_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($inmate->photo_path, now()->addMinutes(5)) : $disk->url($inmate->photo_path)) : 'https://via.placeholder.com/200x200?text=No+Photo'; @endphp
					<img src="{{ $photo }}" alt="Photo" class="rounded mb-3 img-fluid" style="max-height:220px;object-fit:cover;">
					<h4 class="mb-0">{{ $inmate->full_name }}</h4>
					<p class="text-muted mb-1">Reg #: {{ $inmate->registration_number ?: '—' }}</p>
					<p class="small mb-0">Institution: {{ $inmate->institution?->name ?: '—' }}</p>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="row g-4">
				<div class="col-md-6">
					<div class="card h-100">
						<div class="card-header">Personal Details</div>
						<div class="card-body">
							<dl class="row mb-0">
								<dt class="col-sm-5">Gender</dt><dd class="col-sm-7">{{ $inmate->gender ?: '—' }}</dd>
								<dt class="col-sm-5">DOB</dt><dd class="col-sm-7">{{ optional($inmate->date_of_birth)->format('Y-m-d') ?: '—' }}</dd>
								<dt class="col-sm-5">Admission</dt><dd class="col-sm-7">{{ optional($inmate->admission_date)->format('Y-m-d') ?: '—' }}</dd>
							</dl>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card h-100">
						<div class="card-header">Guardian Details</div>
						<div class="card-body">
							@if($inmate->guardian_first_name || $inmate->guardian_last_name)
								<dl class="row mb-0">
									<dt class="col-sm-5">Relation</dt><dd class="col-sm-7">{{ $inmate->guardian_relation ?: '—' }}</dd>
									<dt class="col-sm-5">Name</dt><dd class="col-sm-7">{{ trim($inmate->guardian_first_name.' '.$inmate->guardian_last_name) }}</dd>
									<dt class="col-sm-5">Email</dt><dd class="col-sm-7">{{ $inmate->guardian_email ?: '—' }}</dd>
									<dt class="col-sm-5">Phone</dt><dd class="col-sm-7">{{ $inmate->guardian_phone ?: '—' }}</dd>
									<dt class="col-sm-5">Address</dt><dd class="col-sm-7">{{ $inmate->guardian_address ?: '—' }}</dd>
								</dl>
							@else
								<p class="text-muted mb-0">No guardian details.</p>
							@endif
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="card h-100">
						<div class="card-header">Other Details</div>
						<div class="card-body">
							<dl class="row mb-0">
								<dt class="col-sm-3">Aadhaar #</dt><dd class="col-sm-9">{{ $inmate->aadhaar_number ?: '—' }}</dd>
								<dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $inmate->notes ?: '—' }}</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<a href="{{ route('developer.inmates.index') }}" class="btn btn-outline-secondary">Back to List</a>
</x-app-layout>
