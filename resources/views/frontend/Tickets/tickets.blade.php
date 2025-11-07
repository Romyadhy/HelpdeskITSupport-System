
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
                                    {{-- Ticket ID --}}
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>

                                    {{-- Subject --}}
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $ticket->title }}</div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Created: {{ $ticket->created_at->format('Y-m-d') }}
                                        </p>
                                    </td>

                                    {{-- User --}}
                                    <td class="px-6 py-4 hidden md:table-cell">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-600 mr-2">
                                                {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <span>{{ $ticket->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
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

                                    {{-- Priority --}}
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

                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">

                                        {{-- View --}}
                                        <a href="{{ route('tickets.show', $ticket->id) }}" title="View Ticket"
                                            class="text-gray-400 hover:text-indigo-600 p-2 rounded-lg transition">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- ==== User ==== --}}
                                        @can('edit-own-ticket', $ticket)
                                            @if ($ticket->status === 'Open')
                                                <a href="{{ route('tickets.edit', $ticket->id) }}" title="Edit Ticket"
                                                    class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        @endcan

                                        @can('delete-own-ticket', $ticket)
                                            @if (in_array($ticket->status, ['Open', 'Closed']))
                                                <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST"
                                                    class="inline delete-ticket-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" title="Delete Ticket"
                                                        class="delete-ticket-btn text-gray-400 hover:text-red-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan

                                        {{-- ==== Support ==== --}}
                                        @can('handle-ticket')
                                            @if ($ticket->status === 'Open' && !$ticket->assigned_to)
                                                {{-- Handle Ticket --}}
                                                <form action="{{ route('tickets.start', $ticket->id) }}" method="POST"
                                                    class="inline handle-ticket-form">
                                                    @csrf
                                                    <button type="button" title="Handle Ticket"
                                                        class="handle-ticket-btn text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-wrench mr-1"></i>
                                                    </button>
                                                </form>

                                            @elseif($ticket->status === 'In Progress' && $ticket->assigned_to === auth()->id())
                                                {{-- Close Ticket --}}
                                                @can('close-ticket')
                                                    <button type="button" title="Close Ticket"
                                                        class="close-ticket-btn text-gray-400 hover:text-green-600 p-2 rounded-lg transition"
                                                        data-action="{{ route('tickets.close', $ticket->id) }}">
                                                        <i class="fas fa-check mr-1"></i>
                                                    </button>
                                                @endcan

                                                {{-- Escalate --}}
                                                @can('escalate-ticket')
                                                    <button type="button" title="Escalate Ticket"
                                                        class="escalate-ticket-btn text-gray-400 hover:text-purple-600 p-2 rounded-lg transition"
                                                        data-action="{{ route('tickets.escalate', $ticket->id) }}">
                                                        <i class="fas fa-level-up-alt mr-1"></i>
                                                    </button>
                                                @endcan

                                                {{-- 🟠 Cancel Ticket --}}
                                                <form action="{{ route('tickets.cancel', $ticket->id) }}" method="POST"
                                                    class="inline cancel-ticket-form">
                                                    @csrf
                                                    <button type="button" title="Cancel Handling"
                                                        class="cancel-ticket-btn text-gray-400 hover:text-orange-600 p-2 rounded-lg transition"
                                                        data-ticket-id="{{ $ticket->id }}">
                                                        <i class="fas fa-ban mr-1"></i>
                                                    </button>
                                                </form>

                                            @elseif($ticket->status === 'In Progress' && $ticket->assigned_to !== auth()->id())
                                                @can('take-over')
                                                    <form action="{{ route('tickets.takeOver', $ticket->id) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit" title="Take Over Ticket"
                                                            class="text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                                            <i class="fas fa-hand-paper mr-1"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        @endcan

                                        {{-- ==== Admin ==== --}}
                                        @can('handle-escalated-ticket')
                                            @if (!$ticket->is_escalation && $ticket->status === 'In Progress' && $ticket->assigned_to === auth()->id())
                                                @can('close-ticket')
                                                    <button type="button" title="Close Ticket"
                                                        class="close-ticket-btn text-gray-400 hover:text-green-600 p-2 rounded-lg transition"
                                                        data-action="{{ route('tickets.close', $ticket->id) }}">
                                                        <i class="fas fa-check mr-1"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                            @if ($ticket->is_escalation && $ticket->status === 'In Progress')
                                                <form action="{{ route('tickets.handleEscalated', $ticket->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" title="Handle Escalated Ticket"
                                                        class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                                        <i class="fas fa-user-check mr-1"></i>
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

    {{-- ✅ SweetAlert Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ✅ Global Success Alert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif

            // 🗑️ Delete
            document.querySelectorAll('.delete-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Delete this ticket?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel'
                    }).then(result => { if (result.isConfirmed) form.submit(); });
                });
            });

            // 🔧 Handle
            document.querySelectorAll('.handle-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Start Handling?',
                        text: 'You will take responsibility for this ticket.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Handle it',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6b7280'
                    }).then(result => { if (result.isConfirmed) form.submit(); });
                });
            });

            // ✅ Close Ticket (solution input)
            document.querySelectorAll('.close-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const actionUrl = button.dataset.action;
                    Swal.fire({
                        title: 'Finish Ticket',
                        text: 'Please describe your solution before closing.',
                        input: 'textarea',
                        inputPlaceholder: 'Enter solution here...',
                        showCancelButton: true,
                        confirmButtonText: 'Submit Solution',
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        preConfirm: value => {
                            if (!value.trim()) Swal.showValidationMessage('Solution is required!');
                            return value;
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            const token = document.createElement('input');
                            token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
                            const solution = document.createElement('input');
                            solution.type = 'hidden'; solution.name = 'solution'; solution.value = result.value;
                            form.append(token, solution);
                            document.body.append(form);
                            form.submit();
                        }
                    });
                });
            });

            // ⬆️ Escalate
            document.querySelectorAll('.escalate-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const actionUrl = button.dataset.action;
                    Swal.fire({
                        title: 'Escalate Ticket?',
                        text: 'This will forward the issue to higher support.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#9333ea',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, escalate it'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            const token = document.createElement('input');
                            token.type = 'hidden';
                            token.name = '_token';
                            token.value = '{{ csrf_token() }}';
                            form.appendChild(token);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // 🟠 Cancel Ticket (new polish)
            document.querySelectorAll('.cancel-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Cancel Handling?',
                        text: 'This ticket will be reopened and available for other support members.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, release it'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

        });
    </script>
</x-app-layout>
