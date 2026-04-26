<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarAttribute;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarAttributeController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarAttribute::class);

        $items = CarAttribute::query()->orderBy('title')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Özellik adları',
            'description' => 'Örn. Klima, Navigasyon — pivot satırında “hangi özellik”.',
            'modelFqcn' => CarAttribute::class,
            'routePrefix' => 'car-attributes',
            'items' => $items,
            'columns' => [
                ['key' => 'title', 'label' => 'Başlık'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarAttribute::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni özellik',
            'description' => null,
            'indexRoute' => route('car-attributes.index'),
            'action' => route('car-attributes.store'),
            'method' => 'POST',
            'modelFqcn' => CarAttribute::class,
            'model' => null,
            'routePrefix' => 'car-attributes',
            'fields' => [
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => false],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarAttribute::class);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        CarAttribute::query()->create($data);

        return redirect()
            ->route('car-attributes.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Özellik eklendi.']);
    }

    public function edit(CarAttribute $car_attribute): View
    {
        $this->authorize('update', $car_attribute);

        return view('admin.fleet.simple.form', [
            'title' => 'Özellik düzenle',
            'description' => 'Kayıt #'.$car_attribute->id,
            'indexRoute' => route('car-attributes.index'),
            'action' => route('car-attributes.update', $car_attribute),
            'method' => 'PUT',
            'modelFqcn' => CarAttribute::class,
            'model' => $car_attribute,
            'routePrefix' => 'car-attributes',
            'fields' => [
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => false],
            ],
        ]);
    }

    public function update(Request $request, CarAttribute $car_attribute): RedirectResponse
    {
        $this->authorize('update', $car_attribute);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ]);
        $car_attribute->update($data);

        return redirect()
            ->route('car-attributes.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarAttribute $car_attribute): RedirectResponse
    {
        $this->authorize('delete', $car_attribute);
        $car_attribute->delete();

        return redirect()
            ->route('car-attributes.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Özellik kaldırıldı.']);
    }
}
