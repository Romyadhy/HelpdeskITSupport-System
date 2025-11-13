<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            ⚙️ Support Dashboard
        </h2>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-10">

        {{-- ======================= --}}
        {{--     STATISTIK UTAMA     --}}
        {{-- ======================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center shadow hover:shadow-md transition">
                <div class="text-4xl mb-2">🟢</div>
                <h3 class="text-gray-700 text-sm font-semibold">Tiket Aktif</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $openTickets }}</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center shadow hover:shadow-md transition">
                <div class="text-4xl mb-2">🕓</div>
                <h3 class="text-gray-700 text-sm font-semibold">Sedang Dikerjakan</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $inProgressTickets }}</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center shadow hover:shadow-md transition">
                <div class="text-4xl mb-2">✅</div>
                <h3 class="text-gray-700 text-sm font-semibold">Tiket Selesai</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $closedTickets }}</p>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 text-center shadow hover:shadow-md transition">
                <div class="text-4xl mb-2">📅</div>
                <h3 class="text-gray-700 text-sm font-semibold">Tiket Hari Ini</h3>
                <p class="text-3xl font-bold text-gray-900">{{ $todayTickets }}</p>
            </div>

        </div>



        {{-- ======================= --}}
        {{--     QUICK ACTIONS      --}}
        {{-- ======================= --}}
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Quick Actions</h3>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('tickets.index') }}"
                   class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm shadow">
                    🔎 Lihat Semua Tiket
                </a>

                <a href="{{ route('tickets.index', ['status' => 'Open']) }}"
                   class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm shadow">
                    🟢 Tiket Open
                </a>

                <a href="{{ route('tickets.index', ['status' => 'In Progress']) }}"
                   class="px-4 py-2 rounded-lg bg-yellow-600 hover:bg-yellow-700 text-white text-sm shadow">
                    🕓 Tiket In Progress
                </a>

                <a href="{{ route('tickets.index', ['status' => 'Closed']) }}"
                   class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm shadow">
                    ✅ Tiket Closed
                </a>
            </div>
        </div>



        {{-- ============================= --}}
        {{--     DAILY REPORT REMINDER     --}}
        {{-- ============================= --}}
        @php
            // $hasReportToday = \App\Models\DailyReport::where('user_id', auth()->id())
            //     ->whereDate('report_date', now()->toDateString())
            //     ->exists();
            $hasReportToday = \App\Models\DailyReport::whereDate('report_date', today())->exists();
        @endphp

        <div class="bg-gradient-to-r from-teal-500 to-green-400 rounded-2xl shadow text-white p-6">
            <h3 class="text-lg font-semibold mb-2">📝 Daily Report</h3>

            @if ($hasReportToday)
                <p class="text-sm">
                    Kamu sudah mengirim laporan harian hari ini. Terima kasih! 🎉  
                </p>
            @else
                <p class="text-sm mb-3">
                    Kamu belum mengirim laporan harian hari ini. Jangan lupa submit ya! 😊
                </p>

                <a href="{{ route('reports.daily.create') }}"
                   class="inline-block mt-2 px-4 py-2 bg-white text-teal-700 font-semibold rounded-lg shadow hover:bg-gray-100 text-sm">
                    ✏️ Buat Laporan Hari Ini
                </a>
            @endif
        </div>



        {{-- ======================= --}}
        {{--  DAFTAR TIKET DITANGANI --}}
        {{-- ======================= --}}
        <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">🎫 Tiket Yang Sedang Kamu Tangani</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-gray-600 font-semibold">#</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold">Judul Tiket</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold">Kategori</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold text-center">Status</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($assignedTickets as $index => $ticket)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $ticket->title }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->category_name ?? '-' }}</td>

                                <td class="px-6 py-3 text-center">
                                    @if ($ticket->status === 'Open')
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">Open</span>
                                    @elseif ($ticket->status === 'In Progress')
                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">In Progress</span>
                                    @elseif ($ticket->status === 'Closed')
                                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Closed</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">
                                    Tidak ada tiket yang sedang kamu tangani.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- ======================== --}}
        {{--  TIKET BARU DITUTUP     --}}
        {{-- ======================== --}}
        @php
            $recentClosed = $assignedTickets->where('status', 'Closed')->sortByDesc('solved_at')->take(5);
        @endphp

        <div class="bg-white shadow rounded-2xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🧹 Tiket Terakhir yang Kamu Selesaikan</h3>

            @if ($recentClosed->count() > 0)
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
                <p class="text-gray-500 italic text-sm">Belum ada tiket selesai beberapa hari ini.</p>
            @endif
        </div>

    </div>
</x-app-layout>
