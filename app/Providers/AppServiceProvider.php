<?php

namespace App\Providers;

use App\Models\Car;
use App\Models\Faq;
use App\Models\HomePartner;
use App\Models\Message;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Reference;
use App\Models\HomeServiceTile;
use App\Models\HomeTestimonial;
use App\Models\Slider;
use App\Models\CarAttribute;
use App\Models\CarAttributeCategory;
use App\Models\CarAttributePivot;
use App\Models\CarAttributeValue;
use App\Models\CarDownPayment;
use App\Models\CarDuration;
use App\Models\CarExtraService;
use App\Models\CarKilometerOption;
use App\Models\CarPackage;
use App\Models\CarPriceMatrix;
use App\Models\RentalRequest;
use App\Models\User;
use App\Models\WeCallYou;
use App\Observers\LeadSourceObserver;
use App\Policies\FleetManagementPolicy;
use App\Policies\MessagePolicy;
use App\Policies\RentalRequestPolicy;
use App\Policies\UserPolicy;
use App\Policies\WeCallYouPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(User::class, UserPolicy::class);

        $fleetModels = [
            Car::class,
            CarDownPayment::class,
            CarPackage::class,
            CarDuration::class,
            CarKilometerOption::class,
            CarPriceMatrix::class,
            CarExtraService::class,
            CarAttributeCategory::class,
            CarAttributeValue::class,
            CarAttribute::class,
            CarAttributePivot::class,
        ];

        foreach ($fleetModels as $modelClass) {
            Gate::policy($modelClass, FleetManagementPolicy::class);
        }

        Gate::policy(RentalRequest::class, RentalRequestPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(WeCallYou::class, WeCallYouPolicy::class);
        RentalRequest::observe(LeadSourceObserver::class);
        Message::observe(LeadSourceObserver::class);
        WeCallYou::observe(LeadSourceObserver::class);

        foreach ([Slider::class, Faq::class, HomeServiceTile::class, HomePartner::class, HomeTestimonial::class, Reference::class, PageCategory::class, Page::class] as $homeModel) {
            Gate::policy($homeModel, FleetManagementPolicy::class);
        }

        Route::bind('user', function (string $value): User {
            return User::query()->withoutSuperAdmins()->whereKey($value)->firstOrFail();
        });

        View::composer('admin.components.header', function ($view): void {
            if (! Auth::check()) {
                $view->with('adminNotifications', [
                    'totalUnread' => 0,
                    'items' => collect(),
                    'links' => [],
                ]);

                return;
            }

            $rentalRequests = RentalRequest::query()
                ->whereNull('read_at')
                ->latest('id')
                ->get(['id', 'name', 'surname', 'created_at']);

            $messages = Message::query()
                ->whereNull('read_at')
                ->latest('id')
                ->get(['id', 'name', 'surname', 'category', 'created_at']);

            $weCallYouItems = WeCallYou::query()
                ->whereNull('read_at')
                ->latest('id')
                ->get(['id', 'name', 'surname', 'created_at']);

            $items = $rentalRequests
                ->map(function (RentalRequest $item): array {
                    $fullName = trim(($item->name ?? '').' '.($item->surname ?? ''));

                    return [
                        'type' => 'rental_request',
                        'title' => 'Kiralama Talebi',
                        'description' => $fullName !== '' ? $fullName : 'İsimsiz başvuru',
                        'created_at' => $item->created_at,
                        'url' => route('rental-requests.show', $item),
                    ];
                })
                ->concat($messages->map(function (Message $item): array {
                    $fullName = trim(($item->name ?? '').' '.($item->surname ?? ''));

                    return [
                        'type' => 'message',
                        'title' => 'İletişim Mesajı',
                        'description' => $fullName !== '' ? $fullName : 'Yeni mesaj',
                        'created_at' => $item->created_at,
                        'url' => route('messages.show', $item),
                    ];
                }))
                ->concat($weCallYouItems->map(function (WeCallYou $item): array {
                    $fullName = trim(($item->name ?? '').' '.($item->surname ?? ''));

                    return [
                        'type' => 'we_call_you',
                        'title' => 'Geri Arama Talebi',
                        'description' => $fullName !== '' ? $fullName : 'Yeni geri arama talebi',
                        'created_at' => $item->created_at,
                        'url' => route('we-call-you.show', $item),
                    ];
                }))
                ->sortByDesc('created_at')
                ->take(12)
                ->values();

            $view->with('adminNotifications', [
                'totalUnread' => $rentalRequests->count() + $messages->count() + $weCallYouItems->count(),
                'items' => $items,
                'links' => [
                    'rentalRequests' => route('rental-requests.index', ['status' => 'pending']),
                    'messages' => route('messages.index', ['status' => 'pending']),
                    'weCallYou' => route('we-call-you.index', ['status' => 'pending']),
                ],
            ]);
        });
    }
}
