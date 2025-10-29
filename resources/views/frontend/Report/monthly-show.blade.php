<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📅 Detail Laporan Bulanan
        </h2>
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-100">

            {{-- Header --}}
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-teal-700 mb-1">
                    Laporan Bulanan: {{ $report->month }} {{ $report->year }}
                </h2>
                <p class="text-gray-500 text-sm">
                    Dibuat pada: {{ $report->report_date->format('d M Y') }}
                </p>
            </div>

            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-indigo-50 p-5 rounded-xl text-center shadow-sm hover:shadow-md transition">
                    <div class="text-3xl mb-2">👤</div>
                    <h3 class="text-gray-700 text-sm font-semibold">Pembuat</h3>
                    <p class="text-gray-900 font-bold">{{ $report->user->name ?? '-' }}</p>
                </div>

                <div class="bg-green-50 p-5 rounded-xl text-center shadow-sm hover:shadow-md transition">
                    <div class="text-3xl mb-2">🧾</div>
                    <h3 class="text-gray-700 text-sm font-semibold">Status</h3>
                    @if ($report->status == 'Verified')
                        <p class="text-green-700 font-bold">Verified</p>
                    @else
                        <p class="text-yellow-700 font-bold">Pending</p>
                    @endif
                </div>

                <div class="bg-blue-50 p-5 rounded-xl text-center shadow-sm hover:shadow-md transition">
                    <div class="text-3xl mb-2">✅</div>
                    <h3 class="text-gray-700 text-sm font-semibold">Total Hari Dilaporkan</h3>
                    <p class="text-gray-900 font-bold">{{ $report->total_days_reported ?? 0 }} Hari</p>
                </div>

                <div class="bg-purple-50 p-5 rounded-xl text-center shadow-sm hover:shadow-md transition">
                    <div class="text-3xl mb-2">📌</div>
                    <h3 class="text-gray-700 text-sm font-semibold">Total Tugas & Tiket</h3>
                    <p class="text-gray-900 font-bold">
                        {{ $report->total_tasks ?? 0 }} Tugas <br>
                        {{ $report->total_tickets ?? 0 }} Tiket
                    </p>
                </div>
            </div>

            {{-- Ringkasan Laporan --}}
            <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-800 mb-3 border-b pb-2">
                    📝 Ringkasan Laporan
                </h3>
                <div class="bg-gray-50 border rounded-xl p-5 text-gray-700 leading-relaxed shadow-sm">
                    {!! nl2br(e($report->content)) !!}
                </div>
            </div>

            {{-- Rincian Laporan Harian --}}
            <div class="mb-10">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    📆 Rincian Laporan Harian
                </h3>

                @if (count($dailyReports) > 0)
                    <div class="overflow-x-auto border rounded-xl shadow-sm">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-indigo-100 text-gray-700 uppercase text-sm tracking-wider">
                                    <th class="px-4 py-3 text-left border-b">Tanggal</th>
                                    <th class="px-4 py-3 text-left border-b">Ringkasan</th>
                                    <th class="px-4 py-3 text-center border-b">Tugas</th>
                                    <th class="px-4 py-3 text-center border-b">Tiket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dailyReports as $daily)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-gray-800 border-b">
                                            {{ \Carbon\Carbon::parse($daily->report_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 border-b">
                                            {{ Str::limit($daily->content, 100) }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-700 border-b">
                                            {{ $daily->tasks->count() }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-700 border-b">
                                            {{ $daily->tickets->count() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg p-4 text-sm">
                        Tidak ada laporan harian yang terkait dengan laporan ini.
                    </div>
                @endif
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('reports.monthly') }}"
                    class="px-5 py-2.5 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700 transition">
                    ← Kembali
                </a>

                {{-- 🔽 Export PDF --}}
                <a href="{{ route('reports.monthly.pdf', $report->id) }}"
                    class="inline-flex items-center gap-2 bg-rose-600/90 text-white px-3 py-1.5 rounded-md text-sm font-medium shadow-sm hover:bg-rose-700 transition">
                    <i class="fas fa-file-pdf text-xs"></i>
                    Export PDF
                </a>
            </div>



        </div>
    </div>
</x-app-layout>
