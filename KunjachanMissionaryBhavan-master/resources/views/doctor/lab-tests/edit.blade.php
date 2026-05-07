<x-app-layout>
	<x-slot name="header"><h2 class="h5 mb-0">Lab Test: {{ $labTest->test_name }}</h2></x-slot>
	<div class="alert alert-info m-3">
		This page has moved. Redirecting to the details view...
		<a class="ms-2" href="{{ route('doctor.lab-tests.show',$labTest) }}">Open details</a>
	</div>
	@push('scripts')
	<script>setTimeout(()=>{ window.location.replace(@json(route('doctor.lab-tests.show',$labTest))); }, 1000);</script>
	@endpush
</x-app-layout>
