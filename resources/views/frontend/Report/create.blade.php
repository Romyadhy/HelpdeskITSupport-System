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

                <form method="POST" action="{{ route('reports.daily.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Deskripsi Laporan</label>
                        <textarea name="content" class="w-full border-gray-300 rounded-lg" rows="3" placeholder="Tuliskan kegiatan hari ini..." required></textarea>
                    </div>

                    {{-- Tasks (Completed Today) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-gray-700">Tugas yang Dikerjakan (Hari Ini)</label>
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                {{ $tasksCompletedToday->count() }} items
                            </span>
                        </div>

                        <select name="task_ids[]" class="w-full border-gray-300 rounded-lg" multiple size="6">
                            @forelse ($tasksCompletedToday as $task)
                                <option value="{{ $task->id }}" selected>{{ $task->title }}</option>
                            @empty
                                <option disabled>Tidak ada task completed hari ini</option>
                            @endforelse
                        </select>
                        <p class="text-xs text-gray-500 mt-1">* Multi-select (Ctrl/Cmd untuk pilih banyak)</p>
                    </div>

                    {{-- Tickets (Closed & In Progress Today) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="font-semibold text-gray-700">Ticket yang Ditangani (Hari Ini)</label>
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                {{ $ticketsClosedToday->count() + $ticketsActiveToday->count() }} items
                            </span>
                        </div>

                        <select name="ticket_ids[]" class="w-full border-gray-300 rounded-lg" multiple size="8">
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
                        <p class="text-xs text-gray-500 mt-1">* Closed today otomatis terpilih; uncheck jika tidak ingin dilaporkan</p>
                    </div>

                    <x-primary-button>Submit Laporan</x-primary-button>
                </form>
            </div>
            @endrole

            {{-- LIST REPORT --}}
            
        </div>
    </div>
</x-app-layout>
