<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreSliderRequest;
use App\Http\Requests\Manage\UpdateSliderRequest;
use App\Models\Slider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SliderController
{
    use AuthorizesRequests;

    private const DIR = 'home-slides';

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Slider::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $sliders = Slider::query()
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('badge', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('title_highlight', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort === 'title' ? 'title' : 'sort_order', $direction)
            ->orderBy('id')
            ->get();

        return view('admin.site.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        $this->authorize('create', Slider::class);

        return view('admin.site.sliders.create');
    }

    public function store(StoreSliderRequest $request): RedirectResponse
    {
        $slider = new Slider;
        $slider->fill($request->safe()->except(['image_1', 'image_2']));
        $slider->sort_order = ((int) Slider::query()->max('sort_order')) + 1;

        if ($request->hasFile('image_1')) {
            $slider->image_1 = $request->file('image_1')->store(self::DIR, 'public');
        }
        if ($request->hasFile('image_2')) {
            $slider->image_2 = $request->file('image_2')->store(self::DIR, 'public');
        }

        $slider->save();

        return redirect()
            ->route('sliders.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Hero slaytı oluşturuldu.',
            ]);
    }

    public function edit(Slider $slider): View
    {
        $this->authorize('view', $slider);

        return view('admin.site.sliders.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, Slider $slider): RedirectResponse
    {
        $slider->fill($request->safe()->except(['image_1', 'image_2', 'remove_image_1', 'remove_image_2']));

        if ($request->hasFile('image_1')) {
            $this->deleteStored($slider->image_1);
            $slider->image_1 = $request->file('image_1')->store(self::DIR, 'public');
        } elseif ($request->boolean('remove_image_1')) {
            $this->deleteStored($slider->image_1);
            $slider->image_1 = null;
        }

        if ($request->hasFile('image_2')) {
            $this->deleteStored($slider->image_2);
            $slider->image_2 = $request->file('image_2')->store(self::DIR, 'public');
        } elseif ($request->boolean('remove_image_2')) {
            $this->deleteStored($slider->image_2);
            $slider->image_2 = null;
        }

        $slider->save();

        return redirect()
            ->route('sliders.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Slayt kaydedildi.',
            ]);
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $this->authorize('delete', $slider);
        $this->deleteStored($slider->image_1);
        $this->deleteStored($slider->image_2);
        $slider->delete();

        return redirect()
            ->route('sliders.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Slayt kaldırıldı.',
            ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        dd($request->all());
 
        $this->authorize('viewAny', Slider::class);

        $validated = $request->validate([
            'slider_ids' => ['required', 'array', 'min:1'],
            'slider_ids.*' => ['required', 'integer', 'exists:sliders,id'],
        ]);

        /** @var list<int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['slider_ids'])));
        $sliders = Slider::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $slide = $sliders->get($id);
            if ($slide) {
                $this->authorize('update', $slide);
            }
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $index => $id) {
                Slider::query()->whereKey($id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('sliders.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Sıralama güncellendi',
                'message' => 'Slayt sırası kaydedildi.',
            ]);
    }

    private function deleteStored(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
