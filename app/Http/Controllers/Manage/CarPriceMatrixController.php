<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreCarPriceMatrixRequest;
use App\Http\Requests\Manage\UpdateCarPriceMatrixRequest;
use App\Models\Car;
use App\Models\CarDownPayment;
use App\Models\CarDuration;
use App\Models\CarKilometerOption;
use App\Models\CarPackage;
use App\Models\CarPriceMatrix;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CarPriceMatrixController
{
    use AuthorizesRequests;

    public function create(Car $car): View
    {
        $this->authorize('create', CarPriceMatrix::class);

        $packages = CarPackage::query()->where('is_active', true)->orderBy('name')->get();
        $durations = CarDuration::query()->where('is_active', true)->orderBy('months')->get();
        $kilometerOptions = CarKilometerOption::query()->where('is_active', true)->orderBy('kilometer')->get();
        $downPayments = CarDownPayment::query()->where('is_active', true)->orderBy('amount')->get();

        return view('admin.fleet.price_matrices.create', compact(
            'car',
            'packages',
            'durations',
            'kilometerOptions',
            'downPayments',
        ));
    }

    public function store(StoreCarPriceMatrixRequest $request, Car $car): RedirectResponse
    {
        $data = $request->validated();
        $data['car_id'] = $car->id;

        CarPriceMatrix::query()->create($data);

        return redirect()
            ->route('cars.edit', $car)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Fiyat satırı eklendi',
                'message' => 'Fiyat matrisi güncellendi.',
            ]);
    }

    public function edit(CarPriceMatrix $price_matrix): View
    {
        $this->authorize('update', $price_matrix);

        $price_matrix->load('car');
        $car = $price_matrix->car;

        $packages = CarPackage::query()->where('is_active', true)->orderBy('name')->get();
        $durations = CarDuration::query()->where('is_active', true)->orderBy('months')->get();
        $kilometerOptions = CarKilometerOption::query()->where('is_active', true)->orderBy('kilometer')->get();
        $downPayments = CarDownPayment::query()->where('is_active', true)->orderBy('amount')->get();

        return view('admin.fleet.price_matrices.edit', compact(
            'car',
            'price_matrix',
            'packages',
            'durations',
            'kilometerOptions',
            'downPayments',
        ));
    }

    public function update(UpdateCarPriceMatrixRequest $request, CarPriceMatrix $price_matrix): RedirectResponse
    {
        $price_matrix->update($request->validated());

        return redirect()
            ->route('cars.edit', $price_matrix->car_id)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Fiyat satırı güncellendi',
                'message' => 'Kayıt kaydedildi.',
            ]);
    }

    public function destroy(CarPriceMatrix $price_matrix): RedirectResponse
    {
        $this->authorize('delete', $price_matrix);

        $carId = $price_matrix->car_id;
        $price_matrix->delete();

        return redirect()
            ->route('cars.edit', $carId)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Fiyat satırı silindi',
                'message' => 'Matris güncellendi.',
            ]);
    }
}
