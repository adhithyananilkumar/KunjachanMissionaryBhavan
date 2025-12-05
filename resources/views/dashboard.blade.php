<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
       <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium">Registration Details</h3>
                        <a href="{{ route('registration.pdf.download') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Extra Documents</h3>
                    
                    <form action="{{ route('user.documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-6">
                        @csrf
                        <div class="flex items-center gap-4">
                            <input type="file" name="document" required class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            "/>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Upload
                            </button>
                        </div>
                        @error('document')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>

                    @if(auth()->user()->documents->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach(auth()->user()->documents as $doc)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <span class="text-gray-700">{{ $doc->file_name }}</span>
                                        <span class="ml-2 text-xs text-gray-500">({{ $doc->created_at->format('M d, Y') }})</span>
                                    </div>
                                    <a href="{{ route('user.documents.download', $doc) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Download</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">No extra documents uploaded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            {{ __("You're logged in!") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
