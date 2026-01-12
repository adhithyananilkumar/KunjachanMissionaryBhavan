<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Order Lab Test for {{ $inmate->full_name }}</h2></x-slot>
	<div class="card"><div class="card-body">
		<form method="POST" action="{{ route('doctor.lab-tests.store', $inmate) }}" enctype="multipart/form-data">
			@csrf
			<div class="mb-3">
				<label class="form-label">Test Name <span class="text-danger">*</span></label>
				<input type="text" name="test_name" class="form-control @error('test_name') is-invalid @enderror" value="{{ old('test_name') }}" required>
				@error('test_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>
			<div class="mb-3">
				<label class="form-label">Clinical Notes</label>
				<textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
				@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>
			<div class="d-flex gap-2">
				<button class="btn btn-primary" type="submit">Order Test</button>
				<a href="{{ route('doctor.inmates.show',$inmate) }}" class="btn btn-outline-secondary">Cancel</a>
			</div>
		</form>
	</div></div>
</x-app-layout>
