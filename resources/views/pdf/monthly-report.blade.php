<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan IT Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        @page { margin: 40px 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; }
        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            text-align: center; font-size: 10px; color: #6b7280;
        }
    </style>
</head>
<body class="text-gray-800 text-[12px] leading-relaxed">

    {{-- Header --}}
    <div class="text-center border-b-2 border-emerald-700 pb-2 mb-4">
        <h1 class="text-2xl font-bold text-emerald-700">Laporan Bulanan IT Support</h1>
        <p class="text-gray-600">
            Periode: {{ $report->month }} {{ $report->year }} &middot;
            Dicetak: {{ $today->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- Informasi Umum --}}
    <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Informasi Umum</h2>
    <table class="text-sm mb-6">
        <tbody>
            <tr>
                <td class="w-1/3 font-medium bg-gray-50 p-2">Nama</td>
                <td class="w-1/12 text-center font-semibold">:</td>
                <td class="p-2">{{ $report->user->name ?? 'Tidak diketahui' }}</td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Periode</td>
                <td class="text-center font-semibold">:</td>
                <td class="p-2">{{ $report->month }} {{ $report->year }}</td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Tanggal Dibuat</td>
                <td class="text-center font-semibold">:</td>
                <td class="p-2">
                    {{ \Carbon\Carbon::parse($report->report_date)->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
                </td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Status Verifikasi</td>
                <td class="text-center font-semibold">:</td>
                <td class="p-2">
                    @if ($report->verified_at)
                        ✅ Terverifikasi oleh {{ $report->verifier->name ?? 'N/A' }}
                        ({{ \Carbon\Carbon::parse($report->verified_at)->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA)
                    @else
                        ⚠️ Belum diverifikasi
                    @endif
                </td>
            </tr>
            <tr>
                <td class="font-medium bg-gray-50 p-2">Ringkasan Statistik</td>
                <td class="text-center font-semibold">:</td>
                <td class="p-2">
                    {{ $report->total_days_reported ?? 0 }} hari dilaporkan &middot;
                    {{ $report->total_tasks ?? 0 }} tugas &middot;
                    {{ $report->total_tickets ?? 0 }} tiket
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Ringkasan Laporan --}}
    @if (!empty($report->content))
        <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Ringkasan Laporan</h2>
        <div class="p-3 border rounded mb-6 bg-gray-50 text-gray-700">
            {!! nl2br(e($report->content)) !!}
        </div>
    @endif

    {{-- Rincian Laporan Harian --}}
    <h2 class="text-lg font-semibold text-emerald-700 border-b border-gray-300 pb-1 mb-2">Rincian Laporan Harian</h2>
    <table class="text-sm mb-8">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="p-2 text-center w-10">No</th>
                <th class="p-2 text-left w-32">Tanggal</th>
                <th class="p-2 text-left">Ringkasan</th>
                <th class="p-2 text-center w-20">Tugas</th>
                <th class="p-2 text-center w-20">Tiket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyReports as $idx => $d)
                <tr>
                    <td class="p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="p-2">
                        {{ \Carbon\Carbon::parse($d->report_date)->translatedFormat('d M Y') }}
                    </td>
                    <td class="p-2">{{ \Illuminate\Support\Str::limit($d->content, 120) }}</td>
                    <td class="p-2 text-center">{{ $d->tasks->count() }}</td>
                    <td class="p-2 text-center">{{ $d->tickets->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-3 text-center text-gray-500 italic">Tidak ada laporan harian terkait.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Dicetak oleh {{ auth()->user()->name }} ·
        {{ now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
    </div>

</body>
</html>
