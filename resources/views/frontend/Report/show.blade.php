<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📝 Detail Laporan Harian
            </h2>
            <a href="{{ route('reports.daily') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Card 1: Informasi Utama Laporan --}}
            <div class="bg-white shadow-md rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <p class="text-sm text-gray-500">Laporan Oleh:</p>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $report->user->name ?? 'Pengguna Tidak Dikenal' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        Tanggal Laporan: {{ $report->report_date->format('l, d F Y') }}
                    </p>
                    <p class="text-xs text-gray-400">
                        Dikirim pada: {{ $report->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 text-sm text-right">
                    @if ($report->verified_at)
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium">
                            <i class="fas fa-check-circle mr-1.5"></i>
                            Diverifikasi oleh {{ $report->verifier->name ?? 'N/A' }}
                        </span>
                        <span class="block text-xs text-gray-400 mt-1">
                            pada {{ $report->verified_at->format('d M Y, H:i') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">
                            <i class="fas fa-clock mr-1.5"></i>
                            Menunggu Verifikasi
                        </span>
                    @endif
                </div>
            </div>

            {{-- Card 2: Konten Laporan --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h4 class="text-base font-semibold text-gray-700  mb-3 border-b pb-2">
                    <i class="fas fa-file-alt mr-2 text-indigo-500"></i>Isi Laporan
                </h4>
                <div class="prose prose-sm text-gray-700  leading-relaxed whitespace-pre-line">
                    {{ $report->content }}
                </div>
            </div>

            {{-- Card 3: Tugas yang Dilaporkan Selesai --}}
            <div class="bg-white  shadow-md rounded-lg p-6">
                 <h4 class="text-base font-semibold text-gray-700 mb-3 border-b pb-2">
                    <i class="fas fa-tasks mr-2 text-blue-500"></i>Tugas Harian yang Diselesaikan
                </h4>
                @if ($report->tasks && $report->tasks->count() > 0)
                    <ul class="space-y-2">
                        @foreach ($report->tasks as $task)
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mr-2"></i> {{ $task->title }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada tugas rutin yang dilaporkan selesai.</p>
                @endif
            </div>

            {{-- Card 4: Tiket yang Ditangani --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                 <h4 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">
                     <i class="fas fa-ticket-alt mr-2 text-orange-500"></i>Tiket yang Ditangani
                 </h4>
                 @if ($report->tickets && $report->tickets->count() > 0)
                    <div class="space-y-4">
                        @foreach ($report->tickets as $ticket)
                            <div class="border rounded p-3 bg-gray-50 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-800">#{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} - {{ $ticket->title }}</span>
                                    <span @class([
                                        'px-2 py-0.5 text-xs font-semibold rounded-full',
                                        'bg-red-100 text-red-600' => $ticket->status === 'Open',
                                        'bg-yellow-100 text-yellow-700' => $ticket->status === 'In Progress',
                                        'bg-green-100 text-green-700' => $ticket->status === 'Closed',
                                        'bg-purple-100 text-purple-700' => $ticket->status === 'Escalated',
                                    ])>
                                        {{ $ticket->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Prioritas: {{ $ticket->priority }}</p>
                            </div>
                        @endforeach
                    </div>
                 @else
                    <p class="text-sm text-gray-500 italic">Tidak ada tiket yang dilaporkan ditangani.</p>
                 @endif
            </div>

            {{-- Card 5: Tombol Aksi (Verifikasi oleh Admin) --}}
            {{-- Tampilkan tombol ini HANYA jika laporan belum diverifikasi DAN user adalah admin --}}
            @can('verify-daily-report')
                 @if (!$report->verified_at)
                     <div class="bg-white shadow-md rounded-lg p-6 text-center">
                         <p class="text-sm text-gray-600 mb-4">Laporan ini belum diverifikasi.</p>
                         <form action="{{ route('reports.daily.verify', $report->id) }}" method="POST">
                             @csrf
                             @method('PUT') {{-- Gunakan PUT untuk aksi update/verifikasi --}}
                             <button type="submit"
                                 class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                                 <i class="fas fa-check-circle mr-2"></i> Verifikasi Laporan Ini
                             </button>
                         </form>
                     </div>
                 @endif
            @endcan

        </div>
    </div>
</x-app-layout>