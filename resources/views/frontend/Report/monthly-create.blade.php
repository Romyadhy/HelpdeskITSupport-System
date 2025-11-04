<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            🗓️ Buat Laporan Bulanan
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8">

                {{-- Error Message --}}
                @if ($errors->any())
                    <div class="mb-8 rounded-xl border-l-4 border-red-500 bg-red-50 p-4 text-red-700">
                        <p class="font-semibold mb-2">Terjadi kesalahan:</p>
                        <ul class="list-disc pl-6 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Header Info --}}
                <div class="mb-8 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-teal-700">
                            Periode Laporan: {{ $month }} {{ $year }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Data laporan harian di bawah diambil otomatis berdasarkan bulan dan tahun saat ini.
                        </p>
                    </div>

                    {{-- Optional month selector --}}
                    <form method="GET" action="{{ route('reports.monthly.create') }}" class="flex items-center gap-3">
                        <input type="month" name="period" value="{{ $period ?? now()->format('Y-m') }}"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button
                            class="px-3 py-2 rounded-md bg-teal-600 text-white hover:bg-teal-700 transition shadow-sm">
                            Ganti Periode
                        </button>
                    </form>
                </div>

                {{-- Statistik --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 text-center shadow-sm">
                        <p class="text-sm text-blue-600">Jumlah Hari Dilaporkan</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700">{{ $totalDaysReported }}</p>
                    </div>
                    <div class="bg-green-50 border border-green-100 rounded-xl p-5 text-center shadow-sm">
                        <p class="text-sm text-green-600">Total Tasks</p>
                        <p class="mt-2 text-3xl font-bold text-green-700">{{ $totalTasks }}</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-5 text-center shadow-sm">
                        <p class="text-sm text-amber-600">Total Tickets</p>
                        <p class="mt-2 text-3xl font-bold text-amber-700">{{ $totalTickets }}</p>
                    </div>
                </div>

                {{-- Form Create --}}
                <form method="POST" action="{{ route('reports.monthly.store') }}" class="space-y-10">
                    @csrf

                    {{-- Ringkasan --}}
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                            📝 Ringkasan Laporan Bulanan
                        </label>
                        <textarea id="content" name="content" rows="6"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Tuliskan ringkasan kegiatan, pencapaian, kendala, serta rekomendasi selama periode ini..." required>{{ old('content') }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">
                            Gunakan ringkasan ini untuk menyoroti capaian utama, hambatan, dan insight dari laporan
                            harian.
                        </p>
                    </div>

                    {{-- Pilih Daily Reports --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-700 text-sm">📋 Laporan Harian dalam Periode Ini</h3>
                            <button type="button" id="btnSelectAll"
                                class="text-xs px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 transition">
                                Pilih Semua / Hapus Semua
                            </button>
                        </div>

                        <div
                            class="bg-gray-50 border rounded-lg p-4 max-h-80 overflow-y-auto divide-y divide-gray-200 shadow-inner">
                            @forelse ($dailyReports as $report)
                                <div class="py-3 flex items-start gap-3">
                                    <input type="checkbox" class="mt-1 dr-check accent-indigo-600"
                                        name="daily_report_ids[]" id="dr_{{ $report->id }}"
                                        value="{{ $report->id }}"
                                        {{ in_array($report->id, old('daily_report_ids', [])) ? 'checked' : '' }}>
                                    <label for="dr_{{ $report->id }}" class="cursor-pointer flex-1">
                                        <div class="flex justify-between text-sm font-medium text-gray-800">
                                            <span>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</span>
                                            <span class="text-xs text-gray-500">
                                                {{ $report->tasks->count() }} tugas • {{ $report->tickets->count() }}
                                                tiket
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ Str::limit($report->content, 120) }}
                                        </p>
                                    </label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic py-2">Tidak ada laporan harian untuk periode ini.
                                </p>
                            @endforelse
                        </div>

                        <p class="text-xs text-gray-500 mt-2 italic">
                            *Jika tidak memilih laporan harian apa pun, maka tidak ada laporan yang akan disertakan.*
                        </p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('reports.monthly') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                            ← Batal
                        </a>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-300 transition">
                            💾 Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Select All helper --}}
    <script>
        document.getElementById('btnSelectAll')?.addEventListener('click', function() {
            const checks = document.querySelectorAll('.dr-check');
            const allChecked = Array.from(checks).every(c => c.checked);
            checks.forEach(c => c.checked = !allChecked);
        });
    </script>
</x-app-layout>
