<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-calendar-alt text-indigo-500"></i>
            Laporan Bulanan IT Support
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- SUMMARY UTAMA BULANAN --}}
            <div
                class="bg-white shadow rounded-xl p-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                {{-- KIRI --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Hi, {{ Auth::user()->name }}
                    </h3>
                    <p class="text-gray-500 text-sm">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="text-md text-gray-500 pt-1">
                        Rekap laporan bulanan sebagai dokumentasi.
                    </p>
                </div>

                {{-- KANAN (BUTTON CREATE) --}}
                @can('create-monthly-report')
                    <a href="{{ route('reports.monthly.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5
            bg-teal-600 hover:bg-teal-700
            text-white font-semibold rounded-lg shadow transition">
                        <i class="fas fa-plus-circle"></i>
                        Buat Laporan Bulanan
                    </a>
                @endcan
            </div>

            {{-- STATISTIK BULANAN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 mb-8">

                {{-- Total Laporan --}}
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Laporan Bulan Ini</h4>
                    <p class="text-3xl font-bold text-indigo-600 mt-1">
                        {{ $totalMonthlyReports ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Jumlah laporan yang dikirim</p>
                </div>

                {{-- Total Tugas --}}
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Tugas Diselesaikan</h4>
                    <p class="text-3xl font-bold text-green-600 mt-1">
                        {{ $totalMonthlyTasks ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Akumulasi tugas bulanan</p>
                </div>

                {{-- Total Ticket --}}
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Ticket Ditangani</h4>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">
                        {{ $totalMonthlyTickets ?? '-' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Permintaan yang terselesaikan</p>
                </div>

            </div>


            {{-- WRAPPER UTAMA --}}
            <div class="bg-white shadow rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-indigo-600"></i>
                    Riwayat Laporan Bulanan
                </h3>

                @if ($monthlyReports->isEmpty())
                    <div class="p-6 text-center text-gray-500 italic">
                        <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                        <p>Belum ada laporan bulanan yang tersedia.</p>
                    </div>
                @else
                    <div class="space-y-5">

                        @foreach ($monthlyReports as $report)
                            <div class="border rounded-xl p-5 bg-gray-50 hover:bg-gray-100 transition shadow-sm">

                                {{-- HEADER --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-lg">
                                            {{ $report->month }} {{ $report->year }}
                                            <span class="text-sm text-gray-500">
                                                oleh {{ $report->user->name ?? '-' }}
                                            </span>
                                        </p>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Rekap aktivitas bulanan IT Support
                                        </p>
                                    </div>

                                    {{-- Badge --}}
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                        Verified
                                    </span>
                                </div>

                                {{-- ISI --}}
                                <div class="text-sm text-gray-600 mt-2 space-y-1">
                                    <p>📅 Total Hari Laporan: <b>{{ $report->total_days_reported }}</b></p>
                                    <p>✅ Total Tugas Diselesaikan: <b>{{ $report->total_tasks }}</b></p>
                                    <p>🎫 Total Tiket Ditangani: <b>{{ $report->total_tickets }}</b></p>
                                </div>

                                {{-- TOMBOL AKSI --}}
                                <div class="flex gap-3 mt-4">

                                    {{-- Detail --}}
                                    @can('view-monthly-reports')
                                        <a href="{{ route('reports.monthly.show', $report->id) }}"
                                            class="bg-teal-500 hover:bg-teal-600 text-white px-3 py-1.5 rounded-md text-sm transition">
                                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                                        </a>
                                    @endcan

                                    {{-- Edit --}}
                                    @can('edit-monthly-report')
                                        <a href="{{ route('reports.monthly.edit', $report->id) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md text-sm transition">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('delete-monthly-report')
                                        <form action="{{ route('reports.monthly.destroy', $report->id) }}" method="POST"
                                            class="delete-report-btn">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-sm transition">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    @endcan

                                </div>

                            </div>
                        @endforeach

                    </div>

                @endif
            </div>
            <div class="px-6 py-5 border-t bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-lg">

                <!-- Left: Showing Info -->
                <div class="text-sm text-gray-600">
                    Showing
                    <span class="font-semibold text-gray-900">{{ $monthlyReports->firstItem() }}</span>
                    to
                    <span class="font-semibold text-gray-900">{{ $monthlyReports->lastItem() }}</span>
                    of
                    <span class="font-semibold text-gray-900">{{ $monthlyReports->total() }}</span>
                    results
                </div>

                <!-- Right: Pagination -->
                <div class="flex items-center space-x-1">

                    {{-- Previous --}}
                    @if ($monthlyReports->onFirstPage())
                        <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $monthlyReports->previousPageUrl() }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300
                text-gray-600 hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($monthlyReports->links()->elements[0] as $page => $url)
                        @if ($page == $monthlyReports->currentPage())
                            <span class="px-4 py-2 rounded-xl bg-teal-500 text-white font-semibold shadow">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="px-4 py-2 rounded-xl bg-white border border-gray-300
                    text-gray-700 hover:bg-gray-100 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($monthlyReports->hasMorePages())
                        <a href="{{ $monthlyReport->nextPageUrl() }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300
                text-gray-600 hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-report-btn').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Laporan?',
                        text: 'Yakin ingin menghapus laporan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

</x-app-layout>
