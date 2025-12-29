<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Managemen Lokasi Ticket
        </h2>
    </x-slot>

    <div class="py-12" x-data="locationManagement()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex flex-col py-1.5">
                            <h3 class="text-lg font-medium pb-1">Daftar Lokasi</h3>
                            <p class="font-thin text-sm text-gray-500">Berikut adalah daftar lokasi pada ticket/pelaporan maslah</p>
                        </div>
                        <button @click="openCreateModal()" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                            Tambahkan Lokasi Baru
                        </button>
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
                                            <button @click="openEditModal({{ $location->id }}, '{{ $location->name }}', {{ $location->is_active ? 'true' : 'false' }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>

                                            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="inline-block delete-location-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-900 delete-location-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modern Pagination with Info -->
                    <div class="px-6 py-5 border-t bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">

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

        <!-- Create Location Modal -->
        <div x-show="showCreateModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitCreate()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create New Location</h3>

                            <!-- Error Display -->
                            <div x-show="Object.keys(errors).length > 0" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    <template x-for="(error, field) in errors" :key="field">
                                        <li x-text="error[0]"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="create-name" class="block text-sm font-medium text-gray-700">Location Name</label>
                                <input type="text" x-model="formData.name" id="create-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Active Status -->
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" x-model="formData.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Create Location
                            </button>
                            <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Location Modal -->
        <div x-show="showEditModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitEdit()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Location</h3>

                            <!-- Error Display -->
                            <div x-show="Object.keys(errors).length > 0" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    <template x-for="(error, field) in errors" :key="field">
                                        <li x-text="error[0]"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="edit-name" class="block text-sm font-medium text-gray-700">Location Name</label>
                                <input type="text" x-model="editingLocation.name" id="edit-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Active Status -->
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" x-model="editingLocation.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update Location
                            </button>
                            <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 & Alpine.js Scripts -->
    <script>
        function locationManagement() {
            return {
                showCreateModal: false,
                showEditModal: false,
                formData: {
                    name: '',
                    is_active: true
                },
                editingLocation: {
                    id: null,
                    name: '',
                    is_active: true
                },
                errors: {},

                openCreateModal() {
                    this.formData = {
                        name: '',
                        is_active: true
                    };
                    this.errors = {};
                    this.showCreateModal = true;
                },

                openEditModal(id, name, isActive) {
                    this.editingLocation = {
                        id: id,
                        name: name,
                        is_active: isActive
                    };
                    this.errors = {};
                    this.showEditModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.errors = {};
                },

                async submitCreate() {
                    this.errors = {};

                    try {
                        const response = await fetch('{{ route("admin.locations.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Location created successfully.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'An error occurred.'
                                });
                            }
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    }
                },

                async submitEdit() {
                    this.errors = {};

                    try {
                        const response = await fetch(`/admin/locations/${this.editingLocation.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: this.editingLocation.name,
                                is_active: this.editingLocation.is_active
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Location updated successfully.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'An error occurred.'
                                });
                            }
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    }
                }
            }
        }

        // Delete confirmation with SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            // Success/Error messages from session
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}'
                });
            @endif

            // Delete location confirmation
            document.querySelectorAll('.delete-location-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
