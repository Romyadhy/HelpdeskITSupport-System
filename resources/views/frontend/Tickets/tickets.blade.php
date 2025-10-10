<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Support Tickets
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-teal-600">All Tickets</h1>
                    <p class="text-gray-500 mt-1">Manage and track all support requests.</p>
                </div>
                @can('create-ticket')
                    <a href="{{ route('tickets.create') }}" 
                        class="mt-4 sm:mt-0 inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                        + New Ticket
                    </a>
                @endcan
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Ticket ID</th>
                                <th class="px-6 py-3 text-left font-semibold">Subject</th>
                                <th class="px-6 py-3 text-left font-semibold hidden md:table-cell">User</th>
                                <th class="px-6 py-3 text-left font-semibold">Status</th>
                                <th class="px-6 py-3 text-left font-semibold">Priority</th>
                                <th class="px-6 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition text-left">
                                    {{-- Kolom Ticket ID --}}
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    
                                    {{-- Kolom Subject --}}
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $ticket->title }}</div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Created: {{ $ticket->created_at->format('Y-m-d') }}
                                        </p>
                                    </td>

                                    {{-- Kolom User --}}
                                    <td class="px-6 py-4 hidden md:table-cell">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-600 mr-2">
                                                {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <span>{{ $ticket->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'px-3 py-1 text-xs font-semibold rounded-full',
                                            'bg-red-100 text-red-600' => $ticket->status === 'Open',
                                            'bg-yellow-100 text-yellow-700' => $ticket->status === 'In Progress',
                                            'bg-green-100 text-green-700' => $ticket->status === 'Closed',
                                            'bg-purple-100 text-purple-700' => $ticket->status === 'Escalated',
                                        ])>
                                            {{ $ticket->status }}
                                        </span>
                                    </td>

                                    {{-- Kolom Priority --}}
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'px-3 py-1 text-xs font-semibold rounded-full',
                                            'bg-red-100 text-red-600' => $ticket->priority === 'High',
                                            'bg-orange-100 text-orange-600' => $ticket->priority === 'Medium',
                                            'bg-green-100 text-green-700' => $ticket->priority === 'Low',
                                        ])>
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>

                                    {{-- ===================== ACTIONS ===================== --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">

                                        {{-- View Button --}}
                                        <a href="{{ route('tickets.show', $ticket->id) }}" 
                                        title="View Ticket" 
                                        class="text-gray-400 hover:text-indigo-600 p-2 rounded-lg transition">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- ==== User ==== --}}
                                        @can('edit-own-ticket', $ticket)
                                            @if($ticket->status === 'Open')
                                                <a href="{{ route('tickets.edit', $ticket->id) }}" 
                                                title="Edit Ticket" 
                                                class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        @endcan

                                        @can('delete-own-ticket', $ticket)
                                            @if(in_array($ticket->status, ['Open', 'Closed']))
                                                <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" 
                                                    onsubmit="return confirm('Are you sure?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete Ticket" 
                                                            class="text-gray-400 hover:text-red-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        {{-- ==== Support ==== --}}
                                        @can('handle-ticket')
                                            {{-- Hanldle --}}
                                            @if($ticket->status === 'Open' && !$ticket->assigned_to)
                                                <form action="{{ route('tickets.start', $ticket->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" title="Handle Ticket" 
                                                            class="text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-wrench"></i>
                                                    </button>
                                                </form>

                                            {{-- Closed --}}
                                            @elseif($ticket->status === 'In Progress' && $ticket->assigned_to === auth()->id())
                                                @can('close-ticket')
                                                    <form action="{{ route('tickets.close', $ticket->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                                        @csrf
                                                        <input type="text" name="solution" placeholder="Solution..." class="border rounded px-2 text-xs w-28" required>
                                                        <button type="submit" title="Close Ticket" 
                                                                class="text-gray-400 hover:text-green-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endcan

                                                {{-- Escalations --}}
                                                @can('escalate-ticket')
                                                    <form action="{{ route('tickets.escalate', $ticket->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" title="Escalate Ticket" 
                                                                class="text-gray-400 hover:text-purple-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-level-up-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan

                                            {{-- Take Over --}}
                                            @elseif($ticket->status === 'In Progress' && $ticket->assigned_to !== auth()->id())
                                                @can('take-over')
                                                    <form action="{{ route('tickets.takeOver', $ticket->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" title="Take Over Ticket" 
                                                                class="text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-hand-paper"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        @endcan

                                        {{-- ==== Admin ==== --}}
                                        @can('handle-escalated-ticket')
                                            @if($ticket->is_escalation && $ticket->status === 'In Progress')
                                                <form action="{{ route('tickets.handleEscalated', $ticket->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" title="Handle Escalated Ticket" 
                                                            class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>