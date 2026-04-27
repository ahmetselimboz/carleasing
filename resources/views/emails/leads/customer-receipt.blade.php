<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $payload['type_label'] }} - Talebiniz alindi</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;">
        <tr>
            <td style="padding:24px;">
                <h1 style="margin:0 0 12px;font-size:20px;">Talebiniz bize ulasti</h1>
                <p style="margin:0 0 14px;line-height:1.6;">
                    Merhaba {{ $payload['name'] }},
                    <strong>{{ mb_strtolower((string) $payload['type_label']) }}</strong> kaydinizi aldik.
                    Ekibimiz en kisa surede inceleyip sizinle iletisime gececektir.
                </p>
                <p style="margin:0 0 4px;font-size:14px;color:#334155;"><strong>Talep No:</strong> #{{ $payload['lead_id'] }}</p>
                <p style="margin:0 0 20px;font-size:14px;color:#334155;"><strong>Tarih:</strong> {{ $payload['created_at'] }}</p>
                <p style="margin:0;font-size:13px;color:#64748b;">Bu e-posta bilgilendirme amaclidir.</p>
            </td>
        </tr>
    </table>
</body>
</html>
