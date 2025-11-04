<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🗓️ Daily Report Overview</h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary Card --}}
            <div class="bg-white shadow rounded-xl p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Hi, {{ Auth::user()->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ now()->format('l, d F Y') }}</p>
                </div>

                @if ($hasReportToday)
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        ✅ Sudah Lapor Hari Ini
                    </span>
                @else
                    @can('create-daily-report')
                        <a href="{{ route('reports.daily.create') }}"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow">
                            + Buat Laporan Harian
                        </a>
                    @endcan
                @endif
            </div>

            {{-- Statistik kecil --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Laporan Harian di Bulan Ini</h4>
                    <p class="text-3xl font-bold text-blue-600">{{ $monthlyReportsCount }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Tugas Diselesaikan Hari Ini</h4>
                    <p class="text-3xl font-bold text-green-600">{{ $completedTasksCount }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Ticket Ditangani Hari Ini</h4>
                    <p class="text-3xl font-bold text-yellow-600">{{ $handledTicketsCount }}</p>
                </div>
            </div>

            {{-- OPSIONAL: Statistik per support untuk admin/manager --}}
            @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager'))
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Statistik Support</h3>
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="p-2 text-left">Nama Support</th>
                                <th class="p-2 text-center">Jumlah Laporan Bulan Ini</th>
                                <th class="p-2 text-center">Total Task</th>
                                <th class="p-2 text-center">Total Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailyReports->groupBy('user.name') as $name => $reports)
                                <tr class="border-t">
                                    <td class="p-2">{{ $name }}</td>
                                    <td class="text-center p-2">{{ $reports->count() }}</td>
                                    <td class="text-center p-2">{{ $reports->flatMap->tasks->count() }}</td>
                                    <td class="text-center p-2">{{ $reports->flatMap->tickets->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Riwayat laporan --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Laporan Sebelumnya</h3>

                @forelse ($dailyReports as $report)
                    <div class="border-b py-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                    @if (isset($report->user))
                                        <span class="text-sm text-gray-500"> oleh {{ $report->user->name }}</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($report->content, 80) }}</p>
                                <a href="{{ route('reports.daily.show', $report->id) }}"
                                    class="text-teal-600 hover:underline text-sm">Lihat Detail</a>

                                <div class="text-xs text-gray-500 mt-2 mb-2">
                                    @if ($report->tasks->count() > 0)
                                        <p><strong>Tasks:</strong> {{ $report->tasks->pluck('title')->join(', ') }}</p>
                                    @endif
                                    @if ($report->tickets->count() > 0)
                                        <p><strong>Tickets:</strong> {{ $report->tickets->pluck('title')->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Status verifikasi --}}
                            @if ($report->verified_at)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Verified</span>
                            @else
                                <span
                                    class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                            @endif

                            
                        </div>
                        {{-- Export Pdf --}}
                            <a href="{{ route('reports.daily.pdf', $report->id) }}"
                                class="inline-flex items-center gap-2 bg-rose-500/90 text-white px-2.5 py-1.5 rounded-md text-sm font-medium shadow-sm hover:bg-rose-600 transition-all duration-150">
                                <i class="fas fa-file-pdf text-xs"></i>
                                Export PDF
                            </a>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada laporan yang dibuat.</p>
                @endforelse
            </div>


        </div>
    </div>
</x-app-layout>
