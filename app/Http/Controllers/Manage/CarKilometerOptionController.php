<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarKilometerOption;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarKilometerOptionController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarKilometerOption::class);

        $items = CarKilometerOption::query()->orderBy('kilometer')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Kilometre seçenekleri',
            'description' => 'Yıllık km paketleri veya limit metinleri.',
            'modelFqcn' => CarKilometerOption::class,
            'routePrefix' => 'car-kilometer-options',
            'items' => $items,
            'columns' => [
                ['key' => 'kilometer', 'label' => 'Kilometre'],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarKilometerOption::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni km seçeneği',
            'description' => null,
            'indexRoute' => route('car-kilometer-options.index'),
            'action' => route('car-kilometer-options.store'),
            'method' => 'POST',
            'modelFqcn' => CarKilometerOption::class,
            'model' => null,
            'routePrefix' => 'car-kilometer-options',
            'fields' => [
                ['name' => 'kilometer', 'label' => 'Kilometre / etiket', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarKilometerOption::class);

        $data = $request->validate([
            'kilometer' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarKilometerOption::query()->create($data);

        return redirect()
            ->route('car-kilometer-options.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Seçenek eklendi.']);
    }

    public function edit(CarKilometerOption $car_kilometer_option): View
    {
        $this->authorize('update', $car_kilometer_option);

        return view('admin.fleet.simple.form', [
            'title' => 'Km seçeneği düzenle',
            'description' => 'Kayıt #'.$car_kilometer_option->id,
            'indexRoute' => route('car-kilometer-options.index'),
            'action' => route('car-kilometer-options.update', $car_kilometer_option),
            'method' => 'PUT',
            'modelFqcn' => CarKilometerOption::class,
            'model' => $car_kilometer_option,
            'routePrefix' => 'car-kilometer-options',
            'fields' => [
                ['name' => 'kilometer', 'label' => 'Kilometre / etiket', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarKilometerOption $car_kilometer_option): RedirectResponse
    {
        $this->authorize('update', $car_kilometer_option);

        $data = $request->validate([
            'kilometer' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_kilometer_option->update($data);

        return redirect()
            ->route('car-kilometer-options.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarKilometerOption $car_kilometer_option): RedirectResponse
    {
        $this->authorize('delete', $car_kilometer_option);
        $car_kilometer_option->delete();

        return redirect()
            ->route('car-kilometer-options.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Seçenek kaldırıldı.']);
    }
}
