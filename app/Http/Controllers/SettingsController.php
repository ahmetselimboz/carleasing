<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Requests\UpdateMenusRequest;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingsController
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return list<array{type: string, parent: ?string, page_id: ?int, label: string, url: string}>
     */
    private function normalizeMenuRows(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $type = in_array(($row['type'] ?? null), ['custom', 'page', 'group'], true)
                ? (string) $row['type']
                : 'custom';
            $parent = trim((string) ($row['parent'] ?? ''));
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

            if ($type === 'group') {
                if ($label === '') {
                    continue;
                }
                $normalized[] = [
                    'type' => 'group',
                    'parent' => null,
                    'page_id' => null,
                    'label' => $label,
                    'url' => '',
                ];
                continue;
            }

            if ($label === '' && $url === '' && $parent === '') {
                continue;
            }

            $normalized[] = [
                'type' => $type,
                'parent' => $parent !== '' ? $parent : null,
                'page_id' => $type === 'page' ? $pageId : null,
                'label' => $label,
                'url' => $url,
            ];
        }

        return $normalized;
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = Setting::singleton();

        $settings->title = $request->input('title');
        $settings->description = $request->input('description');
        $settings->maintenance_mode = $request->boolean('maintenance_mode');

        $dir = 'settings';

        if ($request->hasFile('logo')) {
            $this->deleteIfExists($settings->logo);
            $settings->logo = $request->file('logo')->store($dir, 'public');
        }

        if ($request->hasFile('favicon')) {
            $this->deletePreviousFavicon($settings->favicon);
            $request->file('favicon')->move(public_path(), 'favicon.ico');
            $settings->favicon = 'favicon.ico';
        }

        if ($request->hasFile('placeholder_image')) {
            $this->deleteIfExists($settings->placeholder_image);
            $settings->placeholder_image = $request->file('placeholder_image')->store($dir, 'public');
        }

        $prevMb = $settings->magicbox ?? [];

        $ogImage = (string) data_get($prevMb, 'seo.og_image', '');
        if ($request->hasFile('magicbox.seo.og_image')) {
            $this->deleteIfExists($ogImage);
            $ogImage = $request->file('magicbox.seo.og_image')->store($dir, 'public');
        }
        $googleSecret = $request->input('magicbox.google.oauth_client_secret');
        if (! filled($googleSecret)) {
            $googleSecret = (string) data_get($prevMb, 'google.oauth_client_secret', '');
        }
        $smtpPassword = $request->input('magicbox.mail.smtp_password');
        if (! filled($smtpPassword)) {
            $smtpPassword = (string) data_get($prevMb, 'mail.smtp_password', '');
        }

        $settings->magicbox = [
            'maintenance' => [
                'message' => $request->input('magicbox.maintenance.message', ''),
                'estimated_end' => $request->input('magicbox.maintenance.estimated_end'),
            ],
            'contact' => [
                'email' => $request->input('magicbox.contact.email', ''),
                'phone' => $request->input('magicbox.contact.phone', ''),
                'address' => $request->input('magicbox.contact.address', ''),
            ],
            'social' => [
                'facebook' => $request->input('magicbox.social.facebook', ''),
                'twitter' => $request->input('magicbox.social.twitter', ''),
                'instagram' => $request->input('magicbox.social.instagram', ''),
                'linkedin' => $request->input('magicbox.social.linkedin', ''),
                'youtube' => $request->input('magicbox.social.youtube', ''),
                'tiktok' => $request->input('magicbox.social.tiktok', ''),
            ],
            'site' => [
                'copyright' => $request->input('magicbox.site.copyright', ''),
            ],
            'seo' => [
                'meta_keywords' => $request->input('magicbox.seo.meta_keywords', ''),
                'default_meta_description' => $request->input('magicbox.seo.default_meta_description', ''),
                'allow_indexing' => $request->boolean('magicbox.seo.allow_indexing'),
                'google_site_verification' => trim((string) $request->input('magicbox.seo.google_site_verification', '')),
                'bing_site_verification' => trim((string) $request->input('magicbox.seo.bing_site_verification', '')),
                'yandex_verification' => trim((string) $request->input('magicbox.seo.yandex_verification', '')),
                'twitter_handle' => trim((string) $request->input('magicbox.seo.twitter_handle', '')),
                'locale' => trim((string) $request->input('magicbox.seo.locale', '')) ?: 'tr-TR',
                'og_locale' => trim((string) $request->input('magicbox.seo.og_locale', '')) ?: 'tr_TR',
                'og_image' => $ogImage,
            ],
            'inject' => [
                'head' => $request->input('magicbox.inject.head', ''),
                'body' => $request->input('magicbox.inject.body', ''),
                'footer' => $request->input('magicbox.inject.footer', ''),
            ],
            'ads_txt' => [
                'content' => $request->input('magicbox.ads_txt.content', ''),
            ],
            'google' => [
                'oauth_client_id' => $request->input('magicbox.google.oauth_client_id', ''),
                'oauth_client_secret' => $googleSecret,
                'oauth_redirect_url' => $request->input('magicbox.google.oauth_redirect_url', ''),
                'property_id' => $request->input('magicbox.google.property_id', ''),
                'analytics_tracking_code' => $request->input('magicbox.google.analytics_tracking_code', ''),
            ],
            'mail' => [
                'smtp_host' => $request->input('magicbox.mail.smtp_host', ''),
                'smtp_port' => $request->input('magicbox.mail.smtp_port', ''),
                'smtp_username' => $request->input('magicbox.mail.smtp_username', ''),
                'smtp_password' => $smtpPassword,
                'smtp_encryption' => $request->input('magicbox.mail.smtp_encryption', ''),
                'from_address' => $request->input('magicbox.mail.from_address', ''),
                'from_name' => $request->input('magicbox.mail.from_name', ''),
            ],
            'features' => [
                'newsletter' => $request->boolean('magicbox.features.newsletter'),
                'cookie_banner' => $request->boolean('magicbox.features.cookie_banner'),
                'allow_registration' => $request->boolean('magicbox.features.allow_registration'),
                'open_graph_default' => $request->boolean('magicbox.features.open_graph_default'),
            ],
            'integrations' => [
                'google_analytics_id' => $request->input('magicbox.integrations.google_analytics_id', ''),
                'google_tag_manager_id' => $request->input('magicbox.integrations.google_tag_manager_id', ''),
            ],
            'menus' => data_get($prevMb, 'menus', []),
        ];

        $settings->save();

        return redirect()
            ->route('settings')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Ayarlar başarıyla güncellendi.',
            ]);
    }

    public function updateMenus(UpdateMenusRequest $request): RedirectResponse
    {
        $settings = Setting::singleton();
        $mb = $settings->magicbox ?? [];

        $navbar = $this->normalizeMenuRows((array) $request->input('navbar', []));
        $footer = $this->normalizeMenuRows((array) $request->input('footer', []));

        data_set($mb, 'menus.navbar', $navbar);
        data_set($mb, 'menus.footer', $footer);

        $settings->magicbox = $mb;
        $settings->save();

        return redirect()
            ->route('menus.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Menü ayarları güncellendi.',
            ]);
    }

    private function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deletePreviousFavicon(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (str_contains($path, '/')) {
            $this->deleteIfExists($path);

            return;
        }

        $full = public_path($path);
        if (File::isFile($full)) {
            File::delete($full);
        }
    }
}
