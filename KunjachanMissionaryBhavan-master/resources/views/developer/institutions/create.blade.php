<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create Institution') }}</h2>
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

					<form method="POST" action="{{ route('developer.institutions.store') }}" class="space-y-6">
						@csrf
						<div>
							<label class="block font-medium text-sm text-gray-700" for="name">Name <span class="text-red-500">*</span></label>
							<input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="address">Address <span class="text-red-500">*</span></label>
							<textarea id="address" name="address" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('address') }}</textarea>
							@error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="phone">Phone</label>
							<input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div>
							<label class="block font-medium text-sm text-gray-700" for="email">Email</label>
							<input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
							@error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
						</div>
						<div class="flex items-center gap-4">
							<x-primary-button>{{ __('Save') }}</x-primary-button>
							<a href="{{ route('developer.institutions.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancel') }}</a>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
</x-app-layout>
