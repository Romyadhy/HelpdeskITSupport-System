<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ✏️ Edit Laporan Bulanan
        </h2>
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-100">

            {{-- Judul Halaman --}}
            <div class="mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-teal-700">
                    Edit Laporan Bulanan: {{ $report->month }} {{ $report->year }}
                </h2>
                <p class="text-sm text-gray-500">Dibuat pada: {{ $report->report_date->format('d M Y') }}</p>
            </div>

            {{-- Pesan Error --}}
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Edit --}}
            <form id="update-monthly-report-form" method="POST" action="{{ route('reports.monthly.update', $report->id) }}" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Informasi Laporan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">📅 Bulan</label>
                        <input type="text" name="month" value="{{ $report->month }}" readonly
                            class="block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">📆 Tahun</label>
                        <input type="text" name="year" value="{{ $report->year }}" readonly
                            class="block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Ringkasan Laporan --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">📝 Ringkasan Laporan</label>
                    <textarea name="content" id="content" rows="6"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Tuliskan pembaruan atau revisi laporan bulanan di sini..." required>{{ old('content', $report->content) }}</textarea>
                </div>

                {{-- Pilih Daily Reports --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">📋 Pilih Laporan Harian Terkait</h3>
                    <div class="bg-gray-50 border rounded-lg p-4 max-h-72 overflow-y-auto">
                        @forelse ($dailyReports as $daily)
                            <div class="flex items-start mb-3">
                                <input type="checkbox" name="daily_report_ids[]" value="{{ $daily->id }}"
                                    id="daily_{{ $daily->id }}"
                                    class="mt-1 mr-3 rounded border-gray-300 focus:ring-indigo-500 focus:ring-2"
                                    {{ in_array($daily->id, $report->daily_report_ids ?? []) ? 'checked' : '' }}>
                                <label for="daily_{{ $daily->id }}" class="text-sm text-gray-700 leading-snug">
                                    <strong>{{ \Carbon\Carbon::parse($daily->report_date)->format('d M Y') }}</strong> —
                                    {{ Str::limit($daily->content, 80) }}
                                    <br>
                                    <span class="text-xs text-gray-500">
                                        {{ $daily->tasks->count() }} tugas | {{ $daily->tickets->count() }} tiket
                                    </span>
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">Tidak ada laporan harian yang tersedia untuk bulan ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('reports.monthly.show', $report->id) }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                        ← Batal
                    </a>

                    <button type="submit"
                        class="px-5 py-2.5 bg-teal-600 text-white rounded-lg shadow hover:bg-teal-700 transition focus:ring-2 focus:ring-indigo-300">
                        💾 Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('update-monthly-report-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data yang Anda masukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488', // teal-600
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
