<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily / Monthly Tasks</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="taskManagement()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                {{-- ================= CONTENT ================= --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2">
                            <h3 class="text-lg font-semibold text-gray-700">🗓️ Monthly Tasks</h3>
                            <p class="text-sm font-thin text-gray-400">Aktivitas yang dilakukan secara periodik untuk menjaga stabilitas dan keamanan sistem</p>
                        </div>
                        @can('create-task')
                            <button @click="openCreateModal()" 
                                class="inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                                <i class="fas fa-plus mr-2"></i> New Task
                            </button>
                        @endcan
                    </div>

                    {{-- ================= TABLE ================= --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">#</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                    @if (auth()->user()->hasRole('admin'))
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Activate</th>
                                    @endif
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($tasks as $index => $task)
                                    <tr class="hover:bg-gray-50 transition">
                                        {{-- Number --}}
                                        <td class="px-4 py-3 text-gray-700">{{ $index + 1 }}</td>

                                        {{-- Title --}}
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $task->title }}</td>

                                        {{-- Active Status (Admin Only) --}}
                                        @if (auth()->user()->hasRole('admin'))
                                            <td class="px-4 py-3">
                                                @if ($task->is_active)
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded-full">Inactive</span>
                                                @endif
                                            </td>
                                        @endif

                                        {{-- Completion Status --}}
                                        <td class="px-4 py-3">
                                            @if (in_array($task->id, $completedMonthlys))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-3">

                                                {{-- Detail --}}
                                                <button @click="openShowModal({{ $task->id }})" title="Detail"
                                                    class="text-gray-500 hover:text-indigo-600 transition">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                {{-- Edit --}}
                                                @can('edit-task')
                                                    <button @click="openEditModal({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->description) }}', '{{ $task->frequency }}', {{ $task->is_active ? 'true' : 'false' }})" 
                                                        title="Edit"
                                                        class="text-teal-600 hover:text-teal-800 transition">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endcan

                                                {{-- Delete --}}
                                                @can('delete-task')
                                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                        class="inline delete-task-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" title="Delete"
                                                            class="text-red-400 hover:text-red-600 transition delete-task-btn">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan

                                                {{-- Complete --}}
                                                @can('checked-task')
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline complete-task-form">
                                                        @csrf
                                                        <button type="button" title="Mark as Complete"
                                                            class="text-gray-500 hover:text-green-600 complete-task-btn">
                                                            @if (!in_array($task->id, $completedMonthlys))
                                                                <i class="far fa-circle"></i>
                                                            @else
                                                                <i class="fas fa-check-circle text-green-500"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endcan

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-500">
                                            No monthly tasks found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= CREATE TASK MODAL ================= --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitCreate()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Task</h3>
                                <button @click="closeModals()" type="button" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" x-model="createFormData.title" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                    <p x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title"></p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea x-model="createFormData.description" rows="4" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
                                    <p x-show="errors.description" class="mt-1 text-sm text-red-600" x-text="errors.description"></p>
                                </div>

                                <!-- Frequency -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Frequency</label>
                                    <select x-model="createFormData.frequency" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <p x-show="errors.frequency" class="mt-1 text-sm text-red-600" x-text="errors.frequency"></p>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select x-model="createFormData.is_active" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                        <option :value="true">Active</option>
                                        <option :value="false">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Creating...' : 'Create Task'"></span>
                            </button>
                            <button type="button" @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= EDIT TASK MODAL ================= --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitEdit()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Task</h3>
                                <button @click="closeModals()" type="button" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" x-model="editFormData.title" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                    <p x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title"></p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea x-model="editFormData.description" rows="4" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
                                    <p x-show="errors.description" class="mt-1 text-sm text-red-600" x-text="errors.description"></p>
                                </div>

                                <!-- Frequency -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Frequency</label>
                                    <select x-model="editFormData.frequency" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                        <option value="daily">Daily</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <p x-show="errors.frequency" class="mt-1 text-sm text-red-600" x-text="errors.frequency"></p>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select x-model="editFormData.is_active" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                                        <option :value="true">Active</option>
                                        <option :value="false">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Updating...' : 'Update Task'"></span>
                            </button>
                            <button type="button" @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= SHOW TASK MODAL ================= --}}
        <div x-show="showShowModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showShowModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showShowModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Task Details</h3>
                            <button @click="closeModals()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div x-show="loading" class="text-center py-8">
                            <i class="fas fa-circle-notch fa-spin text-4xl text-emerald-500"></i>
                            <p class="mt-2 text-gray-600">Loading task details...</p>
                        </div>

                        <!-- Task Details Display -->
                        <div x-show="!loading" class="space-y-6">
                            <!-- Header with Title & Status -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-4 border-b">
                                <h4 class="text-3xl font-extrabold text-emerald-600 uppercase" x-text="showData.title"></h4>
                                <span class="mt-2 md:mt-0 px-4 py-1.5 text-sm font-semibold rounded-full shadow-sm"
                                    :class="showData.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800'"
                                    x-text="showData.is_active ? 'Active' : 'Inactive'"></span>
                            </div>

                            <!-- Description -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h5 class="text-lg font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-file-alt text-emerald-500 mr-2"></i>Deskripsi Task:
                                </h5>
                                <p class="text-gray-700 leading-relaxed" x-text="showData.description"></p>
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-700">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-sync-alt text-emerald-500"></i>
                                    <span><strong>Frequency:</strong> <span x-text="showData.frequency"></span></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar-check text-emerald-500"></i>
                                    <span><strong>Created At:</strong> <span x-text="showData.created_at"></span></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-tasks text-emerald-500"></i>
                                    <span><strong>Completed This Month:</strong> <span x-text="showData.completed_count_this_month"></span>x</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-power-off text-emerald-500"></i>
                                    <span><strong>Status:</strong> <span x-text="showData.is_active ? 'Active' : 'Inactive'"></span></span>
                                </div>
                            </div>

                            <!-- Completion History Table -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                                <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-history text-emerald-500 mr-2"></i> Riwayat Penyelesaian
                                </h5>
                                <template x-if="showData.completions && showData.completions.length > 0">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama User</th>
                                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Selesai</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <template x-for="completion in showData.completions" :key="completion.number">
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-2 text-sm text-gray-700" x-text="completion.number"></td>
                                                        <td class="px-4 py-2 text-sm text-gray-800 font-medium" x-text="completion.user_name"></td>
                                                        <td class="px-4 py-2 text-sm text-gray-700" x-text="completion.completed_at"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                <template x-if="!showData.completions || showData.completions.length === 0">
                                    <p class="text-gray-500 italic">Belum ada riwayat penyelesaian untuk task ini.</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="closeModals()"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= ALPINE.JS COMPONENT ================= --}}
    <script>
        // Define taskManagement in global scope for Alpine.js
        window.taskManagement = function() {
            return {
                // Modal states
                showCreateModal: false,
                showEditModal: false,
                showShowModal: false,

                // Form data
                createFormData: {
                    title: '',
                    description: '',
                    frequency: 'monthly', // Default for monthly page
                    is_active: true
                },

                editFormData: {
                    id: null,
                    title: '',
                    description: '',
                    frequency: '',
                    is_active: true
                },

                showData: {},
                errors: {},
                loading: false,
                isSubmitting: false,

                // Methods
                openCreateModal() {
                    this.createFormData = {
                        title: '',
                        description: '',
                        frequency: 'monthly',
                        is_active: true
                    };
                    this.errors = {};
                    this.showCreateModal = true;
                },

                openEditModal(id, title, description, frequency, isActive) {
                    this.editFormData = {
                        id: id,
                        title: title,
                        description: description,
                        frequency: frequency,
                        is_active: isActive
                    };
                    this.errors = {};
                    this.showEditModal = true;
                },

                async openShowModal(taskId) {
                    this.loading = true;
                    this.showShowModal = true;

                    try {
                        const response = await fetch(`/tasks/${taskId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Error response:', errorText);
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const task = await response.json();
                        
                        // Store task data for display
                        this.showData = task;
                    } catch (error) {
                        console.error('Error fetching task:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load task details.'
                        });
                        this.showShowModal = false;
                    } finally {
                        this.loading = false;
                    }
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showShowModal = false;
                    this.errors = {};
                },

                async submitCreate() {
                    // Show confirmation dialog
                    const result = await Swal.fire({
                        title: 'Create Task?',
                        text: 'Are you sure you want to create this task?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#14b8a6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, create it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (!result.isConfirmed) {
                        return; // User cancelled
                    }

                    this.errors = {};
                    this.isSubmitting = true; // Start loading

                    try {
                        const response = await fetch('{{ route('tasks.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.createFormData)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Task created successfully.',
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
                    } finally {
                        this.isSubmitting = false; // Stop loading
                    }
                },

                async submitEdit() {
                    // Show confirmation dialog
                    const result = await Swal.fire({
                        title: 'Update Task?',
                        text: 'Are you sure you want to update this task?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#14b8a6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (!result.isConfirmed) {
                        return; // User cancelled
                    }

                    this.errors = {};
                    this.isSubmitting = true; // Start loading

                    try {
                        const response = await fetch(`/tasks/${this.editFormData.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title: this.editFormData.title,
                                description: this.editFormData.description,
                                frequency: this.editFormData.frequency,
                                is_active: this.editFormData.is_active
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Task updated successfully.',
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
                    } finally {
                        this.isSubmitting = false; // Stop loading
                    }
                }
            }
        }
    </script>

    {{-- ================= SWEETALERT SCRIPTS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ Success Alert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // ❌ Error Alert
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                });
            @endif

            // 🟩 Confirm: Mark Complete
            document.querySelectorAll('.complete-task-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Tandai sebagai selesai?',
                        text: "Aksi ini tidak bisa dikembalikan lagi.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Selesai'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // 🗑️ Confirm: Delete Task
            document.querySelectorAll('.delete-task-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin Hapus Task Ini?',
                        text: "Aksi ini tidak bisa dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>
