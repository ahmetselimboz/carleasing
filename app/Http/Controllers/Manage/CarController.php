<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreCarRequest;
use App\Http\Requests\Manage\UpdateCarRequest;
use App\Models\Car;
use App\Models\CarAttribute;
use App\Models\CarAttributeCategory;
use App\Models\CarAttributePivot;
use App\Models\CarAttributeValue;
use App\Support\MagicboxForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CarController
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Car::class);

        $cars = Car::query()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.fleet.cars.index', compact('cars'));
    }

    public function create(): View
    {
        $this->authorize('create', Car::class);

        $mbRows = MagicboxForm::toRows(null);

        return view('admin.fleet.cars.create', compact('mbRows'));
    }

    public function store(StoreCarRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $car = new Car;
        $this->fillCarFromValidated($car, $validated, null);

        if ($request->hasFile('image')) {
            $car->image = $request->file('image')->store('cars', 'public');
        }

        $car->save();

        return redirect()
            ->route('cars.edit', $car)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Araç oluşturuldu',
                'message' => 'Özellikler ve fiyat matrisini ekleyebilirsiniz.',
            ]);
    }

    public function edit(Car $car): View
    {
        $this->authorize('view', $car);

        $car->load([
            'priceMatrices.package',
            'priceMatrices.duration',
            'priceMatrices.kilometerOption',
            'priceMatrices.downPayment',
            'attributePivots.attribute',
            'attributePivots.category',
            'attributePivots.value',
        ]);

        $attributeCategories = CarAttributeCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $attributes = CarAttribute::query()->orderBy('title')->get();
        $attributeValues = CarAttributeValue::query()->orderBy('title')->get();

        $mbRows = MagicboxForm::toRows($car->magicbox);

        return view('admin.fleet.cars.edit', compact(
            'car',
            'attributeCategories',
            'attributes',
            'attributeValues',
            'mbRows',
        ));
    }

    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $validated = $request->validated();
        $this->fillCarFromValidated($car, $validated, $car->id);

        if ($request->hasFile('image')) {
            $this->deleteStoredCarImage($car->image);
            $car->image = $request->file('image')->store('cars', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredCarImage($car->image);
            $car->image = null;
        }

        $car->save();

        return redirect()
            ->route('cars.edit', $car)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Araç güncellendi',
                'message' => 'Değişiklikler kaydedildi.',
            ]);
    }

    public function destroy(Car $car): RedirectResponse
    {
        $this->authorize('delete', $car);
        $car->delete();

        return redirect()
            ->route('cars.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Araç silindi',
                'message' => 'Kayıt arşivlendi (geri alınabilir).',
            ]);
    }

    public function storeAttributePivot(Request $request, Car $car): RedirectResponse
    {
        $this->authorize('update', $car);

        $validated = $request->validate([
            'attribute_id' => ['required', 'exists:car_attributes,id'],
            'attribute_category_id' => ['required', 'exists:car_attribute_categories,id'],
            'attribute_value_id' => ['required', 'exists:car_attribute_values,id'],
        ]);

        $exists = CarAttributePivot::query()
            ->where('car_id', $car->id)
            ->where('attribute_id', $validated['attribute_id'])
            ->where('attribute_category_id', $validated['attribute_category_id'])
            ->where('attribute_value_id', $validated['attribute_value_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['pivot' => 'Bu özellik kombinasyonu zaten ekli.'])
                ->withInput();
        }

        CarAttributePivot::query()->create([
            'car_id' => $car->id,
            'attribute_id' => $validated['attribute_id'],
            'attribute_category_id' => $validated['attribute_category_id'],
            'attribute_value_id' => $validated['attribute_value_id'],
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Özellik eklendi',
            'message' => 'Araç özellik satırı kaydedildi.',
        ]);
    }

    public function destroyAttributePivot(Car $car, CarAttributePivot $car_attribute_pivot): RedirectResponse
    {
        if ($car_attribute_pivot->car_id !== $car->id) {
            abort(404);
        }

        $this->authorize('delete', $car_attribute_pivot);
        $car_attribute_pivot->delete();

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Özellik kaldırıldı',
            'message' => 'Satır silindi.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function fillCarFromValidated(Car $car, array $validated, ?int $ignoreId): void
    {
        $magicbox = $validated['magicbox'] ?? null;
        $slugInput = isset($validated['slug']) ? trim((string) $validated['slug']) : '';

        foreach (['slug', 'magicbox', 'image', 'remove_image'] as $key) {
            unset($validated[$key]);
        }

        $car->fill($validated);

        $slugBase = $slugInput !== '' ? Str::slug($slugInput) : '';
        if ($slugBase === '') {
            $slugBase = Str::slug((string) ($validated['title'] ?? ''));
        }
        if ($slugBase === '') {
            $slugBase = Str::slug(trim((string) (($validated['brand'] ?? '').' '.($validated['model'] ?? ''))));
        }
        if ($slugBase === '') {
            $slugBase = 'arac';
        }

        $car->slug = $this->uniqueCarSlug($slugBase, $ignoreId);
        $car->slug_hash = abs(crc32($car->slug)) % 2147483647;
        $car->magicbox = $magicbox;
    }

    private function uniqueCarSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base !== '' ? $base : 'arac';
        $i = 0;

        while (Car::query()
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    private function deleteStoredCarImage(?string $path): void
    {
        if ($path !== null && $path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
