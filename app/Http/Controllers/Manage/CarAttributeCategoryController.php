<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarAttributeCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarAttributeCategoryController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarAttributeCategory::class);

        $items = CarAttributeCategory::query()->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Özellik kategorileri',
            'description' => 'Örn. Güvenlik, Konfor — araç özellik pivotları için gruplar.',
            'modelFqcn' => CarAttributeCategory::class,
            'routePrefix' => 'car-attribute-categories',
            'items' => $items,
            'columns' => [
                ['key' => 'name', 'label' => 'Ad'],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarAttributeCategory::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni kategori',
            'description' => null,
            'indexRoute' => route('car-attribute-categories.index'),
            'action' => route('car-attribute-categories.store'),
            'method' => 'POST',
            'modelFqcn' => CarAttributeCategory::class,
            'model' => null,
            'routePrefix' => 'car-attribute-categories',
            'fields' => [
                ['name' => 'name', 'label' => 'Kategori adı', 'type' => 'text', 'required' => false],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarAttributeCategory::class);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarAttributeCategory::query()->create($data);

        return redirect()
            ->route('car-attribute-categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Kategori eklendi.']);
    }

    public function edit(CarAttributeCategory $car_attribute_category): View
    {
        $this->authorize('update', $car_attribute_category);

        return view('admin.fleet.simple.form', [
            'title' => 'Kategori düzenle',
            'description' => 'Kayıt #'.$car_attribute_category->id,
            'indexRoute' => route('car-attribute-categories.index'),
            'action' => route('car-attribute-categories.update', $car_attribute_category),
            'method' => 'PUT',
            'modelFqcn' => CarAttributeCategory::class,
            'model' => $car_attribute_category,
            'routePrefix' => 'car-attribute-categories',
            'fields' => [
                ['name' => 'name', 'label' => 'Kategori adı', 'type' => 'text', 'required' => false],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarAttributeCategory $car_attribute_category): RedirectResponse
    {
        $this->authorize('update', $car_attribute_category);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_attribute_category->update($data);

        return redirect()
            ->route('car-attribute-categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarAttributeCategory $car_attribute_category): RedirectResponse
    {
        $this->authorize('delete', $car_attribute_category);
        $car_attribute_category->delete();

        return redirect()
            ->route('car-attribute-categories.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Kategori kaldırıldı.']);
    }
}
