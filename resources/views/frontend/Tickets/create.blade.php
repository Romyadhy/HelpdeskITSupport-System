<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Ticket
            </h2>
            <a href="{{ route('tickets.index') }}"
                class="inline-block bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg shadow transition">
                ← Back to Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    {{-- FORM START --}}
                    <form id="create-ticket-form" method="POST" action="{{ route('tickets.store') }}">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-6">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" required autofocus
                                placeholder="Enter your issue title" :value="old('title')"
                                class="block mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm" />
                            @error('title')
                                <div
                                    class="mt-2 flex items-center bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="5" required placeholder="Describe your issue clearly..."
                                class="block mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            @error('description')
                                <div
                                    class="mt-2 flex items-center bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="mb-6">
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority" name="priority" required
                                class="block mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div
                                    class="mt-2 flex items-center bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="mb-6">
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" required
                                class="block mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div
                                    class="mt-2 flex items-center bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Location --}}
                        <div class="mb-6">
                            <x-input-label for="location_id" :value="__('Location')" />
                            <select id="location_id" name="location_id" required
                                class="block mt-1 w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                                <option value="">-- Select Location --</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div
                                    class="mt-2 flex items-center bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Button --}}
                        <div class="flex items-center justify-end mt-8">
                            <x-primary-button type="button" id="submit-ticket-btn"
                                class="bg-teal-500 text-white px-6 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                                <i class="fas fa-paper-plane mr-2"></i> Create Ticket
                            </x-primary-button>
                        </div>
                    </form>
                    {{-- FORM END --}}
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('submit-ticket-btn');
            const form = document.getElementById('create-ticket-form');

            btn.addEventListener('click', function() {
                const title = document.getElementById('title').value.trim();
                const desc = document.getElementById('description').value.trim();
                const category = document.getElementById('category_id').value;
                const location = document.getElementById('location_id').value;

                if (!title || !desc || !category || !location) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Fields',
                        text: 'Please fill in all required fields before submitting.',
                        confirmButtonColor: '#14b8a6'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Submit this ticket?',
                    text: "Please confirm your report before submitting.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#14b8a6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, create ticket',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
