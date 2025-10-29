<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            ⚙️ Support Dashboard
        </h2>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-8">
        {{-- Statistik Utama --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🟢</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Aktif</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $assignedTickets->where('status', 'open')->count() }}</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">🕓</div>
                <h3 class="text-gray-700 font-semibold text-sm">Menunggu Respon</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $assignedTickets->where('status', 'pending')->count() }}</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">✅</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Selesai</h3>
                <p class="text-2xl font-bold text-gray-900">{{ $assignedTickets->where('status', 'closed')->count() }}</p>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-2">📅</div>
                <h3 class="text-gray-700 font-semibold text-sm">Tiket Hari Ini</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ $assignedTickets->where('created_at', '>=', now()->startOfDay())->count() }}
                </p>
            </div>
        </div>

        {{-- Daftar Tiket Aktif --}}
        <div class="bg-white shadow-md rounded-2xl overflow-hidden border border-gray-100">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">🎫 Daftar Tiket yang Ditangani</h3>
                <a href="{{ route('tickets.index') }}"
                    class="px-4 py-2 text-sm font-medium bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition">
                    Lihat Semua Tiket
                </a>
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
                                <td class="px-6 py-3 text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 text-gray-900 font-medium">{{ $ticket->title ?? 'Tanpa Judul' }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $ticket->category->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if ($ticket->status === 'open')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Open</span>
                                    @elseif ($ticket->status === 'pending')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Closed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">
                                    Tidak ada tiket yang sedang ditangani.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Notifikasi Ringkas --}}
        <div class="bg-gradient-to-r from-teal-500 to-green-400 text-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-2">🔔 Informasi Penting</h3>
            <p class="text-sm leading-relaxed">
                Selamat datang kembali, <span class="font-bold">{{ Auth::user()->name }}</span>!
                Pastikan semua tiket aktif kamu tetap update dan segera ubah status menjadi
                <span class="font-semibold">Closed</span> setelah selesai dikerjakan.
            </p>
        </div>
    </div>
</x-app-layout>
