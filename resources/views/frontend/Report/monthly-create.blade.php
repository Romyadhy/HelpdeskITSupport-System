<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Laporan Bulanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('reports.monthly.store') }}">
                @csrf

                {{-- Input hidden untuk tahun dan bulan --}}
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">

                <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Laporan untuk Bulan: {{ $monthName }}</h3>
                        <p class="mt-1 text-sm text-gray-600">Berikut adalah rangkuman data untuk periode ini. Silakan tulis analisis Anda di bawah.</p>
                    </div>

                    {{-- Menampilkan Statistik Tiket --}}
                    <div class="p-6 border-b border-gray-200">
                        <h4 class="font-medium text-gray-700 mb-2">Statistik Tiket</h4>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">Total Tiket Masuk:</dt>
                            <dd class="font-semibold text-gray-900">{{ $ticketStats['total_created'] ?? 0 }}</dd>

                            <dt class="text-gray-500">Total Tiket Selesai:</dt>
                            <dd class="font-semibold text-gray-900">{{ $ticketStats['total_closed'] ?? 0 }}</dd>

                            <dt class="text-gray-500">Total Eskalasi:</dt>
                            <dd class="font-semibold text-gray-900">{{ $ticketStats['total_escalated'] ?? 0 }}</dd>
                            
                            <dt class="text-gray-500">Waktu Penyelesaian Rata-rata:</dt>
                            <dd class="font-semibold text-gray-900">
                                {{ $ticketStats['avg_duration_minutes'] ? number_format($ticketStats['avg_duration_minutes'] / 60, 1) . ' jam' : 'N/A' }}
                            </dd>
                        </dl>
                        {{-- Input hidden untuk mengirim data statistik tiket --}}
                        <input type="hidden" name="ticket_stats_json" value="{{ json_encode($ticketStats) }}">
                    </div>

                    {{-- Menampilkan Statistik Laporan Harian --}}
                    <div class="p-6">
                        <h4 class="font-medium text-gray-700 mb-2">Statistik Laporan Harian</h4>
                         <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="text-gray-500">Total Laporan Masuk:</dt>
                            <dd class="font-semibold text-gray-900">{{ $dailyReportStats['total_reports_submitted'] ?? 0 }}</dd>

                            <dt class="text-gray-500">Total Laporan Diverifikasi:</dt>
                            <dd class="font-semibold text-gray-900">{{ $dailyReportStats['total_reports_verified'] ?? 0 }}</dd>
                            
                            <dt class="text-gray-500">Jumlah Staf Melapor:</dt>
                            <dd class="font-semibold text-gray-900">{{ $dailyReportStats['total_staff_reported'] ?? 0 }}</dd>
                        </dl>
                         {{-- Input hidden untuk data statistik laporan harian (jika perlu disimpan) --}}
                         {{-- <input type="hidden" name="daily_report_stats_json" value="{{ json_encode($dailyReportStats) }}"> --}}
                    </div>
                </div>

                {{-- Textarea untuk Summary --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                         <label for="summary" class="block text-sm font-medium text-gray-700">Ringkasan & Analisis Bulanan</label>
                         <textarea name="summary" id="summary" rows="10" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis analisis Anda mengenai performa, kendala, dan rekomendasi untuk bulan ini..." required>{{ old('summary') }}</textarea>
                         <x-input-error :messages="$errors->get('summary')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('reports.monthly') }}" class="inline-flex items-center px-4 py-2 bg-white border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                        Batal
                    </a>
                    <x-primary-button>
                        Simpan Laporan Bulanan
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>