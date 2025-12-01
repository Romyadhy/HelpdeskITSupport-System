<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-calendar-alt text-indigo-500"></i>
            Monthly Reports
        </h2>
    </x-slot>

    <div class="py-10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Tombol Buat Laporan Bulanan --}}
            @can('create-monthly-report')
                <div class="flex justify-end mb-6">
                    <a href="{{ route('reports.monthly.create') }}"
                        class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 
                        text-white px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Buat Laporan Bulanan
                    </a>
                </div>
            @endcan

            {{-- Card Utama --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-alt text-indigo-500"></i>
                        Daftar Laporan Bulanan
                    </h3>
                </div>

                {{-- Jika Tidak Ada Data --}}
                @if ($monthlyReports->isEmpty())
                    <div class="p-6 text-center text-gray-500 italic">
                        <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                        <p>Belum ada laporan bulanan yang tersedia.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 text-sm text-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3 border">#</th>
                                    <th class="px-4 py-3 border">Nama User</th>
                                    <th class="px-4 py-3 border">Bulan</th>
                                    <th class="px-4 py-3 border">Total Hari</th>
                                    <th class="px-4 py-3 border">Total Tugas</th>
                                    <th class="px-4 py-3 border">Total Tiket</th>
                                    <!-- <th class="px-4 py-3 border">Status</th> -->
                                    <th class="px-4 py-3 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyReports as $index => $report)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-3 border text-center font-medium text-gray-600">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 border font-medium">{{ $report->user->name ?? '-' }}</td>
                                        <td class="px-4 py-3 border font-medium text-gray-700">
                                            {{ $report->month }} {{ $report->year }}
                                        </td>
                                        <td class="px-4 py-3 border text-center text-gray-600">{{ $report->total_days_reported }}</td>
                                        <td class="px-4 py-3 border text-center text-gray-600">{{ $report->total_tasks }}</td>
                                        <td class="px-4 py-3 border text-center text-gray-600">{{ $report->total_tickets }}</td>

                                        {{-- Status dengan Warna Dinamis --}}
                                        <!-- <td class="px-4 py-3 border text-center">
                                            @if ($report->status === 'Verified')
                                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            @elseif ($report->status === 'Pending Verification')
                                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">
                                                    <i class="fas fa-hourglass-half"></i> Pending
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-semibold">
                                                    <i class="fas fa-pencil-alt"></i> Draft
                                                </span>
                                            @endif
                                        </td> -->

                                        {{-- Aksi --}}
                                        <td class="px-4 py-3 border text-center">
                                            <div class="flex items-center justify-center gap-3">

                                                {{-- Detail --}}
                                                @can('view-monthly-reports')
                                                    <a href="{{ route('reports.monthly.show', $report->id) }}"
                                                        class="text-blue-600 hover:text-blue-800 transition-all flex items-center gap-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan

                                                {{-- Edit --}}
                                                @can('edit-monthly-report')
                                                    <a href="{{ route('reports.monthly.edit', $report->id) }}"
                                                        class="text-green-600 hover:text-green-800 transition-all flex items-center gap-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                {{-- Verifikasi --}}
                                                <!-- @can('verify-daily-report')
                                                    @if ($report->status !== 'Verified')
                                                        <form action="{{ route('reports.monthly.verify', $report->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Verifikasi laporan ini?')"
                                                            class="verify-report-btn flex items-center gap-1 text-indigo-600 hover:text-indigo-800 transition-all">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan -->

                                                {{-- Hapus --}}
                                                @can('delete-monthly-report')
                                                    <form action="{{ route('reports.monthly.destroy', $report->id) }}"
                                                        method="POST"
                                                        class="delete-report-btn flex items-center gap-1 text-red-600 hover:text-red-800 transition-all">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            document.querySelectorAll('.delete-report-btn').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault(); // Prevent default form submission
                    
                    Swal.fire({
                        title: 'Hapus Laporan?',
                        text: 'Yakin ingin menghapus laporan ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // Red color for delete
                        cancelButtonColor: '#6b7280', // Gray for cancel
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            });
        });
      
    </script>
</x-app-layout>
