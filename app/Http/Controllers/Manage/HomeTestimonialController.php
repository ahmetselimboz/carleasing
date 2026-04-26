<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreHomeTestimonialRequest;
use App\Http\Requests\Manage\UpdateHomeTestimonialRequest;
use App\Models\HomeTestimonial;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeTestimonialController
{
    use AuthorizesRequests;

    private const DIR = 'home-testimonials';

    public function index(): View
    {
        $this->authorize('viewAny', HomeTestimonial::class);

        $testimonials = HomeTestimonial::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.site.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        $this->authorize('create', HomeTestimonial::class);

        return view('admin.site.testimonials.create');
    }

    public function store(StoreHomeTestimonialRequest $request): RedirectResponse
    {
        $row = new HomeTestimonial;
        $row->fill($request->safe()->except(['avatar']));
        $row->sort_order = (int) ($request->input('sort_order') ?? 0);
        $row->rating = (int) ($request->input('rating') ?? 5);

        if ($request->hasFile('avatar')) {
            $row->avatar = $request->file('avatar')->store(self::DIR, 'public');
        }

        $row->save();

        return redirect()
            ->route('home-testimonials.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Yorum eklendi.',
            ]);
    }

    public function edit(HomeTestimonial $home_testimonial): View
    {
        $this->authorize('view', $home_testimonial);

        $testimonial = $home_testimonial;

        return view('admin.site.testimonials.edit', compact('testimonial'));
    }

    public function update(UpdateHomeTestimonialRequest $request, HomeTestimonial $home_testimonial): RedirectResponse
    {
        $row = $home_testimonial;
        $row->fill($request->safe()->except(['avatar', 'remove_avatar']));
        $row->sort_order = (int) ($request->input('sort_order') ?? 0);
        $row->rating = (int) ($request->input('rating') ?? 5);

        if ($request->hasFile('avatar')) {
            $this->deleteStored($row->avatar);
            $row->avatar = $request->file('avatar')->store(self::DIR, 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteStored($row->avatar);
            $row->avatar = null;
        }

        $row->save();

        return redirect()
            ->route('home-testimonials.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Yorum kaydedildi.',
            ]);
    }

    public function destroy(HomeTestimonial $home_testimonial): RedirectResponse
    {
        $this->authorize('delete', $home_testimonial);
        $this->deleteStored($home_testimonial->avatar);
        $home_testimonial->delete();

        return redirect()
            ->route('home-testimonials.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Yorum kaldırıldı.',
            ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', HomeTestimonial::class);

        $validated = $request->validate([
            'testimonial_ids' => ['required', 'array', 'min:1'],
            'testimonial_ids.*' => ['required', 'integer', 'exists:home_testimonials,id'],
        ]);

        /** @var list<int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['testimonial_ids'])));
        $testimonials = HomeTestimonial::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $testimonial = $testimonials->get($id);
            if ($testimonial) {
                $this->authorize('update', $testimonial);
            }
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $index => $id) {
                HomeTestimonial::query()->whereKey($id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('home-testimonials.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Sıralama güncellendi',
                'message' => 'Yorum sırası kaydedildi.',
            ]);
    }

    private function deleteStored(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
