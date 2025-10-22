<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 Detail Laporan Harian
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header Info --}}
            <div class="bg-white shadow rounded-xl p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $report->user->name ?? 'Unknown User' }}
                    </h3>
                    <p class="text-gray-500 text-sm">
                        {{ \Carbon\Carbon::parse($report->report_date)->format('l, d F Y') }}
                    </p>
                </div>

                @if($report->verified_at)
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        ✅ Diverifikasi oleh {{ $report->verifier->name ?? 'N/A' }}
                    </span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full">
                        ⏳ Belum Diverifikasi
                    </span>
                @endif
            </div>

            {{-- Konten Laporan --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Deskripsi Laporan</h4>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $report->content }}
                </p>
            </div>

            {{-- Daftar Tugas --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">🧩 Tugas yang Dikerjakan</h4>

                @if($report->tasks->count() > 0)
                    <ul class="list-disc pl-6 space-y-2 text-gray-700">
                        @foreach($report->tasks as $task)
                            <li>
                                <span class="font-medium">{{ $task->title }}</span>
                                <p class="text-sm text-gray-500">{{ $task->description ?? '-' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm">Belum ada tugas yang dilaporkan.</p>
                @endif
            </div>

            {{-- Daftar Ticket --}}
            <div class="bg-white p-6 rounded-xl shadow">
                <h4 class="text-lg font-semibold text-gray-700 mb-3">🎫 Tiket yang Ditangani</h4>

                @if($report->tickets->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($report->tickets as $ticket)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $ticket->title }}</p>
                                        <p class="text-sm text-gray-500">{{ Str::limit($ticket->description, 100) }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($ticket->status === 'Closed') bg-green-100 text-green-700
                                        @elseif($ticket->status === 'In Progress') bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm">Tidak ada tiket yang terhubung.</p>
                @endif
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('reports.daily') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 font-semibold shadow">
                    ← Kembali
                </a>

                @if(!$report->verified_at && Auth::user()->hasRole('admin'))
                    <form action="{{ route('reports.daily.verify', $report->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow">
                            ✅ Verifikasi Laporan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
