<!doctype html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Rapor Ciktisi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1, h2 { margin: 0 0 8px 0; }
        .muted { color: #6b7280; font-size: 11px; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .grid th, .grid td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        .grid th { background: #f8fafc; }
        .kpi { margin-top: 10px; }
        .kpi span { margin-right: 16px; }
    </style>
</head>

<body>
    <h1>Raporlar</h1>
    <p class="muted">Donem: {{ $filters['range_human'] }} | Grup: {{ $filters['group_by'] }}</p>

    <p class="kpi">
        <span><strong>Toplam lead:</strong> {{ number_format($kpis['total_leads'], 0, ',', '.') }}</span>
        <span><strong>Kiralama:</strong> {{ number_format($kpis['rental_requests'], 0, ',', '.') }}</span>
        <span><strong>Mesaj:</strong> {{ number_format($kpis['messages'], 0, ',', '.') }}</span>
        <span><strong>Geri arama:</strong> {{ number_format($kpis['callback_requests'], 0, ',', '.') }}</span>
    </p>

    <h2>Zaman dagilimi</h2>
    <table class="grid">
        <thead>
            <tr>
                <th>Donem</th>
                <th>Toplam</th>
                <th>Kiralama</th>
                <th>Mesaj</th>
                <th>Geri arama</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeline as $row)
                <tr>
                    <td>{{ $row['bucket'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['rental'] }}</td>
                    <td>{{ $row['message'] }}</td>
                    <td>{{ $row['callback'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Ilk 20 lead kaydi</h2>
    <table class="grid">
        <thead>
            <tr>
                <th>Tip</th>
                <th>Tarih</th>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>Şehir</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leadRows->take(20) as $row)
                <tr>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['phone'] }}</td>
                    <td>{{ $row['city'] }}</td>
                    <td>{{ $row['read_status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
