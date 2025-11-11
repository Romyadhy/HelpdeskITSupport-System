    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Task
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">✏️ Edit Task</h2>

        {{-- ================= FORM ================= --}}
        <form id="editTaskForm" action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}"
                    class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2" required
                    placeholder="Enter task title...">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2" required
                    placeholder="Enter task description...">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Frequency --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Frequency</label>
                <select name="frequency" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2"
                    required>
                    <option value="daily" {{ $task->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $task->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>

            {{-- Activated --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Activated</label>
                <select name="is_active" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2"
                    required>
                    <option value="1" {{ $task->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$task->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('tasks.show', $task->id) }}"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="button" id="updateBtn"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    Update Task
                </button>
            </div>
        </form>
    </div>

    {{-- ================= SWEETALERT SCRIPTS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ✅ Show Success Alert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // ❌ Show Error Alert
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                });
            @endif

            // 🟩 Confirm Before Updating Task
            const updateBtn = document.getElementById('updateBtn');
            const form = document.getElementById('editTaskForm');

            updateBtn.addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Update this task?',
                    text: "Make sure all changes are correct before saving.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, update it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
