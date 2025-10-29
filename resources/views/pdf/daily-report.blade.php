<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian IT Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        @page {
            margin: 40px 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ccc;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body class="text-gray-800 text-[12px] leading-relaxed">

    {{-- Header --}}
    <div class="text-center border-b-2 border-emerald-700 pb-2 mb-4">
        <h1 class="text-2xl font-bold text-emerald-700">Laporan Harian IT Support</h1>
        <p class="text-gray-600">{{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Informasi Umum --}}
    <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Informasi Umum</h2>
    <table class="text-sm mb-6">
        <tbody>
            <tr>
                <td class="w-1/3 font-medium bg-gray-50 p-2">Nama Support: </td>
                {{-- <td class="w-1/12 text-center font-semibold">:</td> --}}
                <td class="p-2">{{ $report->user->name ?? 'Tidak diketahui' }}</td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Tanggal Laporan: </td>
                {{-- <td class="text-center font-semibold">:</td> --}}
                <td class="p-2">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Status Verifikasi: </td>
                {{-- <td class="text-center font-semibold">:</td> --}}
                <td class="p-2">
                    @if ($report->verified_at)
                        ✅ Terverifikasi oleh {{ $report->verifier->name ?? 'N/A' }}
                        ({{ \Carbon\Carbon::parse($report->verified_at)->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }}
                        WITA)
                    @else
                        ⚠️ Belum diverifikasi
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Tugas yang Dikerjakan --}}
    <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Tugas Harian yang Sudah
        Dikerjakan</h2>
    <table class="text-sm mb-6">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="p-2 text-center w-10">No</th>
                <th class="p-2 text-left">Deskripsi Tugas</th>
                <th class="p-2 text-center w-32">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->tasks as $i => $task)
                <tr>
                    <td class="p-2 text-center">{{ $i + 1 }}</td>
                    <td class="p-2">{{ $task->title }}</td>
                    <td class="p-2 text-center">
                        {{ \Carbon\Carbon::parse($task->created_at)->setTimezone('Asia/Makassar')->translatedFormat('H:i') }}
                        WITA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-3 text-center text-gray-500 italic">Tidak ada tugas tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tiket yang Ditangani --}}
    <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Ticket yang Sudah Ditangani
    </h2>
    <table class="text-sm mb-8">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="p-2 text-center w-10">No</th>
                <th class="p-2 text-left">Judul Tiket</th>
                <th class="p-2 text-center w-24">Status</th>
                <th class="p-2 text-left">Detail</th>
                <th class="p-2 text-left">Solusi</th>
                <th class="p-2 text-center w-24">Prioritas</th>
                <th class="p-2 text-center w-32">Diselesaikan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->tickets as $i => $ticket)
                <tr>
                    <td class="p-2 text-center">{{ $i + 1 }}</td>
                    <td class="p-2">{{ $ticket->title }}</td>
                    <td class="p-2 text-center">
                        @switch($ticket->status)
                            @case('Open')
                                <span class="text-red-600 font-semibold">Open</span>
                            @break

                            @case('In Progress')
                                <span class="text-yellow-600 font-semibold">In Progress</span>
                            @break

                            @case('Closed')
                                <span class="text-green-600 font-semibold">Closed</span>
                            @break

                            @default
                                <span class="text-gray-600">{{ $ticket->status }}</span>
                        @endswitch
                    </td>
                    <td class="p-2">{{ $ticket->description ?? '-' }}</td>
                    <td class="p-2">{{ $ticket->solution ?? '-' }}</td>
                    <td class="p-2 text-center">{{ $ticket->priority ?? '-' }}</td>
                    <td class="p-2 text-center">{{ $report->verifier->name ?? '-' }}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-3 text-center text-gray-500 italic">Tidak ada tiket ditangani.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Catatan / Isi Laporan --}}
        @if (!empty($report->content))
            <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Catatan / Isi Laporan</h2>
            <p class="text-gray-700 text-sm whitespace-pre-line">{{ $report->content }}</p>
        @endif

        {{-- Footer --}}
        <div class="footer">
            Dicetak oleh {{ auth()->user()->name }} |
            {{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
        </div>

    </body>

    </html>
