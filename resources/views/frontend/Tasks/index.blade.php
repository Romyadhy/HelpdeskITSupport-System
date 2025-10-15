<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daily / Monthly Tasks
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                {{-- Tabs Navigation --}}
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


                {{-- Daily Tasks --}}
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

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                                        Title</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                                        Status</th>
                                    <th
                                        class="px-4 py-2 text-center text-sm font-medium text-gray-700">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($dailyTasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-semibold">
                                            {{ $task->title }}</td>

                                        <td class="px-4 py-3 text-left">
                                            @if (in_array($task->id, $completedTodayIds))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{-- Aksi untuk Admin (CRUD) --}}
                                            <a href="{{ route('tasks.show', $task->id) }}" title="Show"
                                                class="inline-block px-2 py-1 text-sm text-gray-500 hover:text-gray-800"><i
                                                class="fas fa-eye"></i>
                                            </a>
                                            @can('edit-task')
                                                <a href="{{ route('tasks.edit', $task->id) }}" title="Edit"
                                                    class="inline-block px-2 py-1 text-sm text-teal-600 hover:text-teal-800"><i
                                                        class="fas fa-edit"></i></a>
                                            @endcan

                                            {{-- Aksi untuk IT Support (Checklist) --}}
                                            @can('checked-task')
                                                @if (!in_array($task->id, $completedTodayIds))
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" title="Mark as Complete"
                                                            class="px-3 py-1 text-sm text-green-600 hover:text-green-800">
                                                            <i class="fas fa-check-circle"></i> Mark Complete
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">No daily tasks found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Monthly Tasks (Logika Serupa) --}}
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
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                                        Title</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-medium text-gray-700">
                                        Status</th>
                                    <th
                                        class="px-4 py-2 text-center text-sm font-medium text-gray-700">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($monthlyTasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-semibold">
                                            {{ $task->title }}</td>

                                        <td class="px-4 py-3 text-left">
                                            @if (in_array($task->id, $completedTodayIds))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{-- Aksi untuk Admin (CRUD) --}}
                                            <a href="{{ route('tasks.show', $task->id) }}" title="Show"
                                                class="inline-block px-2 py-1 text-sm text-gray-500 hover:text-gray-800"><i
                                                class="fas fa-eye"></i>
                                            </a>
                                            @can('edit-task')
                                                <a href="{{ route('tasks.edit', $task->id) }}" title="Edit"
                                                    class="inline-block px-2 py-1 text-sm text-teal-600 hover:text-teal-800"><i
                                                        class="fas fa-edit"></i></a>
                                            @endcan

                                            {{-- Aksi untuk IT Support (Checklist) --}}
                                            @can('checked-task')
                                                @if (!in_array($task->id, $completedTodayIds))
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" title="Mark as Complete"
                                                            class="px-3 py-1 text-sm text-green-600 hover:text-green-800">
                                                            <i class="fas fa-check-circle"></i> Mark Complete
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">No daily tasks found.
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

    {{-- Simple JS to switch tabs --}}
    <script>
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
