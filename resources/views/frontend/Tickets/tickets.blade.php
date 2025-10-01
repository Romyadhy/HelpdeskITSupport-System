<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tickets
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-teal-500">TICKET KEREN</h1>
                    <p class="mt-1 text-gray-600">Manage and track all support requests.</p>
                </div>
                <a href="{{ route('tickets.create') }}"
                   class="bg-teal-500 hover:bg-teal-600 transition text-white py-2 px-4 rounded-lg shadow">
                    + Create Ticket
                </a>
            </div>

            <div class="bg-white shadow-lg rounded-xl">
                <!-- SCROLLER -->
                <div class="overflow-x-auto">
                    <table class="min-w-[1100px] w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Created By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Solved By</th>
                                {{-- <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell">Duration</th> --}}
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell">Created At</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <!-- Title + short description -->
                                    <td class="px-6 py-4 align-top">
                                        <div class="text-sm font-semibold text-gray-900">{{ $ticket->title }}</div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 hidden md:table-cell">
                                        {{ $ticket->category ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 hidden lg:table-cell">
                                        {{ $ticket->location ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                         <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-red-100 text-red-800'     => $ticket->priority === 'High',
                                            'bg-yellow-100 text-yellow-800'=> $ticket->priority === 'Medium',
                                            'bg-green-100 text-green-800' => $ticket->priority === 'Low',
                                        ])>
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                         <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-blue-100 text-blue-800'    => $ticket->status === 'Open',
                                            'bg-yellow-100 text-yellow-800'=> $ticket->status === 'In Progress',
                                            'bg-green-100 text-green-800'  => $ticket->status === 'Closed',
                                        ])>
                                            {{ $ticket->status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 hidden md:table-cell">
                                        {{ $ticket->user->name ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700 hidden lg:table-cell">
                                        {{ $ticket->solver->name ?? '-' }}
                                    </td>

                                    {{-- <td class="px-6 py-4 text-sm text-gray-700 hidden xl:table-cell">
                                        {{ $ticket->duration ? $ticket->duration.' min' : '-' }}
                                    </td> --}}

                                    <td class="px-6 py-4 text-sm text-gray-500 hidden xl:table-cell">
                                        {{ $ticket->created_at->format('d M Y, H:i') }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(auth()->user()->isAdmin() || $ticket->user_id === auth()->id())
                                        <a href="{{ route('tickets.edit', $ticket) }}" 
                                            class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                            <form action="{{ route('tickets.destroy', $ticket) }}" 
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                        {{-- @if($ticket->status !== 'Closed')
                                            <form action="{{ route('tickets.close', $ticket) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="solution" value="Fixed by IT Support">
                                                <button type="submit"
                                                        class="text-teal-600 hover:text-teal-900 underline decoration-dotted">
                                                    Close
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-green-600">Closed</span>
                                        @endif --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-8 text-center text-gray-500">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
