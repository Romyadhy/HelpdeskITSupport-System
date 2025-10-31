<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            🧭 Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-10">
        {{-- Statistik Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <div
                class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">📊</div>
                <h3 class="text-gray-700 font-semibold text-sm">Total Tiket</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalTickets ?? 0 }}</p>
            </div>

            <div
                class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">✅</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Selesai</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $closedTickets ?? 0 }}</p>
            </div>

            <div
                class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🕓</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Pending</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingTickets ?? 0 }}</p>
            </div>

            <div
                class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🟢</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Open</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $openTickets ?? 0 }}</p>
            </div>

            <div
                class="bg-purple-50 border border-purple-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">👥</div>
                <h3 class="text-gray-700 font-semibold text-sm">Total User</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
            </div>
        </div>

        {{-- Chart SLA --}}
        <div class="bg-white shadow-md rounded-2xl border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">📈 Statistik SLA Tiket Bulan Ini</h3>
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <canvas id="slaChart" height="120"></canvas>

            @if ($slaCategories->isEmpty())
                <p class="text-center text-gray-500 text-sm mt-4 italic">Belum ada data SLA untuk bulan ini.</p>
            @endif
        </div>

        {{-- Tiket Terbaru --}}
        <div class="bg-white shadow-md rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">🧾 Tiket Terbaru</h3>
                <a href="{{ route('tickets.index') }}"
                    class="px-4 py-2 text-sm font-medium bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-gray-600 font-semibold">#</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold">Judul</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold">Kategori</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold">User</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold text-center">Status</th>
                            <th class="px-6 py-3 text-gray-600 font-semibold text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tickets as $index => $ticket)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 text-gray-900 font-medium">{{ $ticket->title ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->category_name ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->user->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if ($ticket->status === 'Open')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-black">Open</span>
                                    @elseif ($ticket->status === 'In Progress')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-black">In Progress</span>
                                    @elseif ($ticket->status === 'Closed')
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-black">Closed</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">
                                    Belum ada tiket terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('slaChart').getContext('2d');

        const labels = @json($slaCategories ?? []);
        const dataValues = @json($slaDurations ?? []);

        const slaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.length > 0 ? labels : ['Tidak Ada Data'],
                datasets: [{
                    label: 'Rata-rata Durasi (menit)',
                    data: dataValues.length > 0 ? dataValues : [0],
                    backgroundColor: ['#10b981', '#3b82f6', '#facc15', '#ef4444', '#a855f7'],
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Durasi (menit)'
                        },
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
