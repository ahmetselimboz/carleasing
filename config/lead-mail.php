<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lead Mail Notifications
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar kiralama talebi / iletisim mesaji / geri arama talebi
    | olustugunda gonderilecek otomatik e-postalari kontrol eder.
    |
    */
    'enabled' => (bool) env('LEAD_MAIL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Yonetim Bildirim Rolleri
    |--------------------------------------------------------------------------
    |
    | Desteklenen degerler: super_admin, admin, customer_service
    |
    */
    'notify_roles' => array_values(array_filter(array_map(
        static fn (string $item): string => trim($item),
        explode(',', (string) env('LEAD_MAIL_ADMIN_ROLES', 'super_admin,admin,customer_service'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Tek Adrese Yonlendirme (Opsiyonel)
    |--------------------------------------------------------------------------
    |
    | Lokal / staging ortamlarda tum mailleri tek inbox'a dusurmek icin:
    | LEAD_MAIL_FORCE_TO=test@example.com
    |
    */
    'force_to' => env('LEAD_MAIL_FORCE_TO'),
];
