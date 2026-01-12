<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Lab Test: {{ $labTest->test_name }}</h2></x-slot>
	<div class="card mb-4"><div class="card-body">
		<dl class="row mb-0">
			<dt class="col-sm-3">Inmate</dt><dd class="col-sm-9">{{ $labTest->inmate->full_name }}</dd>
			<dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge text-bg-secondary">{{ ucfirst(str_replace('_',' ',$labTest->status)) }}</span></dd>
			<dt class="col-sm-3">Ordered</dt><dd class="col-sm-9">{{ $labTest->ordered_date ? $labTest->ordered_date->format('Y-m-d') : '—' }}</dd>
			<dt class="col-sm-3">Completed</dt><dd class="col-sm-9">{{ $labTest->completed_date ? $labTest->completed_date->format('Y-m-d') : '—' }}</dd>
			<dt class="col-sm-3">Reviewed</dt><dd class="col-sm-9">@if($labTest->reviewed_at) {{ $labTest->reviewed_at->format('Y-m-d H:i') }} by {{ $labTest->reviewedBy?->name }} @else — @endif</dd>
			@php $disk = Storage::disk(config('filesystems.default')); $url = $labTest->result_file_path ? (config('filesystems.default')==='s3' ? $disk->temporaryUrl($labTest->result_file_path, now()->addMinutes(5)) : $disk->url($labTest->result_file_path)) : null; @endphp
			<dt class="col-sm-3">Result File</dt><dd class="col-sm-9">@if($url)<a href="{{ $url }}" target="_blank">Download</a>@else — @endif</dd>
		</dl>
	</div></div>
	<div class="card"><div class="card-body">
		@if($labTest->reviewed_at)
			<div class="alert alert-info">This test was accepted by the doctor on {{ $labTest->reviewed_at->format('Y-m-d H:i') }} and can no longer be edited.</div>
		@else
		<form method="POST" action="{{ route('nurse.lab-tests.partial-update',$labTest) }}" enctype="multipart/form-data">
			@csrf
			@method('PATCH')
			<div class="row g-3">
				<div class="col-md-12">
					<label class="form-label">Result File (optional)</label>
					<input type="file" name="result_file" class="form-control @error('result_file') is-invalid @enderror">
					@error('result_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>
			</div>
			<div class="mb-3 mt-3">
				<label class="form-label">Result / Progress Notes (auto marks as completed)</label>
				<textarea name="result_notes" rows="4" class="form-control @error('result_notes') is-invalid @enderror">{{ old('result_notes',$labTest->result_notes) }}</textarea>
				@error('result_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>
			<div class="d-flex gap-2">
				<button class="btn btn-primary" type="submit">Update</button>
				<a href="{{ route('nurse.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
			</div>
		</form>
		@endif
		</div></div>
	@push('scripts')
	<script>
	// Auto-mark lab test ordered notification as read when visiting from notification list
	document.addEventListener('DOMContentLoaded', function(){
		const params = new URLSearchParams(window.location.search);
		const nid = params.get('nid');
		if(nid){
			fetch('/notifications/'+nid+'/mark-read', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
		}
	});
	</script>
	@endpush
</x-app-layout>
