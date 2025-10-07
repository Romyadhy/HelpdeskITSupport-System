app-layout>

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

<a href="{{ route('tickets.create') }}"

class="mt-4 sm:mt-0 inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">

+ New Ticket

</a>

</div>



<div class="bg-white rounded-2xl shadow overflow-hidden">

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

<tbody class="divide-y divide-gray-100">

@forelse($tickets as $ticket)

<tr class="hover:bg-gray-50 transition">

<td class="px-6 py-4 font-semibold text-gray-800">

#{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}

</td>


<td class="px-6 py-4">

<div class="font-medium text-gray-900">{{ $ticket->title }}</div>

<p class="text-xs text-gray-500 mt-1">

Created: {{ $ticket->created_at->format('Y-m-d') }}

</p>

</td>



<td class="px-6 py-4 hidden md:table-cell">

<div class="flex items-center">

<div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-600 mr-2">

{{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}

</div>

<span>{{ $ticket->user->name ?? 'Unknown' }}</span>

</div>

</td>



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



{{-- ACTION BUTTONS --}}

<td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">

{{-- View Detail --}}

<a href="{{ route('tickets.show', $ticket->id) }}"

class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition"

title="View Ticket">

<i class="fas fa-eye"></i>

</a>



{{-- Edit & Delete --}}

@if($ticket->status !== 'Closed' && (auth()->user()->isAdmin() || $ticket->user_id === auth()->id()))

<a href="{{ route('tickets.edit', $ticket->id) }}"

class="bg-blue-100 hover:bg-blue-200 text-blue-700 p-2 rounded-lg transition"

title="Edit Ticket">

<i class="fas fa-edit"></i>

</a>



<form action="{{ route('tickets.destroy', $ticket->id) }}"

method="POST" class="inline"

onsubmit="return confirm('Are you sure you want to delete this ticket?');">

@csrf

@method('DELETE')

<button type="submit"

class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg transition"

title="Delete Ticket">

<i class="fas fa-trash-alt"></i>

</button>

</form>

@endif





{{-- ===================== IT SUPPORT ACTIONS ===================== --}}

{{-- ===================== IT SUPPORT ACTIONS ===================== --}}

@if(auth()->user()->isSupport() && $ticket->status !== 'Closed')

{{-- Jika tiket belum di-escalate --}}

@if(!$ticket->is_escalation)

{{-- Tombol Assign selalu muncul (baik sudah diassign atau belum) --}}

<form action="{{ route('tickets.start', $ticket->id) }}" method="POST" class="inline">

@csrf

<button type="submit"

class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 p-2 rounded-lg transition"

title="Assign Ticket">

<i class="fas fa-wrench"></i>

</button>

</form>



{{-- Jika tiket sudah diassign ke support yang login --}}

@if($ticket->assigned_to == auth()->id())

{{-- Tombol Escalate ke Admin --}}

<form action="{{ route('tickets.escalate', $ticket->id) }}" method="POST" class="inline">

@csrf

<button type="submit"

class="bg-purple-100 hover:bg-purple-200 text-purple-700 p-2 rounded-lg transition"

title="Escalate to Admin">

<i class="fas fa-level-up-alt"></i>

</button>

</form>



{{-- Form Close Ticket --}}

<form action="{{ route('tickets.close', $ticket->id) }}" method="POST" class="inline-flex space-x-2">

@csrf

<input type="text"

name="solution"

placeholder="Solution..."

class="border rounded px-2 text-xs"

required>

<button type="submit"

class="bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-lg transition"

title="Close Ticket">

<i class="fas fa-check"></i>

</button>

</form>

@endif

@endif

@endif







{{-- ===================== ADMIN ACTIONS ===================== --}}

@if(auth()->user()->isAdmin() && $ticket->is_escalation && $ticket->status !== 'Closed')

{{-- Jika tiket belum diassign ke siapapun --}}

@if(!$ticket->assigned_to)

{{-- Tombol Assign (replace semua tombol lainnya) --}}

<form action="{{ route('tickets.start', $ticket->id) }}" method="POST" class="inline">

@csrf

<button type="submit"

class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 p-2 rounded-lg transition"

title="Assign Ticket">

<i class="fas fa-user-check"></i>

</button>

</form>

@elseif($ticket->assigned_to == auth()->id())

{{-- Setelah diassign, tampilkan input solusi --}}

<form action="{{ route('tickets.close', $ticket->id) }}" method="POST" class="inline-flex space-x-2">

@csrf

<input type="text"

name="solution"

placeholder="Admin Solution..."

class="border rounded px-2 text-xs"

required>

<button type="submit"

class="bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-lg transition"

title="Close as Admin">

<i class="fas fa-check"></i>

</button>

</form>

@endif

@endif

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