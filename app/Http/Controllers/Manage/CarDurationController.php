<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarDuration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarDurationController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarDuration::class);

        $items = CarDuration::query()->orderBy('months')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Kiralama süreleri',
            'description' => 'Ay cinsinden süre seçenekleri (örn. 12, 24, 36).',
            'modelFqcn' => CarDuration::class,
            'routePrefix' => 'car-durations',
            'items' => $items,
            'columns' => [
                ['key' => 'months', 'label' => 'Ay'],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarDuration::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni süre',
            'description' => null,
            'indexRoute' => route('car-durations.index'),
            'action' => route('car-durations.store'),
            'method' => 'POST',
            'modelFqcn' => CarDuration::class,
            'model' => null,
            'routePrefix' => 'car-durations',
            'fields' => [
                ['name' => 'months', 'label' => 'Ay (metin)', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarDuration::class);

        $data = $request->validate([
            'months' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarDuration::query()->create($data);

        return redirect()
            ->route('car-durations.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Süre eklendi.']);
    }

    public function edit(CarDuration $car_duration): View
    {
        $this->authorize('update', $car_duration);

        return view('admin.fleet.simple.form', [
            'title' => 'Süre düzenle',
            'description' => 'Kayıt #'.$car_duration->id,
            'indexRoute' => route('car-durations.index'),
            'action' => route('car-durations.update', $car_duration),
            'method' => 'PUT',
            'modelFqcn' => CarDuration::class,
            'model' => $car_duration,
            'routePrefix' => 'car-durations',
            'fields' => [
                ['name' => 'months', 'label' => 'Ay (metin)', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarDuration $car_duration): RedirectResponse
    {
        $this->authorize('update', $car_duration);

        $data = $request->validate([
            'months' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_duration->update($data);

        return redirect()
            ->route('car-durations.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarDuration $car_duration): RedirectResponse
    {
        $this->authorize('delete', $car_duration);
        $car_duration->delete();

        return redirect()
            ->route('car-durations.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Süre kaldırıldı.']);
    }
}
