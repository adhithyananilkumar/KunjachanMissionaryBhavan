<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Institution') }}</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 text-gray-900">

					@if ($errors->any())
						<div class="mb-4 p-4 rounded bg-red-100 text-red-700">
							<ul class="list-disc ms-5 text-sm">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form method="POST" action="{{ route('developer.institutions.update', $institution->id) }}" class="space-y-6">
						@csrf
						@method('PUT')
						<div>
							<label class="block font-medium text-sm text-gray-700" for="name">Name <span class="text-red-500">*</span></label>
							<input id="name" name="name" type="text" value="{{ old('name', $institution->name) }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="address">Address <span class="text-red-500">*</span></label>
							<textarea id="address" name="address" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('address', $institution->address) }}</textarea>
							@error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="phone">Phone</label>
							<input id="phone" name="phone" type="text" value="{{ old('phone', $institution->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="email">Email</label>
							<input id="email" name="email" type="email" value="{{ old('email', $institution->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>

						<div class="border-t pt-6">
							<h3 class="font-semibold mb-2">Manage Enabled Features</h3>
							@php $features = old('features', $institution->enabled_features ?? []); @endphp
							<label class="inline-flex items-center gap-2 text-sm">
								<input type="checkbox" name="features[]" value="orphan_care" class="rounded" {{ in_array('orphan_care',$features) ? 'checked' : '' }}> Orphan Care Module
							</label>
							<label class="inline-flex items-center gap-2 text-sm mt-2">
								<input type="checkbox" name="features[]" value="elderly_care" class="rounded" {{ in_array('elderly_care',$features) ? 'checked' : '' }}> Elderly Care Module
							</label>
							<label class="inline-flex items-center gap-2 text-sm mt-2">
								<input type="checkbox" name="features[]" value="mental_health" class="rounded" {{ in_array('mental_health',$features) ? 'checked' : '' }}> Mental Health Module
							</label>
							<label class="inline-flex items-center gap-2 text-sm mt-2">
								<input type="checkbox" name="features[]" value="rehabilitation" class="rounded" {{ in_array('rehabilitation',$features) ? 'checked' : '' }}> Rehabilitation Module
							</label>
							<label class="inline-flex items-center gap-2 text-sm mt-2">
								<input type="checkbox" name="features[]" value="undefined_inmate" class="rounded" {{ in_array('undefined_inmate',$features) ? 'checked' : '' }}> Allow Undefined / Other Inmate Type
							</label>
						</div>
						<div class="border-top pt-6 mt-4">
							<h3 class="font-semibold mb-2">Doctor Assignment</h3>
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" role="switch" id="doctor_assignment_enabled" name="doctor_assignment_enabled" value="1" {{ old('doctor_assignment_enabled', $institution->doctor_assignment_enabled) ? 'checked' : '' }}>
								<label class="form-check-label" for="doctor_assignment_enabled">Enable doctor-patient assignment (restrict doctors to assigned inmates)</label>
							</div>
						</div>
						<div class="flex items-center gap-4">
							<x-primary-button>{{ __('Update') }}</x-primary-button>
							<a href="{{ route('developer.institutions.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
</x-app-layout>
