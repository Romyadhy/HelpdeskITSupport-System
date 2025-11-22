<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Dashboard
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- WELCOME --}}
            <div class="text-center mb-10">
                <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                    Welcome, <span class="text-teal-600">{{ auth()->user()->name }}</span> 👋
                </h1>
                <p class="text-gray-500 mt-2 text-lg">
                    Pantau status laporanmu & ajukan tiket baru kapan pun.
                </p>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">

                {{-- Open Tickets --}}
                <div class="bg-white shadow-md rounded-2xl p-6 border-l-4 border-teal-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Open Tickets</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $openTicketsCount }}</h3>
                        </div>
                        <i class="fas fa-folder-open text-teal-500 text-4xl"></i>
                    </div>
                </div>

                {{-- In progress --}}
                <div class="bg-white shadow-md rounded-2xl p-6 border-l-4 border-yellow-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">In Progress</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $inProgressTicketsCount }}</h3>
                        </div>
                        <i class="fas fa-spinner text-yellow-500 text-4xl"></i>
                    </div>
                </div>

                {{-- Closed --}}
                <div class="bg-white shadow-md rounded-2xl p-6 border-l-4 border-green-500 hover:shadow-xl transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Closed</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ $closedTicketsCount }}</h3>
                        </div>
                        <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                    </div>
                </div>

            </div>

            {{-- INFORMASI TIKET USER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ticket Insights</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Total Tiket Dibuat --}}
                    <div class="p-4 bg-gray-50 rounded-xl border hover:bg-gray-100 transition">
                        <p class="text-gray-500 text-sm">Total Tickets Created</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $userTickets->count() }}</h3>
                    </div>

                    {{-- Total Tiket Selesai --}}
                    <div class="p-4 bg-gray-50 rounded-xl border hover:bg-gray-100 transition">
                        <p class="text-gray-500 text-sm">Tickets Closed</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $closedTicketsCount }}</h3>
                    </div>

                    {{-- Rata-rata penyelesaian --}}
                    <div class="p-4 bg-gray-50 rounded-xl border hover:bg-gray-100 transition">
                        <p class="text-gray-500 text-sm">Avg. Resolution Time</p>

                        @php
                            $avg = $userTickets
                                ->where('status', 'Closed')
                                ->avg('duration');

                            $avgFormatted = $avg ? round($avg, 2) . ' hrs' : 'N/A';
                        @endphp

                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $avgFormatted }}</h3>
                    </div>

                </div>
            </div>

            {{-- RECENT TICKETS --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-8">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Tickets</h3>
                    <a href="{{ route('tickets.index') }}"
                        class="text-teal-600 text-sm hover:underline">View all</a>
                </div>

                @forelse ($recentTickets as $ticket)
                    <div class="border rounded-xl p-4 mb-3 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $ticket->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Created: {{ $ticket->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <span @class([
                                'px-3 py-1 text-xs rounded-full font-semibold',
                                'bg-red-100 text-red-600' => $ticket->status === 'Open',
                                'bg-yellow-100 text-yellow-700' => $ticket->status === 'In Progress',
                                'bg-green-100 text-green-700' => $ticket->status === 'Closed',
                            ])>
                                {{ $ticket->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm italic text-center">
                        Belum ada tiket yang kamu buat.
                    </p>
                @endforelse
            </div>

            {{-- CTA --}}
            <div class="text-center mt-10">
                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center bg-teal-600 text-white px-6 py-3 rounded-xl shadow-md hover:bg-teal-700 transition font-medium">
                    <i class="fas fa-plus mr-2"></i> Buat Tiket Baru
                </a>
            </div>

        </div>
    </div>

</x-app-layout>
