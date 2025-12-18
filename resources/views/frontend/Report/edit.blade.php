
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ✏️ Edit Laporan Harian
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @role('support')
                <div class="bg-white shadow-lg rounded-2xl p-8 space-y-8">

                    {{-- TOP BAR --}}
                    <div class="flex items-center justify-between">
                        <div class="text-gray-700 font-semibold text-lg">
                            Edit Laporan Harian
                        </div>

                        @if($report->verified_at)
                            <span class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-full">
                                Sudah Diverifikasi
                            </span>
                        @else
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
                                Belum Diverifikasi
                            </span>
                        @endif
                    </div>

                    {{-- SWEETALERT --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
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

                    <form id="editDailyReportForm"
                          method="POST"
                          action="{{ route('reports.daily.update', $report) }}"
                          class="space-y-8">

                        @csrf
                        @method('PUT')

                        {{-- DESKRIPSI --}}
                        <div class="space-y-2">
                            <label class="font-semibold text-gray-700">Deskripsi Laporan</label>

                            <textarea name="content"
                                      rows="4"
                                      required
                                      class="w-full border-gray-300 rounded-xl focus:ring focus:ring-emerald-300 p-3">{{ old('content', $report->content) }}</textarea>

                            <p class="text-xs text-gray-500">
                                Kamu masih bisa mengedit laporan sebelum diverifikasi admin.
                            </p>
                        </div>

                        {{-- GRID --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- TASKS --}}
                            <div>
                                <label class="font-semibold text-gray-700 flex justify-between mb-2">
                                    <span>Tugas Diselesaikan</span>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        {{ $tasksCompletedToday->count() }} items
                                    </span>
                                </label>

                                <div class="space-y-2 bg-gray-50 border rounded-xl p-3 max-h-64 overflow-y-auto">
                                    @forelse ($tasksCompletedToday as $task)
                                        <label class="flex items-center gap-2 text-sm text-gray-700 bg-white p-2 rounded border">
                                            <input type="checkbox"
                                                   name="task_ids[]"
                                                   value="{{ $task->id }}"
                                                   checked
                                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            {{ $task->title }}
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada task hari ini</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- TICKETS --}}
                            <div>
                                <label class="font-semibold text-gray-700 flex justify-between mb-2">
                                    <span>Ticket Ditangani</span>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        {{ $ticketsClosedToday->count() + $ticketsActiveToday->count() }} items
                                    </span>
                                </label>

                                <div class="space-y-3 bg-gray-50 border rounded-xl p-3 max-h-72 overflow-y-auto">

                                    {{-- CLOSED --}}
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Closed Today</p>

                                    @forelse ($ticketsClosedToday as $t)
                                        <label class="flex items-center gap-2 text-sm bg-gray-100 p-2 rounded border">
                                            <input type="checkbox"
                                                   name="ticket_ids[]"
                                                   value="{{ $t->id }}"
                                                   checked
                                                   class="rounded border-gray-300 text-emerald-600">
                                            {{ $t->title }}
                                            <span class="text-xs text-gray-500">(Closed)</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada ticket closed</p>
                                    @endforelse

                                    {{-- ACTIVE --}}
                                    <p class="text-xs text-gray-500 font-semibold uppercase mt-3">
                                        Open / In Progress
                                    </p>

                                    @forelse ($ticketsActiveToday as $t)
                                        <label class="flex items-center gap-2 text-sm p-2 rounded border
                                            @if($t->status === 'Open') bg-green-50
                                            @elseif($t->status === 'In Progress') bg-yellow-50
                                            @endif">
                                            <input type="checkbox"
                                                   name="ticket_ids[]"
                                                   value="{{ $t->id }}"
                                                   checked
                                                   class="rounded border-gray-300 text-emerald-600">
                                            {{ $t->title }}
                                            <span class="text-xs text-gray-500">({{ ucfirst($t->status) }})</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 italic">Tidak ada ticket aktif</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex justify-between items-center pt-4">

                            <a href="{{ route('reports.daily.show', $report) }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                                ← Kembali
                            </a>

                            <button type="button"
                                    id="updateReportBtn"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg shadow flex items-center gap-2">

                                <span id="btnText">Update Laporan</span>

                                <svg id="btnLoading"
                                     class="animate-spin h-5 w-5 text-white hidden"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25"
                                            cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75"
                                          fill="currentColor"
                                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- JS CONFIRM --}}
                    <script>
                        document.getElementById('updateReportBtn').addEventListener('click', function () {
                            Swal.fire({
                                title: 'Update Laporan?',
                                text: 'Perubahan masih bisa dilakukan sebelum diverifikasi admin.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Update',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('btnText').classList.add('hidden');
                                    document.getElementById('btnLoading').classList.remove('hidden');
                                    document.getElementById('editDailyReportForm').submit();
                                }
                            });
                        });
                    </script>

                </div>
            @endrole

        </div>
    </div>
</x-app-layout>
