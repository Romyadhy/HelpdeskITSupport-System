<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket Locations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Locations List</h3>
                        <a href="{{ route('admin.locations.create') }}" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                            Create New Location
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($locations as $location)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $location->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($location->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.locations.edit', $location->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            
                                            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modern Pagination with Info -->
                    <div
                        class="px-6 py-5 border-t bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                        <!-- Left: Showing Info -->
                        <div class="text-sm text-gray-600">
                            Showing
                            <span class="font-semibold text-gray-900">{{ $locations->firstItem() }}</span>
                            to
                            <span class="font-semibold text-gray-900">{{ $locations->lastItem() }}</span>
                            of
                            <span class="font-semibold text-gray-900">{{ $locations->total() }}</span>
                            results
                        </div>

                        <!-- Right: Pagination -->
                        <div class="flex items-center space-x-1">

                            {{-- Previous --}}
                            @if ($locations->onFirstPage())
                                <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $locations->previousPageUrl() }}"
                                    class="px-3 py-2 rounded-xl bg-white border border-gray-300 
                      text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($locations->links()->elements[0] as $page => $url)
                                @if ($page == $locations->currentPage())
                                    <span class="px-4 py-2 rounded-xl bg-teal-500 text-white font-semibold shadow">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="px-4 py-2 rounded-xl bg-white border border-gray-300
                          text-gray-700 hover:bg-gray-100 transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($locations->hasMorePages())
                                <a href="{{ $locations->nextPageUrl() }}"
                                    class="px-3 py-2 rounded-xl bg-white border border-gray-300 
                      text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
