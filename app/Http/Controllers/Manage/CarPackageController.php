<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarPackage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarPackageController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarPackage::class);

        $items = CarPackage::query()->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Paketler',
            'description' => 'Filo paket adları (ör. Tam, Orta, Ekonomik).',
            'modelFqcn' => CarPackage::class,
            'routePrefix' => 'car-packages',
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Ad'],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarPackage::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni paket',
            'description' => null,
            'indexRoute' => route('car-packages.index'),
            'action' => route('car-packages.store'),
            'method' => 'POST',
            'modelFqcn' => CarPackage::class,
            'model' => null,
            'routePrefix' => 'car-packages',
            'fields' => [
                ['name' => 'name', 'label' => 'Paket adı', 'type' => 'text', 'required' => false],
                ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'required' => false],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarPackage::class);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarPackage::query()->create($data);

        return redirect()
            ->route('car-packages.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Paket eklendi.']);
    }

    public function edit(CarPackage $car_package): View
    {
        $this->authorize('update', $car_package);

        return view('admin.fleet.simple.form', [
            'title' => 'Paket düzenle',
            'description' => 'Paket #'.$car_package->id,
            'indexRoute' => route('car-packages.index'),
            'action' => route('car-packages.update', $car_package),
            'method' => 'PUT',
            'modelFqcn' => CarPackage::class,
            'model' => $car_package,
            'routePrefix' => 'car-packages',
            'fields' => [
                ['name' => 'name', 'label' => 'Paket adı', 'type' => 'text', 'required' => false],
                ['name' => 'description', 'label' => 'Açıklama', 'type' => 'textarea', 'required' => false],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarPackage $car_package): RedirectResponse
    {
        $this->authorize('update', $car_package);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_package->update($data);

        return redirect()
            ->route('car-packages.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Paket kaydedildi.']);
    }

    public function destroy(CarPackage $car_package): RedirectResponse
    {
        $this->authorize('delete', $car_package);
        $car_package->delete();

        return redirect()
            ->route('car-packages.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Paket kaldırıldı.']);
    }
}
