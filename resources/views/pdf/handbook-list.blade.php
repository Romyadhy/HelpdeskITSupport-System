<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Handbook IT Support</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { color: #2c3e50; text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background-color: #f0f0f0; text-align: left; }
        .footer { text-align: center; font-size: 10px; margin-top: 20px; color: #777; }
    </style>
</head>
<body>
    <h2>📘 Daftar Handbook & SOP IT Support</h2>
    <p><strong>Tanggal Export:</strong> {{ $exported_at->format('d M Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Uploader</th>
                <th>Tanggal Upload</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($handbooks as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ Str::limit($item->description, 100) }}</td>
                <td>{{ $item->uploader->name ?? '-' }}</td>
                <td>{{ $item->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} IT Support Handbook System — Generated automatically.
    </div>
</body>
</html>
