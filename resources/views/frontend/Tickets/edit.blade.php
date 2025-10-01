<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Ticket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" 
                                      name="title" value="{{ old('title', $ticket->title) }}" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="5"
                                  class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            {{ old('description', $ticket->description) }}
                        </textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select name="priority" id="priority"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="Low" {{ $ticket->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $ticket->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $ticket->priority == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category" :value="__('Category')" />
                        <x-text-input id="category" class="block mt-1 w-full" type="text" 
                                      name="category" value="{{ old('category', $ticket->category) }}" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="location" :value="__('Location')" />
                        <x-text-input id="location" class="block mt-1 w-full" type="text" 
                                      name="location" value="{{ old('location', $ticket->location) }}" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>Update Ticket</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
