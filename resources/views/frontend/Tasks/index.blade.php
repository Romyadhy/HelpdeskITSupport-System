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
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">
                        📅 Daily Tasks
                    </h3>
                    @can('create-task')    
                        <a href="{{ route('tasks.create') }}" 
                            class="mt-4 mb-4 sm:mt-0 inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                            + New Tasks
                        </a>
                    @endcan

                    @if($dailyTasks->isEmpty())
                        <p class="text-gray-500 italic">Belum ada tugas harian yang terdaftar.</p>
                    @else
                        <div class="overflow-x-auto">
                             @if (session('success'))
                                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Success!</strong>
                                    <span class="block sm:inline">{{ session('success') }}</span>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            @endif
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Description</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Status</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Frequency</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($dailyTasks as $task)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-gray-800 font-semibold">{{ $task->title }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $task->description ?? '-' }}</td>
                                            <td class="px-4 py-2 text-center">
                                                @if($task->is_active)
                                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-center capitalize text-gray-700">
                                                {{ $task->frequency }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                
                                                <a href="{{ route('tasks.show', $task->id) }}"
                                                    class="inline-block px-3 py-1 text-sm text-teal-600 hover:text-teal-800">
                                                    Show
                                                </a>
                                                @can('edit-task', $task)    
                                                <a href="{{ route('tasks.edit', $task->id) }}"
                                                    class="inline-block px-3 py-1 text-sm text-teal-600 hover:text-teal-800">
                                                    Edit
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Monthly Tasks --}}
                <div id="monthly" class="p-6 hidden">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">
                        🗓️ Monthly Tasks
                    </h3>
                    @can('create-task')    
                    <a href="{{ route('tasks.create') }}" 
                       class="mt-4 mb-4 sm:mt-0 inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                       + New Tasks
                   </a>
                    @endcan

                    @if($monthlyTasks->isEmpty())
                        <p class="text-gray-500 italic">Belum ada tugas bulanan yang terdaftar.</p>
                    @else
                        <div class="overflow-x-auto">
                             @if (session('success'))
                                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Success!</strong>
                                    <span class="block sm:inline">{{ session('success') }}</span>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            @endif
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Title</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Description</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Status</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Frequency</th>
                                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($monthlyTasks as $task)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2 text-gray-800 font-semibold">{{ $task->title }}</td>
                                            <td class="px-4 py-2 text-gray-600">{{ $task->description ?? '-' }}</td>
                                            <td class="px-4 py-2 text-center">
                                                @if($task->is_active)
                                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-center capitalize text-gray-700">
                                                {{ $task->frequency }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <a href="{{ route('tasks.show', $task->id) }}"
                                                    class="inline-block px-3 py-1 text-sm text-teal-600 hover:text-teal-800">
                                                    Show
                                                </a>
                                                @can('edit-task', $task)    
                                                <a href="{{ route('tasks.edit', $task->id) }}"
                                                    class="inline-block px-3 py-1 text-sm text-teal-600 hover:text-teal-800">
                                                    Edit
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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
