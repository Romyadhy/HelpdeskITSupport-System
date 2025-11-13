<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            🗓️ Buat Laporan Harian
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @role('support')
                <div class="bg-white shadow-lg rounded-2xl p-8 space-y-8">

                    {{-- TOP BAR --}}
                    <div class="flex items-center justify-between">
                        <div class="text-gray-700 font-semibold text-lg">
                            Form Laporan Harian
                        </div>
                    </div>

                    {{-- SWEETALERT NOTIF --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            @if (session('success'))
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: '{{ session('success') }}',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            @endif

                            @if (session('warning'))
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan!',
                                    text: '{{ session('warning') }}',
                                    confirmButtonText: 'OK',
                                });
                            @endif

                            @if ($errors->any())
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    html: `{!! implode('<br>', $errors->all()) !!}`,
                                    confirmButtonText: 'Perbaiki'
                                });
                            @endif
                        });
                    </script>

                    <form id="dailyReportForm" method="POST" action="{{ route('reports.daily.store') }}" class="space-y-8">
                        @csrf

                        {{-- DESKRIPSI --}}
                        <div class="space-y-2">
                            <label class="font-semibold text-gray-700">Deskripsi Laporan</label>
                            <textarea name="content" class="w-full border-gray-300 rounded-xl focus:ring focus:ring-emerald-300 p-3"
                                      rows="4" placeholder="Tuliskan kegiatan hari ini..." required></textarea>
                            <p class="text-xs text-gray-500">Tuliskan ringkasan kegiatanmu hari ini.</p>
                        </div>

                        {{-- GRID 2 KOLOM --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- TASK LIST (Auto Included) --}}
                            <div>
                                <label class="font-semibold text-gray-700 flex justify-between mb-2">
                                    <span>Tugas yang Diselesaikan (Otomatis)</span>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        {{ $tasksCompletedToday->count() }} items
                                    </span>
                                </label>

                                <div class="space-y-2 bg-gray-50 border rounded-xl p-3 max-h-64 overflow-y-auto">
                                    @forelse ($tasksCompletedToday as $task)
                                        {{-- Hidden input untuk otomatis mengirim --}}
                                        <input type="hidden" name="task_ids[]" value="{{ $task->id }}">

                                        <div class="p-2 rounded-lg bg-white shadow-sm border text-sm text-gray-700">
                                            {{ $task->title }}
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada task completed hari ini</p>
                                    @endforelse
                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    Semua tugas di atas otomatis masuk ke laporan.
                                </p>
                            </div>

                            {{-- TICKET LIST (Auto Included) --}}
                            <div>
                                <label class="font-semibold text-gray-700 flex justify-between mb-2">
                                    <span>Ticket Dikerjakan (Otomatis)</span>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        {{ $ticketsClosedToday->count() + $ticketsActiveToday->count() }} items
                                    </span>
                                </label>

                                <div class="space-y-3 bg-gray-50 border rounded-xl p-3 max-h-72 overflow-y-auto">

                                    {{-- CLOSED TODAY --}}
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Closed Today</p>

                                    @forelse ($ticketsClosedToday as $t)
                                        <input type="hidden" name="ticket_ids[]" value="{{ $t->id }}">

                                        <div class="p-2 rounded-lg bg-gray-100 border text-sm text-gray-700">
                                            {{ $t->title }} <span class="text-xs text-gray-500">(Closed)</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada ticket closed hari ini</p>
                                    @endforelse

                                    {{-- ACTIVE --}}
                                    <p class="text-xs text-gray-500 font-semibold uppercase mt-3">Open / In Progress</p>

                                    @forelse ($ticketsActiveToday as $t)
                                        <input type="hidden" name="ticket_ids[]" value="{{ $t->id }}">

                                        <div class="p-2 rounded-lg border text-sm text-gray-700
                                            @if($t->status == 'Open') bg-green-50
                                            @elseif($t->status == 'In Progress') bg-yellow-50 @endif">
                                            {{ $t->title }}
                                            <span class="text-xs text-gray-500">({{ ucfirst($t->status) }})</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada ticket aktif</p>
                                    @endforelse
                                </div>

                                <p class="text-xs text-gray-500 mt-1">
                                    Semua ticket di atas otomatis masuk ke laporan.
                                </p>
                            </div>

                        </div>

                        {{-- BUTTON ACTION --}}
                        <div class="flex justify-between items-center pt-4">

                            <a href="{{ route('reports.daily') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                                ← Kembali
                            </a>

                            <button type="button" id="submitReportBtn"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg shadow flex items-center gap-2">

                                <span id="btnText">Submit Laporan</span>

                                <svg id="btnLoading" class="animate-spin h-5 w-5 text-white hidden"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </button>
                        </div>

                    </form>

                    {{-- JS LOADING --}}
                    <script>
                        document.getElementById('submitReportBtn').addEventListener('click', function() {

                            Swal.fire({
                                title: 'Kirim Laporan?',
                                text: "Data tidak dapat diubah setelah dikirim.",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Kirim',
                            }).then((result) => {

                                if (result.isConfirmed) {

                                    document.getElementById('btnText').classList.add('hidden');
                                    document.getElementById('btnLoading').classList.remove('hidden');

                                    document.getElementById('submitReportBtn').disabled = true;

                                    document.getElementById('dailyReportForm').submit();
                                }
                            });
                        });
                    </script>

                </div>
            @endrole

        </div>
    </div>
</x-app-layout>
