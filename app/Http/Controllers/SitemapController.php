<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController
{
    public function index(): Response
    {
        $now = now();
        $latestCar = Car::query()->where('is_active', true)->max('updated_at');
        $latestPage = Page::query()->where('is_active', true)->max('updated_at');

        $sitemaps = [
            ['loc' => route('sitemap.static'), 'lastmod' => $now->toAtomString()],
            ['loc' => route('sitemap.cars'), 'lastmod' => Carbon::parse($latestCar ?: $now)->toAtomString()],
            ['loc' => route('sitemap.pages'), 'lastmod' => Carbon::parse($latestPage ?: $now)->toAtomString()],
        ];

        return response()->view('sitemap.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function staticUrls(): Response
    {
        $now = now()->toAtomString();
        $urls = [
            ['loc' => route('home'), 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('public.references.index'), 'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function cars(): Response
    {
        $cars = Car::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'slug', 'image', 'title', 'updated_at']);

        $urls = $cars->map(function (Car $car) {
            $row = [
                'loc' => route('cars.show', $car->slug),
                'lastmod' => Carbon::parse($car->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
            if ($url = $car->displayImageUrl()) {
                $row['images'] = [
                    [
                        'loc' => $url,
                        'title' => $car->title,
                    ],
                ];
            }

            return $row;
        })->all();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function pages(): Response
    {
        $pages = Page::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at']);

        $urls = $pages->map(function (Page $page) {
            return [
                'loc' => route('public.pages.show', $page->slug),
                'lastmod' => Carbon::parse($page->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        })->all();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots(): Response
    {
        $allow = (bool) data_get(Setting::cachedForViews(), 'magicbox.seo.allow_indexing', true);
        $maintenance = (bool) data_get(Setting::cachedForViews(), 'maintenance_mode', false);

        $lines = ['User-agent: *'];

        if (! $allow || $maintenance) {
            $lines[] = 'Disallow: /';
        } else {
            $lines[] = 'Disallow: /manage/';
            $lines[] = 'Disallow: /listem';
            $lines[] = 'Disallow: /liste/';
            $lines[] = 'Disallow: /biz-sizi-arayalim';
            $lines[] = 'Disallow: /uzun-donem-arac-kiralama/*/teklif-gonder';
            $lines[] = 'Disallow: /sayfa/*?*';
            $lines[] = 'Allow: /';
            $lines[] = '';
            $lines[] = 'Sitemap: '.route('sitemap.index');
        }

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
