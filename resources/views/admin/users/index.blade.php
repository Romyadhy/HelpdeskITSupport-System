<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Managemen User
        </h2>
    </x-slot>

    <div class="py-12" x-data="userManagement()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex flex-col py-1.5">
                            <h3 class="text-lg font-medium pb-1">Daftar User</h3>
                            <p class="text-sm font-thin text-gray-500">Berikut adalah daftar user pada keseluruhan sistem</p>
                        </div>
                        <button @click="openCreateModal()" class="bg-teal-500 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                            Create New User
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if(!empty($user->getRoleNames()))
                                                @foreach($user->getRoleNames() as $v)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $v }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button @click="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->getRoleNames()->first() }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>

                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block delete-user-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="text-red-600 hover:text-red-900 delete-user-btn">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
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
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create New User</h3>
                            
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
                                <label for="create-name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" x-model="formData.name" id="create-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="create-email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" x-model="formData.email" id="create-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-4" x-data="{ show: false }">
                                <label for="create-password" class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="relative mt-1">
                                    <input :type="show ? 'text' : 'password'" x-model="formData.password" id="create-password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" required>
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4" x-data="{ show: false }">
                                <label for="create-password-confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <div class="relative mt-1">
                                    <input :type="show ? 'text' : 'password'" x-model="formData.password_confirmation" id="create-password-confirmation" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" required>
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="mb-4">
                                <label for="create-role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select x-model="formData.role" id="create-role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Create User
                            </button>
                            <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
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
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit User</h3>
                            
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
                                <label for="edit-name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" x-model="editingUser.name" id="edit-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="edit-email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" x-model="editingUser.email" id="edit-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-4" x-data="{ show: false }">
                                <label for="edit-password" class="block text-sm font-medium text-gray-700">Password</label>
                                <div class="relative mt-1">
                                    <input :type="show ? 'text' : 'password'" x-model="editingUser.password" id="edit-password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10" placeholder="Leave blank to keep current password">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4" x-data="{ show: false }">
                                <label for="edit-password-confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <div class="relative mt-1">
                                    <input :type="show ? 'text' : 'password'" x-model="editingUser.password_confirmation" id="edit-password-confirmation" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="mb-4">
                                <label for="edit-role" class="block text-sm font-medium text-gray-700">Role</label>
                                <select x-model="editingUser.role" id="edit-role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update User
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
        function userManagement() {
            return {
                showCreateModal: false,
                showEditModal: false,
                formData: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: ''
                },
                editingUser: {
                    id: null,
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role: ''
                },
                errors: {},

                openCreateModal() {
                    this.formData = {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role: ''
                    };
                    this.errors = {};
                    this.showCreateModal = true;
                },

                openEditModal(id, name, email, role) {
                    this.editingUser = {
                        id: id,
                        name: name,
                        email: email,
                        password: '',
                        password_confirmation: '',
                        role: role
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
                        const response = await fetch('{{ route("admin.users.store") }}', {
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
                                text: 'User created successfully.',
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
                        const response = await fetch(`/admin/users/${this.editingUser.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: this.editingUser.name,
                                email: this.editingUser.email,
                                password: this.editingUser.password,
                                password_confirmation: this.editingUser.password_confirmation,
                                role: this.editingUser.role
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'User updated successfully.',
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

            // Delete user confirmation
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
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
