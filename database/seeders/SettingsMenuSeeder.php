<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Settings.magicbox üzerindeki header (navbar) ve footer menülerini
 * PageSeeder ile yüklenmiş sayfaları kullanarak doldurur.
 *
 * Menü satır şeması (ManageController::clearCache ile uyumlu):
 *   [
 *     'type' => 'custom' | 'page' | 'group',
 *     'parent' => null | string (üst grup etiketi),
 *     'page_id' => null | int,
 *     'label' => string,
 *     'url' => string,
 *   ]
 */
class SettingsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $pages = Page::query()
            ->where('is_active', true)
            ->get(['id', 'title', 'slug'])
            ->keyBy('slug');

        if ($pages->isEmpty()) {
            $this->command?->warn('Aktif sayfa bulunamadı. Önce PageSeeder çalıştırılmalı.');

            return;
        }

        $navbar = $this->buildNavbar($pages);
        $footer = $this->buildFooter($pages);

        $setting = Setting::singleton();
        $magicbox = $setting->magicbox ?? [];

        data_set($magicbox, 'menus.navbar', $navbar);
        data_set($magicbox, 'menus.footer', $footer);

        $setting->magicbox = $magicbox;
        $setting->save();

        Setting::cachedForViews();

        $this->command?->info('Header ve footer menüleri yüklendi (navbar: '.count($navbar).' satır, footer: '.count($footer).' satır).');
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Page>  $pages
     * @return list<array<string,mixed>>
     */
    private function buildNavbar($pages): array
    {
        $rows = [];

        $rows[] = $this->custom('Ana Sayfa', '/');

        $rows[] = $this->group('Araç Kiralama');
        foreach ([
            'elektrikli-arac-kiralama',
            'hibrit-arac-kiralama',
            'uzun-donem-arac-kiralama',
            'kisa-donem-arac-kiralama',
            'luks-arac-kiralama',
            'suv-kiralama',
            'soforlu-arac-hizmeti',
            'ticari-arac-kiralama',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Araç Kiralama');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $rows[] = $this->group('Filo Hizmetleri');
        foreach ([
            'filo-kiralama',
            'filo-yonetimi',
            'operasyonel-kiralama',
            'bakim-onarim-hizmetleri',
            'lastik-hizmetleri',
            'sigorta-ve-kasko',
            'kaza-yol-yardim-asistans',
            'yedek-arac-hizmeti',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Filo Hizmetleri');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $rows[] = $this->group('Kurumsal');
        foreach ([
            'hakkimizda',
            'surdurulebilirlik',
            'kariyer',
            'basin-odasi',
            'kurumsal-sosyal-sorumluluk',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Kurumsal');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $rows[] = $this->custom('Referanslar', '/referanslar');
        $rows[] = $this->custom('İletişim', '/biz-sizi-arayalim');

        return $rows;
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Page>  $pages
     * @return list<array<string,mixed>>
     */
    private function buildFooter($pages): array
    {
        $rows = [];

        $rows[] = $this->group('Hizmetlerimiz');
        foreach ([
            'elektrikli-arac-kiralama',
            'hibrit-arac-kiralama',
            'uzun-donem-arac-kiralama',
            'kisa-donem-arac-kiralama',
            'suv-kiralama',
            'luks-arac-kiralama',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Hizmetlerimiz');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $rows[] = $this->group('Filo Çözümleri');
        foreach ([
            'filo-kiralama',
            'filo-yonetimi',
            'operasyonel-kiralama',
            'bakim-onarim-hizmetleri',
            'lastik-hizmetleri',
            'kaza-yol-yardim-asistans',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Filo Çözümleri');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $rows[] = $this->group('Kurumsal');
        foreach ([
            'hakkimizda',
            'surdurulebilirlik',
            'kariyer',
            'basin-odasi',
            'kurumsal-sosyal-sorumluluk',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Kurumsal');
            if ($row !== null) {
                $rows[] = $row;
            }
        }
        $rows[] = $this->custom('Referanslar', '/referanslar', 'Kurumsal');
        $rows[] = $this->custom('İletişim', '/biz-sizi-arayalim', 'Kurumsal');

        $rows[] = $this->group('Yardım & Hukuki');
        foreach ([
            'sikca-sorulan-sorular',
            'iletisim-ve-destek',
            'online-islemler',
            'gizlilik-politikasi',
            'kvkk-aydinlatma-metni',
            'cerez-politikasi',
            'yasal-uyari',
        ] as $slug) {
            $row = $this->pageItem($pages, $slug, 'Yardım & Hukuki');
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return array<string,mixed>
     */
    private function group(string $label): array
    {
        return [
            'type' => 'group',
            'parent' => null,
            'page_id' => null,
            'label' => $label,
            'url' => '',
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function custom(string $label, string $url, ?string $parent = null): array
    {
        return [
            'type' => 'custom',
            'parent' => $parent,
            'page_id' => null,
            'label' => $label,
            'url' => $url,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<string, Page>  $pages
     * @return array<string,mixed>|null
     */
    private function pageItem($pages, string $slug, ?string $parent = null): ?array
    {
        $page = $pages->get($slug);
        if ($page === null) {
            return null;
        }

        return [
            'type' => 'page',
            'parent' => $parent,
            'page_id' => $page->id,
            'label' => $page->title,
            'url' => '/sayfa/'.ltrim($page->slug, '/'),
        ];
    }
}
