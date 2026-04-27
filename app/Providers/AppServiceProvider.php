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
use App\Policies\FleetManagementPolicy;
use App\Policies\MessagePolicy;
use App\Policies\RentalRequestPolicy;
use App\Policies\UserPolicy;
use App\Policies\WeCallYouPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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

        foreach ([Slider::class, Faq::class, HomeServiceTile::class, HomePartner::class, HomeTestimonial::class, Reference::class, PageCategory::class, Page::class] as $homeModel) {
            Gate::policy($homeModel, FleetManagementPolicy::class);
        }

        Route::bind('user', function (string $value): User {
            return User::query()->withoutSuperAdmins()->whereKey($value)->firstOrFail();
        });
    }
}
