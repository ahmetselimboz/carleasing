@php
    $siteTitle = data_get($site, 'title') ?: config('app.name');
    $siteDescription = (string) data_get($site, 'description', '');
    $homeUrl = url('/');
    $logoUrl = data_get($site, 'logo_url');
    $defaultImage = $logoUrl ?: data_get($site, 'placeholder_image_url');
    $contactPhone = trim((string) data_get($site, 'magicbox.contact.phone', ''));
    $contactEmail = trim((string) data_get($site, 'magicbox.contact.email', ''));
    $contactAddress = trim((string) data_get($site, 'magicbox.contact.address', ''));

    $sameAs = collect([
        data_get($site, 'magicbox.social.facebook'),
        data_get($site, 'magicbox.social.twitter'),
        data_get($site, 'magicbox.social.instagram'),
        data_get($site, 'magicbox.social.linkedin'),
        data_get($site, 'magicbox.social.youtube'),
        data_get($site, 'magicbox.social.tiktok'),
    ])->filter(fn ($u) => filled($u))->values()->all();

    $organization = array_filter([
        '@type' => 'Organization',
        '@id' => $homeUrl.'#organization',
        'name' => $siteTitle,
        'url' => $homeUrl,
        'logo' => $logoUrl ? [
            '@type' => 'ImageObject',
            'url' => $logoUrl,
        ] : null,
        'sameAs' => ! empty($sameAs) ? $sameAs : null,
        'contactPoint' => ($contactPhone !== '' || $contactEmail !== '') ? [
            '@type' => 'ContactPoint',
            'telephone' => $contactPhone ?: null,
            'email' => $contactEmail ?: null,
            'contactType' => 'customer service',
            'areaServed' => 'TR',
            'availableLanguage' => ['Turkish'],
        ] : null,
        'address' => $contactAddress !== '' ? [
            '@type' => 'PostalAddress',
            'streetAddress' => $contactAddress,
            'addressCountry' => 'TR',
        ] : null,
    ], fn ($v) => $v !== null);

    $website = [
        '@type' => 'WebSite',
        '@id' => $homeUrl.'#website',
        'url' => $homeUrl,
        'name' => $siteTitle,
        'description' => strip_tags($siteDescription),
        'inLanguage' => 'tr-TR',
        'publisher' => ['@id' => $homeUrl.'#organization'],
    ];

    $graph = [$organization, $website];

    if (! empty($jsonLdAdditional) && is_array($jsonLdAdditional)) {
        foreach ($jsonLdAdditional as $node) {
            if (is_array($node) && ! empty($node)) {
                $graph[] = $node;
            }
        }
    }

    $payload = [
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ];
@endphp

<script type="application/ld+json">{!! json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
