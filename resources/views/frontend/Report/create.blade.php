<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🗓️ Daily Reports</h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- FORM: hanya SUPPORT --}}
            @role('support')
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">📝 Buat Laporan Harian</h3>

                    {{-- SweetAlert Notifications --}}
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            @if (session('success'))
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: '{{ session('success') }}',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            @endif

                            @if (session('warning'))
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan!',
                                    text: '{{ session('warning') }}',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#facc15'
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

                    {{-- Form --}}
                    <form id="dailyReportForm" method="POST" action="{{ route('reports.daily.store') }}" class="space-y-6">
                        @csrf

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Deskripsi Laporan</label>
                            <textarea name="content" class="w-full border-gray-300 rounded-lg focus:ring focus:ring-emerald-300 p-2" rows="3"
                                placeholder="Tuliskan kegiatan hari ini..." required></textarea>
                            <p class="text-xs text-gray-500 mt-1">Tuliskan ringkasan pekerjaanmu hari ini secara singkat dan
                                jelas.</p>
                        </div>

                        {{-- Tasks (Completed Today) --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-semibold text-gray-700">Tugas yang Dikerjakan (Hari Ini)</label>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                    {{ $tasksCompletedToday->count() }} items
                                </span>
                            </div>

                            <select name="task_ids[]"
                                class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-300 p-2" multiple
                                size="6">
                                @forelse ($tasksCompletedToday as $task)
                                    <option value="{{ $task->id }}" selected>{{ $task->title }}</option>
                                @empty
                                    <option disabled>Tidak ada task completed hari ini</option>
                                @endforelse
                            </select>
                            <p class="text-xs text-gray-500 mt-1">* Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih
                                dari satu</p>
                        </div>

                        {{-- Tickets (Closed & In Progress Today) --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-semibold text-gray-700">Ticket yang Ditangani (Hari Ini)</label>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                    {{ $ticketsClosedToday->count() + $ticketsActiveToday->count() }} items
                                </span>
                            </div>

                            <select name="ticket_ids[]"
                                class="w-full border-gray-300 rounded-lg focus:ring focus:ring-blue-300 p-2" multiple
                                size="8">
                                <optgroup label="Closed Today">
                                    @forelse ($ticketsClosedToday as $t)
                                        <option value="{{ $t->id }}" selected>
                                            {{ $t->title }} (Closed)
                                        </option>
                                    @empty
                                        <option disabled>— Tidak ada ticket closed hari ini —</option>
                                    @endforelse
                                </optgroup>

                                <optgroup label="On Progress / Open Today">
                                    @forelse ($ticketsActiveToday as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->title }} ({{ ucfirst($t->status) }})
                                        </option>
                                    @empty
                                        <option disabled>— Tidak ada ticket aktif yang diupdate hari ini —</option>
                                    @endforelse
                                </optgroup>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">* Closed today otomatis terpilih; uncheck jika tidak ingin
                                dilaporkan</p>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="button" id="submitReportBtn"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
                                Submit Laporan
                            </button>
                        </div>
                    </form>

                    {{-- SweetAlert Confirm --}}
                    <script>
                        document.getElementById('submitReportBtn').addEventListener('click', function(e) {
                            e.preventDefault();

                            Swal.fire({
                                title: 'Kirim Laporan Harian?',
                                text: "Pastikan semua data sudah benar sebelum dikirim.",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Kirim',
                                cancelButtonText: 'Batal',
                                confirmButtonColor: '#10b981',
                                cancelButtonColor: '#6b7280',
                            }).then((result) => {
                                if (result.isConfirmed) {
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
