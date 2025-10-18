<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily / Monthly Tasks</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">

                {{-- Tabs header --}}
                <div class="border-b flex">
                    {{-- <a href="{{ route('tasks.daily') }}"
                        class="flex-1 py-3 text-center text-sm font-semibold border-b-2 {{ request()->routeIs('tasks.daily') ? 'text-emerald-600 border-emerald-600' : 'text-gray-500 border-transparent hover:text-emerald-600 hover:border-emerald-300' }}">
                        Daily Tasks
                    </a> --}}
                    <a href="{{ route('tasks.monthly') }}"
                        class="flex-1 py-3 text-center text-sm font-semibold border-b-2 {{ request()->routeIs('tasks.monthly') ? 'text-emerald-600 border-emerald-600' : 'text-gray-500 border-transparent hover:text-emerald-600 hover:border-emerald-300' }}">
                        Monthly Tasks
                    </a>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">🗓️ Monthly Tasks</h3>
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
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status (this
                                        month)</th>
                                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $task->title }}</td>

                                        <td class="px-4 py-3">
                                            @if (in_array($task->id, $completedMonthlys))
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Completed</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center space-x-2">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('tasks.show', $task) }}" title="Detail"
                                                    class="inline-block px-2 py-1 text-gray-500 hover:text-gray-800">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit-task')
                                                    <a href="{{ route('tasks.edit', $task) }}" title="Edit"
                                                        class="inline-block px-2 py-1 text-teal-600 hover:text-teal-800">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete-task')
                                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-block px-2 py-1 text-red-400 hover:text-red-600">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('checked-task')
                                                    {{-- @if (!in_array($task->id, $completedMonthlys)) --}}
                                                    <form action="{{ route('tasks.complete', $task) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 text-sm">
                                                            @if (!in_array($task->id, $completedMonthlys))
                                                                <i
                                                                    class="far fa-circle text-gray-400 hover:text-green-600"></i>
                                                            @else
                                                                <i class="fas fa-check-circle text-green-500"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    {{-- @endif --}}
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">No monthly tasks
                                            found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
