<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manager Dashboard
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h1 class="text-4xl font-extrabold text-teal-600">Dashboard Manager</h1>
                <p class="text-gray-600 mt-2">Laporan ringkas aktivitas IT Support & performa SLA</p>
            </div>

            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition">
                    <p class="text-sm text-gray-500">Total Tiket</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalTickets ?? 0 }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition">
                    <p class="text-sm text-gray-500">Selesai (Closed)</p>
                    <p class="text-3xl font-bold text-green-600">{{ $closedTickets ?? 0 }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition">
                    <p class="text-sm text-gray-500">Proses (Pending)</p>
                    <p class="text-3xl font-bold text-yellow-500">{{ $pendingTickets ?? 0 }}</p>
                </div>

                <div class="bg-white p-5 rounded-lg shadow hover:shadow-lg transition">
                    <p class="text-sm text-gray-500">Belum Dikerjakan (Open)</p>
                    <p class="text-3xl font-bold text-red-500">{{ $openTickets ?? 0 }}</p>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Pie Chart: Distribusi Status Tiket --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Distribusi Status Tiket</h3>
                    <div class="flex justify-center">
                        <div class="w-full max-w-md h-72">
                            <canvas id="ticketStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Bar Chart: Performa SLA --}}
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Performa SLA (Rata-rata Penyelesaian per Kategori)</h3>
                    <div class="w-full max-w-lg mx-auto h-80">
                        <canvas id="slaPerformanceChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Daftar Tiket Terbaru --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Tiket Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border border-gray-100">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 font-semibold text-gray-700">#</th>
                                <th class="px-4 py-2 font-semibold text-gray-700">Judul</th>
                                <th class="px-4 py-2 font-semibold text-gray-700">Kategori</th>
                                <th class="px-4 py-2 font-semibold text-gray-700">Prioritas</th>
                                <th class="px-4 py-2 font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($tickets ?? [] as $index => $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 font-medium text-gray-700">{{ $ticket->title }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $ticket->category->name ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($ticket->priority === 'High') bg-red-100 text-red-700
                                            @elseif($ticket->priority === 'Medium') bg-yellow-100 text-yellow-700
                                            @else bg-green-100 text-green-700 @endif">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            @if($ticket->status === 'closed') bg-green-100 text-green-700
                                            @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ticketStatusCtx = document.getElementById('ticketStatusChart').getContext('2d');
        const slaPerformanceCtx = document.getElementById('slaPerformanceChart').getContext('2d');

        // Pie Chart (Status Tiket)
        new Chart(ticketStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Closed', 'Pending', 'Open'],
                datasets: [{
                    data: [{{ $closedTickets ?? 0 }}, {{ $pendingTickets ?? 0 }}, {{ $openTickets ?? 0 }}],
                    backgroundColor: ['#16a34a', '#facc15', '#f87171'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 14 } }
                    }
                }
            }
        });

        // Bar Chart (SLA Performance)
        new Chart(slaPerformanceCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($slaCategories ?? []) !!},
                datasets: [{
                    label: 'Rata-rata durasi penyelesaian (menit)',
                    data: {!! json_encode($slaDurations ?? []) !!},
                    backgroundColor: '#14b8a6',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</x-app-layout>
