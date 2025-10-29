<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            🧭 Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-10">
        {{-- Statistik Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">📊</div>
                <h3 class="text-gray-700 font-semibold text-sm">Total Tiket</h3>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::count() }}</p>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🟢</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Selesai</h3>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'closed')->count() }}</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🕓</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Pending</h3>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Ticket::where('status', 'pending')->count() }}</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">👥</div>
                <h3 class="text-gray-700 font-semibold text-sm">Total User</h3>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
            </div>
        </div>

        {{-- Chart SLA --}}
        <div class="bg-white shadow-md rounded-2xl border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">📈 Statistik SLA Tiket Bulan Ini</h3>
                <span class="text-sm text-gray-500">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <canvas id="slaChart" height="120"></canvas>
        </div>

        {{-- Tiket Terbaru --}}
        <div class="bg-white shadow-md rounded-2xl border border-gray-100">
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
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->category->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->user->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if ($ticket->status === 'open')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Open</span>
                                    @elseif ($ticket->status === 'pending')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Closed</span>
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

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('slaChart').getContext('2d');
        const slaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['<1 Jam', '1-4 Jam', '4-8 Jam', '>8 Jam'],
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: [12, 9, 5, 3], // contoh data dummy (bisa diganti data real dari controller)
                    backgroundColor: ['#10b981', '#3b82f6', '#facc15', '#ef4444'],
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 5 }
                    }
                }
            }
        });
    </script>
</x-app-layout>
