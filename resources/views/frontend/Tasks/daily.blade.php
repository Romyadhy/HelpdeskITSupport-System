<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily / Monthly Tasks</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                {{-- ================= CONTENT ================= --}}
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2">
                            <h3 class="text-lg font-semibold text-gray-700">📅 Daily Tasks</h3>
                            <p class="text-sm font-thin text-gray-400">Aktivitas yang dilakukan secara periodik untuk menjaga stabilitas dan keamanan sistem</p>
                        </div>
                        @can('create-task')
                            <a href="{{ route('tasks.create') }}"
                                class="inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                                <i class="fas fa-plus mr-2"></i> New Task
                            </a>
                        @endcan
                    </div>

                    {{-- ================= TABLE ================= --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                    @if (auth()->user()->hasRole('admin'))
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Active</th>
                                    @endif
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-gray-50 transition">
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
                                            @if (in_array($task->id, $completedTodays))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-3">

                                                {{-- Detail --}}
                                                <a href="{{ route('tasks.show', $task) }}" title="Detail"
                                                    class="text-gray-500 hover:text-indigo-600 transition">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Edit --}}
                                                @can('edit-task')
                                                    <a href="{{ route('tasks.edit', $task) }}" title="Edit"
                                                        class="text-teal-600 hover:text-teal-800 transition">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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
                                                        <button type="button" class="text-gray-500 hover:text-green-600 complete-task-btn"
                                                            title="Mark Complete">
                                                            @if (!in_array($task->id, $completedTodays))
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
                                        <td colspan="4" class="text-center py-4 text-gray-500">
                                            No daily tasks found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= SWEETALERT SCRIPTS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ SweetAlert Success Message
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // ❌ SweetAlert Error Message
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                });
            @endif

            // 🟩 Confirm Complete Task
            document.querySelectorAll('.complete-task-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Mark this task as complete?',
                        text: "You can’t undo this action for today.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, complete it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // 🗑️ Confirm Delete Task
            document.querySelectorAll('.delete-task-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Delete this task?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
