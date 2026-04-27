<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni {{ $payload['type_label'] }}</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;">
        <tr>
            <td style="padding:24px;">
                <h1 style="margin:0 0 12px;font-size:20px;">Yeni {{ mb_strtolower((string) $payload['type_label']) }} olustu</h1>
                <p style="margin:0 0 14px;line-height:1.6;">
                    Sisteme yeni bir kayit geldi. Hizli aksiyon icin detaylar asagidadir.
                </p>
                <p style="margin:0 0 4px;font-size:14px;color:#334155;"><strong>Talep No:</strong> #{{ $payload['lead_id'] }}</p>
                <p style="margin:0 0 4px;font-size:14px;color:#334155;"><strong>Ad Soyad:</strong> {{ $payload['name'] }}</p>
                <p style="margin:0 0 4px;font-size:14px;color:#334155;"><strong>E-posta:</strong> {{ $payload['email'] ?: '-' }}</p>
                <p style="margin:0 0 16px;font-size:14px;color:#334155;"><strong>Telefon:</strong> {{ $payload['phone'] ?: '-' }}</p>
                <a href="{{ $payload['admin_url'] }}"
                    style="display:inline-block;padding:10px 14px;border-radius:8px;background:#37008a;color:#ffffff;text-decoration:none;font-size:14px;">
                    Panelde Goruntule
                </a>
            </td>
        </tr>
    </table>
</body>
</html>
