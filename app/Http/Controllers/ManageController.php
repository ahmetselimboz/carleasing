<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Faq;
use App\Models\HomePartner;
use App\Models\HomeServiceTile;
use App\Models\HomeTestimonial;
use App\Models\Message;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Reference;
use App\Models\RentalRequest;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use App\Models\WeCallYou;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ManageController
{
    public function index()
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $weekAgo = $now->copy()->subDays(7)->startOfDay();
        $monthAgo = $now->copy()->subDays(30)->startOfDay();
        $prevMonthStart = $now->copy()->subDays(60)->startOfDay();
        $sparkStart = $now->copy()->subDays(13)->startOfDay();

        $rentalCounts = [
            'total' => RentalRequest::query()->count(),
            'pending' => RentalRequest::query()->whereNull('read_at')->count(),
            'today' => RentalRequest::query()->where('created_at', '>=', $today)->count(),
            'last_7' => RentalRequest::query()->where('created_at', '>=', $weekAgo)->count(),
            'last_30' => RentalRequest::query()->where('created_at', '>=', $monthAgo)->count(),
            'prev_30' => RentalRequest::query()->whereBetween('created_at', [$prevMonthStart, $monthAgo])->count(),
        ];

        $messageCounts = [
            'total' => Message::query()->count(),
            'pending' => Message::query()->whereNull('read_at')->count(),
            'today' => Message::query()->where('created_at', '>=', $today)->count(),
            'last_7' => Message::query()->where('created_at', '>=', $weekAgo)->count(),
            'last_30' => Message::query()->where('created_at', '>=', $monthAgo)->count(),
            'prev_30' => Message::query()->whereBetween('created_at', [$prevMonthStart, $monthAgo])->count(),
        ];

        $callbackCounts = [
            'total' => WeCallYou::query()->count(),
            'pending' => WeCallYou::query()->whereNull('read_at')->count(),
            'today' => WeCallYou::query()->where('created_at', '>=', $today)->count(),
            'last_7' => WeCallYou::query()->where('created_at', '>=', $weekAgo)->count(),
            'last_30' => WeCallYou::query()->where('created_at', '>=', $monthAgo)->count(),
            'prev_30' => WeCallYou::query()->whereBetween('created_at', [$prevMonthStart, $monthAgo])->count(),
        ];

        $totalLeadsLast30 = $rentalCounts['last_30'] + $messageCounts['last_30'] + $callbackCounts['last_30'];
        $totalLeadsPrev30 = $rentalCounts['prev_30'] + $messageCounts['prev_30'] + $callbackCounts['prev_30'];
        $leadsDelta = $totalLeadsPrev30 > 0
            ? round((($totalLeadsLast30 - $totalLeadsPrev30) / $totalLeadsPrev30) * 100, 1)
            : ($totalLeadsLast30 > 0 ? 100 : 0);

        $totalPending = $rentalCounts['pending'] + $messageCounts['pending'] + $callbackCounts['pending'];

        $fleetCounts = [
            'total' => Car::query()->count(),
            'active' => Car::query()->where('is_active', true)->count(),
            'featured' => Car::query()->where('is_active', true)->where('home_featured', true)->count(),
        ];

        $contentCounts = [
            'pages' => Page::query()->where('is_active', true)->count(),
            'page_categories' => PageCategory::query()->count(),
            'sliders' => Slider::query()->count(),
            'faqs' => Faq::query()->count(),
            'references' => Reference::query()->count(),
            'service_tiles' => HomeServiceTile::query()->count(),
            'partners' => HomePartner::query()->count(),
            'testimonials' => HomeTestimonial::query()->count(),
            'admins' => User::query()->count(),
        ];

        $dailyCount = static fn (string $modelClass, Carbon $start, Carbon $end): int => $modelClass::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->count();

        $sparkline = collect();
        for ($i = 13; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->startOfDay();
            $next = $day->copy()->addDay();
            $rental = $dailyCount(RentalRequest::class, $day, $next);
            $msg = $dailyCount(Message::class, $day, $next);
            $cb = $dailyCount(WeCallYou::class, $day, $next);
            $sparkline->push([
                'date' => $day->toDateString(),
                'label' => $day->isoFormat('DD MMM'),
                'rental' => $rental,
                'message' => $msg,
                'callback' => $cb,
                'total' => $rental + $msg + $cb,
            ]);
        }

        $latestRentals = RentalRequest::query()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'surname', 'email', 'phone_number', 'requested_car_count', 'created_at', 'read_at']);

        $latestMessages = Message::query()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'surname', 'email', 'category', 'created_at', 'read_at']);

        $latestCallbacks = WeCallYou::query()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'name', 'surname', 'phone_number', 'preferred_time', 'created_at', 'read_at']);

        $topFeaturedCars = Car::query()
            ->where('is_active', true)
            ->where('home_featured', true)
            ->orderByRaw('COALESCE(home_sort_order, 65535) asc')
            ->orderBy('id')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'image', 'brand', 'model']);

        $contentHealth = [
            'no_image' => Car::query()->where('is_active', true)->whereNull('image')->count(),
            'no_description' => Car::query()->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('description')->orWhere('description', '');
                })->count(),
            'inactive' => Car::query()->where('is_active', false)->count(),
            'pages_inactive' => Page::query()->where('is_active', false)->count(),
        ];

        return view('admin.index', [
            'rentalCounts' => $rentalCounts,
            'messageCounts' => $messageCounts,
            'callbackCounts' => $callbackCounts,
            'totalLeadsLast30' => $totalLeadsLast30,
            'totalLeadsPrev30' => $totalLeadsPrev30,
            'leadsDelta' => $leadsDelta,
            'totalPending' => $totalPending,
            'fleetCounts' => $fleetCounts,
            'contentCounts' => $contentCounts,
            'sparkline' => $sparkline,
            'latestRentals' => $latestRentals,
            'latestMessages' => $latestMessages,
            'latestCallbacks' => $latestCallbacks,
            'topFeaturedCars' => $topFeaturedCars,
            'contentHealth' => $contentHealth,
            'currentUser' => Auth::user(),
        ]);
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
