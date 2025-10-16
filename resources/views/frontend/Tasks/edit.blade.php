<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Task
        </h2>
    </x-slot>
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Task</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}"
                    class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2" required>{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Frequency</label>
                <select name="frequency"
                    class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2">
                    <option value="daily" {{ $task->frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="monthly" {{ $task->frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('tasks.show', $task->id) }}"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</a>
                <button type="submit"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Update Task</button>
            </div>
        </form>
    </div>
</x-app-layout>
