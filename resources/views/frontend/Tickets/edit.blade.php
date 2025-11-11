<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Ticket
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <form id="editTicketForm" method="POST" action="{{ route('tickets.update', $ticket) }}">
                @csrf
                @method('PATCH')

                {{-- ====== Title ====== --}}
                <div>
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                        value="{{ old('title', $ticket->title) }}" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                {{-- ====== Description ====== --}}
                <div class="mt-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description" rows="5" required
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">{{ old('description', $ticket->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                {{-- ====== Priority ====== --}}
                <div class="mt-4">
                    <x-input-label for="priority" :value="__('Priority')" />
                    <select name="priority" id="priority" required
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="Low" {{ $ticket->priority == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ $ticket->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ $ticket->priority == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                {{-- ====== Category ====== --}}
                <div class="mt-4">
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select name="category_id" id="category_id" required
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $ticket->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                {{-- ====== Location ====== --}}
                <div class="mt-4">
                    <x-input-label for="location_id" :value="__('Location')" />
                    <select name="location_id" id="location_id" required
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                        <option value="">-- Select Location --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}"
                                {{ $ticket->location_id == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                </div>

                {{-- ====== Submit Button ====== --}}
                <div class="flex items-center justify-end mt-6">
                    <x-primary-button type="submit" id="updateButton">
                        Update Ticket
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✅ SweetAlert Confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("editTicketForm");
        const button = document.getElementById("updateButton");

        button.addEventListener("click", function(e) {
            e.preventDefault();

            // Pastikan semua field wajib diisi
            const requiredFields = ["title", "description", "priority", "category_id", "location_id"];
            for (let field of requiredFields) {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please fill out all required fields before updating.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
            }

            // Konfirmasi sebelum update
            Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update this ticket?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
