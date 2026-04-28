<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class ManageController
{
    public function index()
    {
        return view('admin.index');
    }

    public function settings()
    {
        $setting = Setting::singleton();

        return view('admin.settings', [
            'setting' => $setting,
            'mb' => $setting->magicbox ?? [],
            'media' => [
                'logo' => $setting->mediaUrl($setting->logo),
                'favicon' => $setting->mediaUrl($setting->favicon),
                'placeholder_image' => $setting->mediaUrl($setting->placeholder_image),
            ],
        ]);
    }

    public function menus()
    {
        $setting = Setting::singleton();
        $mb = $setting->magicbox ?? [];

        return view('admin.menus.index', [
            'setting' => $setting,
            'navbarMenu' => data_get($mb, 'menus.navbar', []),
            'footerMenu' => data_get($mb, 'menus.footer', []),
            'pages' => Page::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title', 'slug']),
        ]);
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('optimize:clear');
        $setting = Setting::singleton();
        $magicbox = $setting->magicbox ?? [];

        $refreshMenuRows = static function (array $rows): array {
            $normalized = [];

            foreach ($rows as $row) {
                $type = in_array(($row['type'] ?? null), ['custom', 'page', 'group'], true)
                    ? (string) $row['type']
                    : 'custom';
                $parent = isset($row['parent']) ? trim((string) $row['parent']) : null;
                $label = trim((string) ($row['label'] ?? ''));
                $url = trim((string) ($row['url'] ?? ''));
                $pageId = isset($row['page_id']) && $row['page_id'] !== '' ? (int) $row['page_id'] : null;

                if ($type === 'page' && $pageId !== null) {
                    $page = Page::query()->whereKey($pageId)->where('is_active', true)->first();
                    if ($page !== null) {
                        $label = trim((string) $page->title);
                        $url = '/'.ltrim((string) $page->slug, '/');
                    }
                }

                $normalized[] = [
                    'type' => $type,
                    'parent' => $type === 'group' ? null : ($parent !== '' ? $parent : null),
                    'page_id' => $type === 'page' ? $pageId : null,
                    'label' => $label,
                    'url' => $type === 'group' ? '' : $url,
                ];
            }

            return $normalized;
        };

        $navbar = $refreshMenuRows((array) data_get($magicbox, 'menus.navbar', []));
        $footer = $refreshMenuRows((array) data_get($magicbox, 'menus.footer', []));

        data_set($magicbox, 'menus.navbar', $navbar);
        data_set($magicbox, 'menus.footer', $footer);
        $setting->magicbox = $magicbox;
        $setting->save();
        Setting::cachedForViews();

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Önbellek temizlendi',
            'message' => 'Sistem önbellekleri temizlendi ve menuler yenilendi.',
        ]);
    }
}
