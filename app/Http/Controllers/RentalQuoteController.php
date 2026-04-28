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

class RentalQuoteController
{
    public function create(Request $request, string $slug): View
    {
        $car = Car::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'priceMatrices' => fn ($q) => $q->where('is_active', true)
                    ->with(['package', 'duration', 'kilometerOption', 'downPayment']),
                'attributePivots' => fn ($q) => $q->with(['category', 'attribute', 'value']),
            ])
            ->firstOrFail();

        $packageId = $request->integer('package') ?: null;
        $durationId = $request->integer('duration') ?: null;
        $kilometerId = $request->integer('kilometer') ?: null;
        $downPaymentId = $request->integer('down_payment') ?: null;
        $extraIds = collect($request->input('extras', []))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $matrixRow = $car->priceMatrices->first(function ($r) use ($packageId, $durationId, $kilometerId, $downPaymentId) {
            return ($packageId === null || (int) $r->car_package_id === $packageId)
                && ($durationId === null || (int) $r->car_duration_id === $durationId)
                && ($kilometerId === null || (int) $r->car_kilometer_option_id === $kilometerId)
                && ($downPaymentId === null || (int) $r->car_down_payment_id === $downPaymentId);
        }) ?? $car->priceMatrices->first();

        $package = $packageId
            ? CarPackage::query()->find($packageId)
            : $matrixRow?->package;
        $duration = $durationId
            ? CarDuration::query()->find($durationId)
            : $matrixRow?->duration;
        $kilometer = $kilometerId
            ? CarKilometerOption::query()->find($kilometerId)
            : $matrixRow?->kilometerOption;
        $downPayment = $downPaymentId
            ? CarDownPayment::query()->find($downPaymentId)
            : $matrixRow?->downPayment;

        $selectedExtras = $extraIds
            ? CarExtraService::query()->whereIn('id', $extraIds)->where('is_active', true)->get()
            : collect();

        // Specs ozeti (kategori bazli)
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

        return view('theme.v1.car-quote', [
            'car' => $car,
            'package' => $package,
            'duration' => $duration,
            'kilometer' => $kilometer,
            'downPayment' => $downPayment,
            'selectedExtras' => $selectedExtras,
            'matrixRow' => $matrixRow,
            'monthlyPrice' => $matrixRow?->monthly_price,
            'specGroups' => $specGroups,
        ]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $car = Car::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

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
            'package_id' => ['nullable', 'integer'],
            'duration_id' => ['nullable', 'integer'],
            'kilometer_id' => ['nullable', 'integer'],
            'down_payment_id' => ['nullable', 'integer'],
            'extras' => ['nullable', 'array'],
            'extras.*' => ['integer'],
        ]);

        $package = ! empty($data['package_id']) ? CarPackage::query()->find($data['package_id']) : null;
        $duration = ! empty($data['duration_id']) ? CarDuration::query()->find($data['duration_id']) : null;
        $kilometer = ! empty($data['kilometer_id']) ? CarKilometerOption::query()->find($data['kilometer_id']) : null;
        $downPayment = ! empty($data['down_payment_id']) ? CarDownPayment::query()->find($data['down_payment_id']) : null;
        $extras = ! empty($data['extras'])
            ? CarExtraService::query()->whereIn('id', $data['extras'])->where('is_active', true)->get()
            : collect();

        $matrixRow = $car->priceMatrices()
            ->where('is_active', true)
            ->when(! empty($data['package_id']), fn ($q) => $q->where('car_package_id', $data['package_id']))
            ->when(! empty($data['duration_id']), fn ($q) => $q->where('car_duration_id', $data['duration_id']))
            ->when(! empty($data['kilometer_id']), fn ($q) => $q->where('car_kilometer_option_id', $data['kilometer_id']))
            ->when(! empty($data['down_payment_id']), fn ($q) => $q->where('car_down_payment_id', $data['down_payment_id']))
            ->first();

        RentalRequest::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'city' => $data['city'] ?? null,
            'district' => $data['district'] ?? null,
            'requested_car_count' => $data['requested_car_count'] ?? 1,
            'company_total_car_count' => $data['company_total_car_count'] ?? null,
            'tax_office' => $data['tax_office'] ?? null,
            'tax_number_or_tckn' => $data['tax_number_or_tckn'] ?? null,
            'cars' => [[
                'car_id' => $car->id,
                'title' => $car->title,
                'slug' => $car->slug,
                'package' => $package?->name,
                'duration_months' => $duration?->months,
                'annual_km' => $kilometer?->kilometer,
                'down_payment' => $downPayment?->amount,
                'monthly_price' => $matrixRow?->monthly_price,
                'extras' => $extras->map(fn ($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'price' => $e->price,
                ])->values()->all(),
            ]],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('cars.show', $car->slug)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Teklif talebiniz alındı',
                'message' => 'Ekibimiz en kısa sürede sizinle iletişime geçecek.',
            ]);
    }
}
