<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daily / Monthly Tasks
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                {{-- ================= Tabs Navigation ================= --}}
                <div class="border-b flex">
                    <button onclick="showTab('daily')" id="tab-daily"
                        class="flex-1 py-3 text-center text-sm font-semibold text-emerald-600 border-b-2 border-emerald-600">
                        Daily Tasks
                    </button>
                    <button onclick="showTab('monthly')" id="tab-monthly"
                        class="flex-1 py-3 text-center text-sm font-semibold text-gray-500 hover:text-emerald-600 border-b-2 border-transparent hover:border-emerald-300">
                        Monthly Tasks
                    </button>
                </div>

                {{-- ================= DAILY TASKS ================= --}}
                <div id="daily" class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">📅 Daily Tasks</h3>
                        @can('create-task')
                            <a href="{{ route('tasks.create') }}"
                                class="inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                                <i class="fas fa-plus mr-2"></i> New Task
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($dailyTasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-semibold">{{ $task->title }}</td>

                                        <td class="px-4 py-3 text-left">
                                            @if (in_array($task->id, $completedTodayIds))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center space-x-2">
                                            {{-- View --}}
                                            <a href="{{ route('tasks.show', $task->id) }}" title="View Task"
                                                class="text-gray-400 hover:text-indigo-600 p-2 rounded-lg transition">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- Edit --}}
                                            @can('edit-task')
                                                <a href="{{ route('tasks.edit', $task->id) }}" title="Edit Task"
                                                    class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            {{-- Delete --}}
                                            @can('delete-task')
                                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline delete-task-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" title="Delete Task"
                                                        class="delete-task-btn text-gray-400 hover:text-red-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                            {{-- Mark Complete --}}
                                            @can('checked-task')
                                                @if (!in_array($task->id, $completedTodayIds))
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline complete-task-form">
                                                        @csrf
                                                        <button type="button" title="Mark as Complete"
                                                            class="complete-task-btn text-gray-400 hover:text-green-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">
                                            No daily tasks found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= MONTHLY TASKS ================= --}}
                <div id="monthly" class="p-6 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">🗓️ Monthly Tasks</h3>
                        @can('manage-tasks')
                            <a href="{{ route('tasks.create') }}"
                                class="inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                                <i class="fas fa-plus mr-2"></i> New Task
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($monthlyTasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-semibold">{{ $task->title }}</td>
                                        <td class="px-4 py-3 text-left">
                                            @if (in_array($task->id, $complatedMonthly))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center space-x-2">
                                            <a href="{{ route('tasks.show', $task->id) }}" title="View Task"
                                                class="text-gray-400 hover:text-indigo-600 p-2 rounded-lg transition">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @can('edit-task')
                                                <a href="{{ route('tasks.edit', $task->id) }}" title="Edit Task"
                                                    class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('checked-task')
                                                @if (!in_array($task->id, $complatedMonthly))
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline complete-task-form">
                                                        @csrf
                                                        <button type="button" title="Mark as Complete"
                                                            class="complete-task-btn text-gray-400 hover:text-green-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">
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
    </div>

    {{-- ================= SCRIPTS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ✅ SweetAlert: Global success
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            @endif

            // ❌ SweetAlert: Global error
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                });
            @endif

            // 🟩 Confirm: Mark as Complete
            document.querySelectorAll('.complete-task-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Mark this task as complete?',
                        text: "This action will be recorded as completed.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, complete it'
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
                        title: 'Delete this task?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });

        // 🧭 Tabs
        function showTab(tab) {
            const daily = document.getElementById('daily');
            const monthly = document.getElementById('monthly');
            const tabDaily = document.getElementById('tab-daily');
            const tabMonthly = document.getElementById('tab-monthly');

            if (tab === 'daily') {
                daily.classList.remove('hidden');
                monthly.classList.add('hidden');
                tabDaily.classList.add('text-emerald-600', 'border-emerald-600');
                tabMonthly.classList.remove('text-emerald-600', 'border-emerald-600');
            } else {
                monthly.classList.remove('hidden');
                daily.classList.add('hidden');
                tabMonthly.classList.add('text-emerald-600', 'border-emerald-600');
                tabDaily.classList.remove('text-emerald-600', 'border-emerald-600');
            }
        }
    </script>
</x-app-layout>
