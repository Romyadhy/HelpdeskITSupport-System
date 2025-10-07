<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ticket #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h3 class="text-3xl font-extrabold text-teal-600">
                    {{ strtoupper($ticket->title) }}
                </h3>

                {{-- Status Badge --}}
                <span @class([
                    'mt-2 md:mt-0 px-4 py-1.5 text-sm font-semibold rounded-full shadow-sm',
                    'bg-blue-100 text-blue-800' => $ticket->status === 'Open',
                    'bg-yellow-100 text-yellow-800' => $ticket->status === 'In Progress',
                    'bg-green-100 text-green-800' => $ticket->status === 'Closed',
                ])>
                    {{ $ticket->status }}
                </span>
            </div>

            {{-- Description --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi Masalah:</h4>
                <p class="text-gray-700 leading-relaxed">{{ $ticket->description }}</p>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-700">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-layer-group text-teal-500"></i>
                    <span><strong>Category:</strong> {{ \App\Models\TicketCategory::find($ticket->category_id)->name ?? '-' }}<br></span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-map-marker-alt text-teal-500"></i>
                    <span><strong>Location:</strong> {{ \App\Models\TicketLocation::find($ticket->location_id)->name ?? '-' }}<br></span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user text-teal-500"></i>
                    <span><strong>Created By:</strong> {{ $ticket->user->name ?? 'Unknown' }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-calendar text-teal-500"></i>
                    <span><strong>Created At:</strong> {{ $ticket->created_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') }} WITA</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-flag text-teal-500"></i>
                    <span><strong>Priority:</strong> {{ $ticket->priority }}</span>
                </div>
                {{-- <div class="flex items-center space-x-2">
                    <i class="fas fa-stopwatch text-teal-500"></i>
                    <span><strong>Duration:</strong> 
                        {{ $ticket->duration ? $ticket->duration . ' minutes' : '-' }}
                    </span>
                </div> --}}
                <div class="flex items-center space-x-2">
                    <i class="fas fa-stopwatch text-teal-500"></i>
                    <div>
                        <span class="font-semibold">Duration:</span>
                        @if($ticket->status === 'Closed')
                            <span class="text-gray-700">
                                {{ $ticket->duration_human }}
                                <small class="text-gray-500 block text-xs mt-0.5">
                                    ({{ $ticket->started_at 
                                        ? $ticket->started_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') 
                                        : $ticket->created_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') 
                                    }}
                                    →
                                    {{ $ticket->solved_at 
                                        ? $ticket->solved_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') 
                                        : '—' }})
                                </small>
                            </span>
                        @elseif($ticket->status === 'In Progress' && $ticket->started_at)
                            @php
                                $minutes = $ticket->started_at->diffInMinutes(now());
                                $live = \Carbon\CarbonInterval::minutes($minutes)->cascade();
                            @endphp
                            <span class="text-yellow-700">
                                {{ $live->hours ? $live->hours . 'h ' : '' }}{{ $live->minutes }}m (running)
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Solution Section (only if Closed) --}}
            @if($ticket->status === 'Closed')
                <div class="mt-8 bg-green-50 border-l-4 border-green-500 rounded-lg p-5">
                    <h4 class="text-lg font-semibold text-green-700 mb-2 flex items-center">
                        <i class="fas fa-tools mr-2"></i> Solusi dari Tim IT Support
                    </h4>
                    <p class="text-gray-700 leading-relaxed">
                        {{ $ticket->solution ?? 'Belum ada solusi yang tercatat.' }}
                    </p>
                </div>
            @endif

            {{-- Assigned To --}}
            @if($ticket->assigned_to)
                <div class="mt-5 bg-teal-50 border-l-4 border-teal-500 rounded-lg p-5">
                    <h4 class="text-lg font-semibold text-teal-700 mb-2 flex items-center">
                        <i class="fas fa-user-cog mr-2"></i> Ditangani Oleh
                    </h4>
                    <p class="text-gray-700">
                        {{ $ticket->solver->name ?? 'IT Support' }}
                    </p>
                </div>
            @endif

            {{-- Back Button --}}
            <div class="mt-8 flex justify-end">
                <a href="{{ route('tickets.index') }}" 
                   class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2.5 rounded-lg shadow transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Tickets
                </a>
            </div>
        </div>
    </div>

    
</x-app-layout>
