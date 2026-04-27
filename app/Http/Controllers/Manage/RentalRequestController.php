<?php

namespace App\Http\Controllers\Manage;

use App\Models\Car;
use App\Models\RentalRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalRequestController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', RentalRequest::class);

        $status = $request->input('status') ?? 'all';
        $status = in_array($status, ['all', 'pending', 'read']) ? $status : 'all';

        $rentalRequests = RentalRequest::query()
            ->orderByDesc('id')
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('read_at', $status === 'pending' ? null : now())->orWhereNull('read_at');
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.fleet.rental_requests.index', compact('rentalRequests'));
    }

    public function show(RentalRequest $rental_request): View
    {
        $this->authorize('view', $rental_request);

        if ($rental_request->read_at === null) {
            $rental_request->update(['read_at' => now()]);
            $rental_request->refresh();
        }

        $resolvedCars = $this->resolveRentalRequestCars($rental_request->cars);

        return view('admin.fleet.rental_requests.show', [
            'rentalRequest' => $rental_request,
            'resolvedCars' => $resolvedCars,
        ]);
    }

    /**
     * @param  array<int, mixed>|null  $cars
     * @return list<array{raw: array<string, mixed>, car: ?Car, quantity: mixed}>
     */
    private function resolveRentalRequestCars(?array $cars): array
    {
        if ($cars === null || $cars === []) {
            return [];
        }

        $out = [];

        foreach ($cars as $item) {
            if (! is_array($item)) {
                continue;
            }

            /** @var array<string, mixed> $item */
            $slug = $item['slug'] ?? null;
            $slug = is_string($slug) ? $slug : null;

            $car = ($slug !== null && $slug !== '')
                ? Car::query()->where('slug', $slug)->first()
                : null;

            $quantity = $item['adet'] ?? $item['qty'] ?? $item['quantity'] ?? null;

            $out[] = [
                'raw' => $item,
                'car' => $car,
                'quantity' => $quantity,
            ];
        }

        return $out;
    }

    public function update(Request $request, RentalRequest $rental_request): RedirectResponse
    {
        $this->authorize('update', $rental_request);

        $request->validate([
            'action' => ['required', 'in:mark_read,mark_unread'],
        ]);

        if ($request->input('action') === 'mark_read') {
            $rental_request->update(['read_at' => now()]);
            $message = 'Talep okundu olarak işaretlendi.';
        } else {
            $rental_request->update(['read_at' => null]);
            $message = 'Okunmadı olarak işaretlendi.';
        }

        return redirect()
            ->route('rental-requests.show', $rental_request)
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => $message]);
    }

    public function destroy(RentalRequest $rental_request): RedirectResponse
    {
        $this->authorize('delete', $rental_request);
        $rental_request->delete();

        return redirect()
            ->route('rental-requests.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Kiralama talebi kaldırıldı.']);
    }
}
