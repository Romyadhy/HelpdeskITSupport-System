<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian IT Support</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            color: #000;
            line-height: 1.6;
        }

        @page {
            margin: 40px 35px;
        }

        h2, h3 {
            font-weight: bold;
            margin-bottom: 6px;
        }

        h2 {
            font-size: 22px;
        }

        h3 {
            font-size: 16px;
            margin-top: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        table th {
            text-align: center;
        }

        .section-table td:first-child {
            width: 28%;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -5px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #444;
        }

        .text-center { text-align: center; }
        .italic { font-style: italic; }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div style="text-align:center; margin-bottom:20px;">
        <h2>Laporan Harian IT Support</h2>
        <div style="font-size: 14px;">
            {{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- INFORMASI UMUM -->
    <h3>Informasi Umum</h3>

    <table class="section-table">
        <tbody>
            <tr>
                <td>Nama Support</td>
                <td>{{ $report->user->name ?? 'Tidak diketahui' }}</td>
            </tr>
            <tr>
                <td>Tanggal Laporan</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td>Status Verifikasi</td>
                <td>
                    @if ($report->verified_at)
                        Terverifikasi oleh <b>{{ $report->verifier->name ?? 'N/A' }}</b><br>
                        ({{ \Carbon\Carbon::parse($report->verified_at)->timezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA)
                    @else
                        <span class="italic">Belum diverifikasi</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- TUGAS HARIAN -->
    <h3>Tugas Harian yang Sudah Dikerjakan</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Deskripsi Tugas</th>
                <th style="width: 90px;">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->tasks as $i => $task)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $task->title }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($task->created_at)->timezone('Asia/Makassar')->translatedFormat('H:i') }} WITA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center italic">Tidak ada tugas tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TIKET -->
    <h3>Ticket yang Sudah Ditangani</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Judul Tiket</th>
                <th style="width: 70px;">Status</th>
                <th>Detail</th>
                <th>Solusi</th>
                <th style="width: 70px;">Prioritas</th>
                <th style="width: 120px;">Diselesaikan Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->tickets as $i => $ticket)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td class="text-center">{{ $ticket->status }}</td>
                    <td>{{ $ticket->description ?? '-' }}</td>
                    <td>{{ $ticket->solution ?? '-' }}</td>
                    <td class="text-center">{{ $ticket->priority ?? '-' }}</td>
                    <td class="text-center">{{ $ticket->solver->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center italic">Tidak ada tiket ditangani.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- CATATAN -->
    @if (!empty($report->content))
        <h3>Catatan / Isi Laporan</h3>
        <p style="white-space: pre-line;">
            {{ $report->content }}
        </p>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Dicetak oleh {{ auth()->user()->name }} |
        {{ \Carbon\Carbon::now()->timezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
    </div>

</body>
</html>
