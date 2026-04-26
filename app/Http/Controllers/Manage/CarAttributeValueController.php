<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarAttributeValue;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarAttributeValueController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarAttributeValue::class);

        $items = CarAttributeValue::query()->orderBy('title')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Özellik değerleri',
            'description' => 'Örn. Var / Yok / Sunroof — pivot satırında seçilen değer.',
            'modelFqcn' => CarAttributeValue::class,
            'routePrefix' => 'car-attribute-values',
            'items' => $items,
            'columns' => [
                ['key' => 'title', 'label' => 'Değer'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarAttributeValue::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni değer',
            'description' => null,
            'indexRoute' => route('car-attribute-values.index'),
            'action' => route('car-attribute-values.store'),
            'method' => 'POST',
            'modelFqcn' => CarAttributeValue::class,
            'model' => null,
            'routePrefix' => 'car-attribute-values',
            'fields' => [
                ['name' => 'title', 'label' => 'Değer metni', 'type' => 'text', 'required' => false],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarAttributeValue::class);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        CarAttributeValue::query()->create($data);

        return redirect()
            ->route('car-attribute-values.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Değer eklendi.']);
    }

    public function edit(CarAttributeValue $car_attribute_value): View
    {
        $this->authorize('update', $car_attribute_value);

        return view('admin.fleet.simple.form', [
            'title' => 'Değer düzenle',
            'description' => 'Kayıt #'.$car_attribute_value->id,
            'indexRoute' => route('car-attribute-values.index'),
            'action' => route('car-attribute-values.update', $car_attribute_value),
            'method' => 'PUT',
            'modelFqcn' => CarAttributeValue::class,
            'model' => $car_attribute_value,
            'routePrefix' => 'car-attribute-values',
            'fields' => [
                ['name' => 'title', 'label' => 'Değer metni', 'type' => 'text', 'required' => false],
            ],
        ]);
    }

    public function update(Request $request, CarAttributeValue $car_attribute_value): RedirectResponse
    {
        $this->authorize('update', $car_attribute_value);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);
        $car_attribute_value->update($data);

        return redirect()
            ->route('car-attribute-values.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarAttributeValue $car_attribute_value): RedirectResponse
    {
        $this->authorize('delete', $car_attribute_value);
        $car_attribute_value->delete();

        return redirect()
            ->route('car-attribute-values.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Değer kaldırıldı.']);
    }
}
