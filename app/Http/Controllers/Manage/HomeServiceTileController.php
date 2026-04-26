<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreHomeServiceTileRequest;
use App\Http\Requests\Manage\UpdateHomeServiceTileRequest;
use App\Models\HomeServiceTile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeServiceTileController
{
    use AuthorizesRequests;

    private const DIR = 'home-service-tiles';

    public function index(Request $request): View
    {
        $this->authorize('viewAny', HomeServiceTile::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $tiles = HomeServiceTile::query()
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort === 'title' ? 'title' : 'sort_order', $direction)
            ->orderBy('id')
            ->get();

        return view('admin.site.service-tiles.index', compact('tiles'));
    }

    public function create(): View
    {
        $this->authorize('create', HomeServiceTile::class);

        $orderTiles = HomeServiceTile::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'sort_order']);

        return view('admin.site.service-tiles.create', compact('orderTiles'));
    }

    public function store(StoreHomeServiceTileRequest $request): RedirectResponse
    {
        $tile = new HomeServiceTile;
        $tile->fill($request->safe()->except(['image']));
        $tile->sort_order = ((int) HomeServiceTile::query()->max('sort_order')) + 1;

        if ($request->hasFile('image')) {
            $tile->image = $request->file('image')->store(self::DIR, 'public');
        }

        $tile->save();

        return redirect()
            ->route('home-service-tiles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Hizmet kutusu eklendi.',
            ]);
    }

    public function edit(HomeServiceTile $home_service_tile): View
    {
        $this->authorize('view', $home_service_tile);

        $tile = $home_service_tile;
        $orderTiles = HomeServiceTile::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'sort_order']);

        return view('admin.site.service-tiles.edit', compact('tile', 'orderTiles'));
    }

    public function update(UpdateHomeServiceTileRequest $request, HomeServiceTile $home_service_tile): RedirectResponse
    {
        $tile = $home_service_tile;
        $tile->fill($request->safe()->except(['image', 'remove_image']));
        if ($request->hasFile('image')) {
            $this->deleteStored($tile->image);
            $tile->image = $request->file('image')->store(self::DIR, 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStored($tile->image);
            $tile->image = null;
        }

        $tile->save();

        return redirect()
            ->route('home-service-tiles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Hizmet kutusu kaydedildi.',
            ]);
    }

    public function destroy(HomeServiceTile $home_service_tile): RedirectResponse
    {
        $this->authorize('delete', $home_service_tile);
        $this->deleteStored($home_service_tile->image);
        $home_service_tile->delete();

        return redirect()
            ->route('home-service-tiles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Kayıt kaldırıldı.',
            ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', HomeServiceTile::class);

        $validated = $request->validate([
            'tile_ids' => ['required', 'array', 'min:1'],
            'tile_ids.*' => ['required', 'integer', 'exists:home_service_tiles,id'],
        ]);

        /** @var list<int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['tile_ids'])));
        $tiles = HomeServiceTile::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $tile = $tiles->get($id);
            if ($tile) {
                $this->authorize('update', $tile);
            }
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $index => $id) {
                HomeServiceTile::query()->whereKey($id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('home-service-tiles.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Sıralama güncellendi',
                'message' => 'Hizmet kutuları sırası kaydedildi.',
            ]);
    }

    private function deleteStored(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
