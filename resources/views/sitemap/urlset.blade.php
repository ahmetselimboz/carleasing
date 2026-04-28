<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        @isset($url['lastmod'])<lastmod>{{ $url['lastmod'] }}</lastmod>@endisset
        @isset($url['changefreq'])<changefreq>{{ $url['changefreq'] }}</changefreq>@endisset
        @isset($url['priority'])<priority>{{ $url['priority'] }}</priority>@endisset
        @isset($url['images'])
            @foreach ($url['images'] as $img)
                <image:image>
                    <image:loc>{{ $img['loc'] }}</image:loc>
                    @isset($img['title'])<image:title>{{ $img['title'] }}</image:title>@endisset
                </image:image>
            @endforeach
        @endisset
    </url>
@endforeach
</urlset>
