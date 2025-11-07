<x-app-layout>
    {{-- ===================== HEADER ===================== --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📝 Create New Task
            </h2>
            <a href="{{ route('tasks.daily') }}"
                class="bg-teal-500 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow hover:bg-teal-600 transition">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    {{-- ===================== CONTENT ===================== --}}
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-8">

                <h3 class="text-lg font-bold text-gray-700 mb-6">🧩 Task Information</h3>

                <form id="createTaskForm" method="POST" action="{{ route('tasks.store') }}">
                    @csrf

                    {{-- TITLE --}}
                    <div class="mb-5">
                        <x-input-label for="title" :value="__('Task Title')" />
                        <x-text-input id="title" name="title" type="text" required
                            placeholder="e.g., Backup daily server logs"
                            value="{{ old('title') }}"
                            class="mt-1 block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mb-5">
                        <x-input-label for="description" :value="__('Task Description')" />
                        <textarea id="description" name="description" rows="5" required
                            placeholder="Describe what this task is for..."
                            class="mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- FREQUENCY --}}
                    <div class="mb-5">
                        <x-input-label for="frequency" :value="__('Task Frequency')" />
                        <select id="frequency" name="frequency" required
                            class="mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                            <option value="" disabled {{ old('frequency') ? '' : 'selected' }}>Select Frequency</option>
                            <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                        <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
                    </div>

                    {{-- ACTIVE STATUS --}}
                    <div class="mb-6">
                        <x-input-label for="is_active" :value="__('Status')" />
                        <select id="is_active" name="is_active" required
                            class="mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                            <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Only active tasks will appear in the daily/monthly task list.</p>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="flex items-center justify-end gap-3">
                        <button type="reset"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Reset
                        </button>

                        <button id="submitTaskBtn" type="button"
                            class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow transition">
                            <i class="fas fa-save mr-1"></i> Create Task
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ===================== SCRIPTS ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ✅ SweetAlert session success
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // ❌ SweetAlert validation error (Laravel)
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please check your form fields and try again.',
                });
            @endif

            // ✅ Confirm before submitting
            const submitBtn = document.getElementById('submitTaskBtn');
            const form = document.getElementById('createTaskForm');

            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Create this task?',
                    text: "Please confirm before saving this task.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, create it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
