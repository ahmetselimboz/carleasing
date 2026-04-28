@php
    use Illuminate\Support\Str;

    $siteTitle = data_get($site, 'title') ?: config('app.name');
    $siteDescription = (string) data_get($site, 'description', '');
    $defaultDescription = (string) data_get($site, 'magicbox.seo.default_meta_description', $siteDescription);
    $keywords = trim((string) data_get($site, 'magicbox.seo.meta_keywords', ''));
    $allowIndex = (bool) data_get($site, 'magicbox.seo.allow_indexing', true);
    $maintenance = (bool) data_get($site, 'maintenance_mode', false);

    $rawTitle = trim((string) ($title ?? ''));
    $fullTitle = $rawTitle === ''
        ? $siteTitle
        : ($rawTitle === $siteTitle ? $siteTitle : $rawTitle.' | '.$siteTitle);

    $rawDescription = trim((string) ($description ?? $defaultDescription));
    $metaDescription = Str::limit(strip_tags($rawDescription), 160);

    $canonicalUrl = $canonical ?? url()->current();

    $imageUrl = $image ?? data_get($site, 'magicbox.seo.og_image_url')
        ?? data_get($site, 'logo_url')
        ?? data_get($site, 'placeholder_image_url');

    $ogType = $ogType ?? 'website';
    $robots = $robots ?? null;
    if ($robots === null) {
        $robots = ($allowIndex && ! $maintenance && (! isset($noindex) || ! $noindex))
            ? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'
            : 'noindex,nofollow';
    }

    $locale = (string) (data_get($site, 'magicbox.seo.locale') ?: str_replace('_', '-', app()->getLocale()));
    $ogLocale = (string) (data_get($site, 'magicbox.seo.og_locale') ?: 'tr_TR');

    $twitterCard = $twitterCard ?? 'summary_large_image';
    $twitterSite = trim((string) data_get($site, 'magicbox.seo.twitter_handle', ''));

    $gscVerification = trim((string) data_get($site, 'magicbox.seo.google_site_verification', ''));
    $bingVerification = trim((string) data_get($site, 'magicbox.seo.bing_site_verification', ''));
    $yandexVerification = trim((string) data_get($site, 'magicbox.seo.yandex_verification', ''));

    $contactPhone = trim((string) data_get($site, 'magicbox.contact.phone', ''));
    $contactEmail = trim((string) data_get($site, 'magicbox.contact.email', ''));
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if ($keywords !== '')
    <meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="robots" content="{{ $robots }}">
<meta name="googlebot" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteTitle }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="{{ $ogLocale }}">
@if ($imageUrl)
    <meta property="og:image" content="{{ $imageUrl }}">
    <meta property="og:image:secure_url" content="{{ $imageUrl }}">
    <meta property="og:image:alt" content="{{ $fullTitle }}">
@endif

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($imageUrl)
    <meta name="twitter:image" content="{{ $imageUrl }}">
@endif
@if ($twitterSite !== '')
    <meta name="twitter:site" content="{{ Str::startsWith($twitterSite, '@') ? $twitterSite : '@'.$twitterSite }}">
@endif

@if ($gscVerification !== '')
    <meta name="google-site-verification" content="{{ $gscVerification }}">
@endif

@if (! empty($prevUrl))
    <link rel="prev" href="{{ $prevUrl }}">
@endif
@if (! empty($nextUrl))
    <link rel="next" href="{{ $nextUrl }}">
@endif

@if (! empty($alternates) && is_array($alternates))
    @foreach ($alternates as $hreflang => $altUrl)
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $altUrl }}">
    @endforeach
@endif
