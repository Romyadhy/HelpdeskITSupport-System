<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Monthly Reports</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-end mb-4">
            <a href="{{ route('reports.monthly.create') }}"
                class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">+ Buat Laporan Bulanan</a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Bulan</th>
                        <th class="px-4 py-2 text-left">Total Task</th>
                        <th class="px-4 py-2 text-left">Total Ticket</th>
                        <th class="px-4 py-2 text-left">Tanggal Buat</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monthly as $report)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $report->month }}</td>
                            <td class="px-4 py-2">{{ $report->total_tasks }}</td>
                            <td class="px-4 py-2">{{ $report->total_tickets }}</td>
                            <td class="px-4 py-2">{{ $report->report_date->format('d M Y') }}</td>
                            <td class="px-4 py-2">
                                <a href="#"
                                    class="text-teal-600 hover:underline">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada laporan bulanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
