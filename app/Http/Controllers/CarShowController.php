<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarExtraService;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarShowController
{
    public function show(Request $request, string $slug): View
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

        $extraServices = CarExtraService::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $faqs = Faq::query()->activeOrdered()->get();

        $relatedCars = Car::query()
            ->where('is_active', true)
            ->where('id', '!=', $car->id)
            ->with(['priceMatrices' => fn ($q) => $q->where('is_active', true)->orderBy('id')->with('duration')])
            ->orderByRaw('COALESCE(home_sort_order, 65535) asc')
            ->limit(4)
            ->get();

        // Spec tabs: kategoriye gore grupla
        $specGroups = [];
        foreach ($car->attributePivots as $pivot) {
            $catName = $pivot->category?->name ?? 'Genel';
            $catId = $pivot->category?->id ?? 0;
            $key = $catId.'_'.$catName;
            if (! isset($specGroups[$key])) {
                $specGroups[$key] = ['name' => $catName, 'rows' => []];
            }
            $specGroups[$key]['rows'][] = [
                'label' => $pivot->attribute?->title ?? '',
                'value' => $pivot->value?->title ?? '',
            ];
        }
        $specGroups = array_values($specGroups);

        // Price matrix verisi: JS tarafinda secim degisikliginde fiyat lookup'i icin
        $matrixRows = $car->priceMatrices->map(fn ($r) => [
            'package_id' => $r->car_package_id,
            'duration_id' => $r->car_duration_id,
            'kilometer_id' => $r->car_kilometer_option_id,
            'down_payment_id' => $r->car_down_payment_id,
            'monthly_price' => $r->monthly_price,
        ])->values();

        // Her boyut icin essiz secenekleri matris satirlarindan cikar
        $packages = $car->priceMatrices->pluck('package')->filter()->unique('id')->sortBy('name')->values();
        $durations = $car->priceMatrices->pluck('duration')->filter()->unique('id')->sortBy('months')->values();
        $kilometers = $car->priceMatrices->pluck('kilometerOption')->filter()->unique('id')->sortBy('kilometer')->values();
        $downPayments = $car->priceMatrices->pluck('downPayment')->filter()->unique('id')->sortBy('amount')->values();

        return view('theme.v1.car-show', compact(
            'car',
            'extraServices',
            'faqs',
            'relatedCars',
            'specGroups',
            'matrixRows',
            'packages',
            'durations',
            'kilometers',
            'downPayments',
        ));
    }
}
