<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarDownPayment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarDownPaymentController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', CarDownPayment::class);

        $items = CarDownPayment::query()->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.fleet.simple.index', [
            'title' => 'Peşinat seçenekleri',
            'description' => 'Fiyat matrisinde kullanılacak peşinat kalemleri.',
            'modelFqcn' => CarDownPayment::class,
            'routePrefix' => 'car-down-payments',
            'items' => $items,
            'columns' => [
                ['key' => 'amount', 'label' => 'Tutar / etiket'],
                ['key' => 'is_active', 'label' => 'Durum', 'type' => 'bool'],
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CarDownPayment::class);

        return view('admin.fleet.simple.form', [
            'title' => 'Yeni peşinat',
            'description' => 'Örn. yüzde veya tutar metni (sitede nasıl göstereceğinize göre).',
            'indexRoute' => route('car-down-payments.index'),
            'action' => route('car-down-payments.store'),
            'method' => 'POST',
            'modelFqcn' => CarDownPayment::class,
            'model' => null,
            'routePrefix' => 'car-down-payments',
            'fields' => [
                ['name' => 'amount', 'label' => 'Tutar / etiket', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarDownPayment::class);

        $data = $request->validate([
            'amount' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        CarDownPayment::query()->create($data);

        return redirect()
            ->route('car-down-payments.index')
            ->with('toast', ['type' => 'success', 'title' => 'Kaydedildi', 'message' => 'Peşinat seçeneği eklendi.']);
    }

    public function edit(CarDownPayment $car_down_payment): View
    {
        $this->authorize('update', $car_down_payment);

        return view('admin.fleet.simple.form', [
            'title' => 'Peşinat düzenle',
            'description' => 'Kalem #'.$car_down_payment->id,
            'indexRoute' => route('car-down-payments.index'),
            'action' => route('car-down-payments.update', $car_down_payment),
            'method' => 'PUT',
            'modelFqcn' => CarDownPayment::class,
            'model' => $car_down_payment,
            'routePrefix' => 'car-down-payments',
            'fields' => [
                ['name' => 'amount', 'label' => 'Tutar / etiket', 'type' => 'text', 'required' => true],
                ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox'],
            ],
        ]);
    }

    public function update(Request $request, CarDownPayment $car_down_payment): RedirectResponse
    {
        $this->authorize('update', $car_down_payment);

        $data = $request->validate([
            'amount' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $car_down_payment->update($data);

        return redirect()
            ->route('car-down-payments.index')
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => 'Kayıt kaydedildi.']);
    }

    public function destroy(CarDownPayment $car_down_payment): RedirectResponse
    {
        $this->authorize('delete', $car_down_payment);
        $car_down_payment->delete();

        return redirect()
            ->route('car-down-payments.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Peşinat kaldırıldı.']);
    }
}
