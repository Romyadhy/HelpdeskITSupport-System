<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-2xl text-gray-800 tracking-tight">
                Support Dashboard
            </h2>
            <p class="text-sm text-gray-500">
                Welcome back, {{ auth()->user()->name }} 👋
            </p>
        </div>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-10">

        {{-- ================= STATS CARDS ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-xl">🔓</div>
                <div>
                    <p class="text-sm text-gray-500">Tiket Aktif</p>
                    <h3 class="text-3xl font-semibold">{{ $openTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-700 text-xl">🕓</div>
                <div>
                    <p class="text-sm text-gray-500">Sedang Dikerjakan</p>
                    <h3 class="text-3xl font-semibold">{{ $inProgressTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-700 text-xl">✅</div>
                <div>
                    <p class="text-sm text-gray-500">Tiket Selesai</p>
                    <h3 class="text-3xl font-semibold">{{ $closedTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-indigo-100 text-indigo-700 text-xl">📅</div>
                <div>
                    <p class="text-sm text-gray-500">Tiket Hari Ini</p>
                    <h3 class="text-3xl font-semibold">{{ $todayTickets }}</h3>
                </div>
            </div>

        </div>

        {{-- ================= QUICK ACTIONS ================= --}}
        <div class="border rounded-xl bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Quick Actions</h3>
            <p class="mb-4 text-sm text-gray-600">Shortcut untuk melihat daftar pelaporan masalah</p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('tickets.index') }}"
                   class="px-4 py-2 rounded-lg bg-gray-700 text-white text-sm hover:bg-gray-900 transition">
                    <i class="fa-solid fa-ticket"></i> Semua Ticket
                </a>

                <a href="{{ route('tickets.index', ['status' => 'Open']) }}"
                   class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-green-700 transition">
                    <i class="fa-solid fa-book-open"></i> Open
                </a>

                <a href="{{ route('tickets.index', ['status' => 'In Progress']) }}"
                   class="px-4 py-2 rounded-lg bg-yellow-500 text-white text-sm hover:bg-yellow-600 transition">
                    <i class="fa-solid fa-clock"></i> In Progress
                </a>

                <a href="{{ route('tickets.index', ['status' => 'Closed']) }}"
                   class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm hover:bg-blue-700 transition">
                    <i class="fa-solid fa-circle-check"></i> Closed
                </a>
            </div>
        </div>

        {{-- ================= DAILY REPORT ================= --}}
        @php
            $hasReportToday = \App\Models\DailyReport::whereDate('report_date', today())->exists();
        @endphp

        <div class="border rounded-xl shadow-sm p-6
            {{ $hasReportToday ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">

            <h3 class="text-lg font-semibold mb-1">Daily Report</h3>

            @if ($hasReportToday)
                <p class="text-sm text-green-700">
                    ✔ Kamu sudah mengirim laporan harian hari ini
                </p>
            @else
                <p class="text-sm text-yellow-700 mb-3">
                    ⚠ Kamu belum mengirim laporan harian hari ini
                </p>

                <a href="{{ route('reports.daily.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                    Buat Laporan Hari Ini
                </a>
            @endif
        </div>

        {{-- ================= ASSIGNED TICKETS TABLE ================= --}}
        <div class="border rounded-xl bg-white shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    Tiket yang Sedang Kamu Tangani
                </h3>

                <a href="{{ route('tickets.index') }}"
                    class="text-sm font-medium text-teal-500 hover:text-teal-700 transition">
                    Lihat Semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b text-gray-600">
                        <tr>
                            <th class="p-3 text-left">#</th>
                            <th class="p-3 text-left">Judul</th>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assignedTickets as $index => $ticket)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3">{{ $index + 1 }}</td>
                                <td class="p-3 font-medium text-gray-900">
                                    {{ $ticket->title }}
                                </td>
                                <td class="p-3 text-gray-700">
                                    {{ $ticket->category_name ?? '-' }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs
                                        @if ($ticket->status === 'Open')
                                            bg-green-100 text-green-700
                                        @elseif ($ticket->status === 'In Progress')
                                            bg-yellow-100 text-yellow-700
                                        @elseif ($ticket->status === 'Closed')
                                            bg-blue-100 text-blue-700
                                        @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-center text-gray-500">
                                    {{ $ticket->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500 italic">
                                    Tidak ada tiket yang sedang kamu tangani.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= RECENT CLOSED ================= --}}
        @php
            $recentClosed = $assignedTickets
                ->where('status', 'Closed')
                ->sortByDesc('solved_at')
                ->take(5);
        @endphp

        <div class="border rounded-xl bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">
                Tiket Terakhir yang Kamu Selesaikan
            </h3>

            @if ($recentClosed->count())
                <ul class="space-y-3">
                    @foreach ($recentClosed as $ticket)
                        <li class="flex justify-between items-center border-b pb-2">
                            <span class="text-gray-700">{{ $ticket->title }}</span>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($ticket->solved_at)->diffForHumans() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 italic text-sm">
                    Belum ada tiket yang diselesaikan baru-baru ini.
                </p>
            @endif
        </div>

    </div>
</x-app-layout>
