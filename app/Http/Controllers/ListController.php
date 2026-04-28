<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarDownPayment;
use App\Models\CarDuration;
use App\Models\CarExtraService;
use App\Models\CarKilometerOption;
use App\Models\CarPackage;
use App\Models\RentalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListController
{
    public function index(): View
    {
        $cars = Car::query()
            ->where('is_active', true)
            ->with([
                'priceMatrices' => fn ($q) => $q->where('is_active', true)->orderBy('id')->with(['package', 'duration', 'kilometerOption', 'downPayment']),
                'attributePivots' => fn ($q) => $q->with(['category', 'attribute', 'value']),
            ])
            ->orderByRaw('COALESCE(home_sort_order, 65535) asc')
            ->get();

        $extraServices = CarExtraService::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'description', 'price']);

        $favoriteCarMap = $cars->mapWithKeys(function (Car $car): array {
            $matrixRows = $car->priceMatrices->map(fn ($r) => [
                'package_id' => $r->car_package_id,
                'duration_id' => $r->car_duration_id,
                'kilometer_id' => $r->car_kilometer_option_id,
                'down_payment_id' => $r->car_down_payment_id,
                'monthly_price' => $r->monthly_price,
            ])->values();

            $packages = $car->priceMatrices->pluck('package')->filter()->unique('id')->sortBy('name')->values()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
            ])->values();
            $durations = $car->priceMatrices->pluck('duration')->filter()->unique('id')->sortBy('months')->values()->map(fn ($d) => [
                'id' => $d->id,
                'months' => $d->months,
            ])->values();
            $kilometers = $car->priceMatrices->pluck('kilometerOption')->filter()->unique('id')->sortBy('kilometer')->values()->map(fn ($k) => [
                'id' => $k->id,
                'kilometer' => $k->kilometer,
            ])->values();
            $downPayments = $car->priceMatrices->pluck('downPayment')->filter()->unique('id')->sortBy('amount')->values()->map(fn ($d) => [
                'id' => $d->id,
                'amount' => $d->amount,
            ])->values();

            $specGroups = [];
            foreach ($car->attributePivots as $pivot) {
                $catName = $pivot->category?->name ?? 'Genel';
                if (! isset($specGroups[$catName])) {
                    $specGroups[$catName] = [];
                }
                $specGroups[$catName][] = [
                    'label' => $pivot->attribute?->title ?? '',
                    'value' => $pivot->value?->title ?? '',
                ];
            }

            return [
                $car->slug => [
                    'slug' => $car->slug,
                    'title' => $car->title,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'fuel_type' => $car->fuel_type,
                    'transmission_type' => $car->transmission_type,
                    'body_type' => $car->body_type,
                    'url' => route('cars.show', $car->slug),
                    'quote_store_url' => route('cars.quote.store', $car->slug),
                    'image_url' => $car->displayImageUrl(),
                    'price' => $car->displayMonthlyPriceContext()['price'] ?? null,
                    'matrix_rows' => $matrixRows,
                    'packages' => $packages,
                    'durations' => $durations,
                    'kilometers' => $kilometers,
                    'down_payments' => $downPayments,
                    'spec_groups' => $specGroups,
                ],
            ];
        });

        return view('theme.v1.favorites.index', [
            'favoriteCarMap' => $favoriteCarMap,
            'extraServices' => $extraServices,
        ]);
    }

    public function store(Car $car): RedirectResponse
    {
        return back()->with('toast', [
            'type' => 'info',
            'title' => 'Bilgilendirme',
            'message' => 'Liste islemi tarayicinizda localStorage ile yonetiliyor.',
        ]);
    }

    public function destroy(Car $car): RedirectResponse
    {
        return back()->with('toast', [
            'type' => 'info',
            'title' => 'Bilgilendirme',
            'message' => 'Liste islemi tarayicinizda localStorage ile yonetiliyor.',
        ]);
    }

    public function submitListRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'surname' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:160'],
            'phone_number' => ['required', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'requested_car_count' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'company_total_car_count' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'tax_office' => ['nullable', 'string', 'max:120'],
            'tax_number_or_tckn' => ['nullable', 'string', 'max:32'],
            'kvkk' => ['accepted'],
            'cart_items' => ['required', 'string'],
        ]);

        $rawItems = json_decode($data['cart_items'], true);
        if (! is_array($rawItems) || empty($rawItems)) {
            return back()->withErrors(['cart_items' => 'Talep icin en az bir arac secmelisiniz.'])->withInput();
        }

        $carsPayload = [];
        foreach ($rawItems as $item) {
            $slug = (string) data_get($item, 'slug', '');
            if ($slug === '') {
                continue;
            }

            $car = Car::query()->where('slug', $slug)->where('is_active', true)->first();
            if (! $car) {
                continue;
            }

            $packageId = data_get($item, 'package_id');
            $durationId = data_get($item, 'duration_id');
            $kilometerId = data_get($item, 'kilometer_id');
            $downPaymentId = data_get($item, 'down_payment_id');
            $extras = collect(data_get($item, 'extras', []))->map(fn ($v) => (int) $v)->filter()->values()->all();

            $package = $packageId ? CarPackage::query()->find((int) $packageId) : null;
            $duration = $durationId ? CarDuration::query()->find((int) $durationId) : null;
            $kilometer = $kilometerId ? CarKilometerOption::query()->find((int) $kilometerId) : null;
            $downPayment = $downPaymentId ? CarDownPayment::query()->find((int) $downPaymentId) : null;
            $extraRows = ! empty($extras)
                ? CarExtraService::query()->whereIn('id', $extras)->where('is_active', true)->get()
                : collect();

            $matrixRow = $car->priceMatrices()
                ->where('is_active', true)
                ->when($packageId, fn ($q) => $q->where('car_package_id', $packageId))
                ->when($durationId, fn ($q) => $q->where('car_duration_id', $durationId))
                ->when($kilometerId, fn ($q) => $q->where('car_kilometer_option_id', $kilometerId))
                ->when($downPaymentId, fn ($q) => $q->where('car_down_payment_id', $downPaymentId))
                ->first();

            $carsPayload[] = [
                'car_id' => $car->id,
                'title' => $car->title,
                'slug' => $car->slug,
                'package' => $package?->name,
                'duration_months' => $duration?->months,
                'annual_km' => $kilometer?->kilometer,
                'down_payment' => $downPayment?->amount,
                'monthly_price' => $matrixRow?->monthly_price,
                'extras' => $extraRows->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'price' => $e->price,
                ])->values()->all(),
            ];
        }

        if (empty($carsPayload)) {
            return back()->withErrors(['cart_items' => 'Listeye eklenen araclar bulunamadi.'])->withInput();
        }

        RentalRequest::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'requested_car_count' => $data['requested_car_count'] ?? count($carsPayload),
            'company_total_car_count' => $data['company_total_car_count'] ?? null,
            'tax_office' => $data['tax_office'] ?? null,
            'tax_number_or_tckn' => $data['tax_number_or_tckn'] ?? null,
            'cars' => $carsPayload,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('favorites.index')->with('toast', [
            'type' => 'success',
            'title' => 'Talebiniz alindi',
            'message' => 'Listenizdeki araclar icin talebiniz basariyla iletildi.',
        ]);
    }
}
