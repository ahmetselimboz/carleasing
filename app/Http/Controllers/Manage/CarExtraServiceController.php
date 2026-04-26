<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarExtraService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarExtraServiceController
{
    use AuthorizesRequests;

    /** @var array<int, string> */
    private const PRICE_TYPE_LABELS = [
        0 => 'Sabit (tek sefer)',
        1 => 'Aylık',
        2 => 'Yüzde',
    ];

    public function index(): View
    {
        $this->authorize('viewAny', CarExtraService::class);

        $items = CarExtraService::query()->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Ek hizmetler',
            'description' => 'Araçlara eklenebilir ücretli ek kalemler.',
            'modelFqcn' => CarExtraService::class,
            'routePrefix' => 'car-extra-services',
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Ad'],
                ['key' => 'price', 'label' => 'Ücret'],
                ['key' => 'price_type', 'label' => 'Tip', 'type' => 'map', 'map' => self::PRICE_TYPE_LABELS],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarExtraService::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni ek hizmet',
            'description' => null,
            'indexRoute' => route('car-extra-services.index'),
            'action' => route('car-extra-services.store'),
            'method' => 'POST',
            'modelFqcn' => CarExtraService::class,
            'model' => null,
            'routePrefix' => 'car-extra-services',
            'fields' => [
                ['name' => 'name', 'label' => 'Ad', 'type' => 'text', 'required' => false],
                ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'required' => false],
                ['name' => 'price', 'label' => 'Ücret', 'type' => 'text', 'required' => true],
                [
                    'name' => 'price_type',
                    'label' => 'Ücret tipi',
                    'type' => 'select',
                    'required' => true,
                    'options' => self::PRICE_TYPE_LABELS,
                ],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarExtraService::class);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'price_type' => ['required', 'integer', Rule::in([0, 1, 2])],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarExtraService::query()->create($data);

        return redirect()
            ->route('car-extra-services.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Ek hizmet eklendi.']);
    }

    public function edit(CarExtraService $car_extra_service): View
    {
        $this->authorize('update', $car_extra_service);

        return view('admin.fleet.simple.form', [
            'title' => 'Ek hizmet düzenle',
            'description' => 'Kayıt #'.$car_extra_service->id,
            'indexRoute' => route('car-extra-services.index'),
            'action' => route('car-extra-services.update', $car_extra_service),
            'method' => 'PUT',
            'modelFqcn' => CarExtraService::class,
            'model' => $car_extra_service,
            'routePrefix' => 'car-extra-services',
            'fields' => [
                ['name' => 'name', 'label' => 'Ad', 'type' => 'text', 'required' => false],
                ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'required' => false],
                ['name' => 'price', 'label' => 'Ücret', 'type' => 'text', 'required' => true],
                [
                    'name' => 'price_type',
                    'label' => 'Ücret tipi',
                    'type' => 'select',
                    'required' => true,
                    'options' => self::PRICE_TYPE_LABELS,
                ],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarExtraService $car_extra_service): RedirectResponse
    {
        $this->authorize('update', $car_extra_service);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'string', 'max:255'],
            'price_type' => ['required', 'integer', Rule::in([0, 1, 2])],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_extra_service->update($data);

        return redirect()
            ->route('car-extra-services.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarExtraService $car_extra_service): RedirectResponse
    {
        $this->authorize('delete', $car_extra_service);
        $car_extra_service->delete();

        return redirect()
            ->route('car-extra-services.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Ek hizmet kaldırıldı.']);
    }
}
