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

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Önbellek temizlendi',
            'message' => 'Sistem önbellekleri temizlendi.',
        ]);
    }
}
