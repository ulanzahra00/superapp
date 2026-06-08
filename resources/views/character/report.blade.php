<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .score { font-size: 28px; font-weight: bold; margin-top: 14px; }
    </style>
</head>
<body>
    <h1>Laporan Karakter Siswa</h1>
    <p>Nama: {{ $student->name }}</p>
    <p>Kelas: {{ $student->class_name ?? '-' }}</p>
    <p>Orang tua: {{ optional($student->parent)->name ?? '-' }}</p>
    <div class="score">Total poin: {{ $total }}</div>
    <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Poin</th><th>Deskripsi</th></tr></thead>
        <tbody>
            @foreach($student->studentPoints as $point)
                <tr>
                    <td>{{ $point->occurred_at->format('d M Y') }}</td>
                    <td>{{ ucfirst($point->type) }}</td>
                    <td>{{ $point->category }}</td>
                    <td>{{ $point->point }}</td>
                    <td>{{ $point->title }}. {{ $point->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <thead><tr><th>Tanggal</th><th>Sanksi</th><th>Total Poin</th><th>Catatan</th></tr></thead>
        <tbody>
            @foreach($student->sanctions as $sanction)
                <tr>
                    <td>{{ $sanction->created_at->format('d M Y') }}</td>
                    <td>{{ $sanction->sanction_type }}</td>
                    <td>{{ $sanction->total_points }}</td>
                    <td>{{ $sanction->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

