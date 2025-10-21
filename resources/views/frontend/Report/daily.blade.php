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

                @if($hasReportToday)
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        ✅ Sudah Lapor Hari Ini
                    </span>
                @else
                    <a href="{{ route('reports.daily.create') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow">
                       + Buat Laporan Harian
                    </a>
                @endif
            </div>

            {{-- Statistik kecil --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Laporan Bulan Ini</h4>
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

            {{-- Riwayat laporan --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Laporan Sebelumnya</h3>
                
                @forelse ($dailyReports as $report)
                    <div class="border-b py-3">
                        <div class="flex justify-between items-start">
                            <div>
                                {{-- Gunakan parse jika belum di-cast di model --}}
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($report->content, 80) }}
                                </p>

                                {{-- Tampilkan task dan ticket jika ada --}}
                                <div class="text-xs text-gray-500 mt-2">
                                    @if($report->tasks->count() > 0)
                                        <p><strong>Tasks:</strong> 
                                            {{ $report->tasks->pluck('title')->join(', ') }}
                                        </p>
                                    @endif
                                    @if($report->tickets->count() > 0)
                                        <p><strong>Tickets:</strong> 
                                            {{ $report->tickets->pluck('title')->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Status verifikasi --}}
                            @if ($report->verified_at)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Verified</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada laporan yang dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
