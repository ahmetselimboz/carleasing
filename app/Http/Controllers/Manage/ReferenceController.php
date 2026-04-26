<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreReferenceRequest;
use App\Http\Requests\Manage\UpdateReferenceRequest;
use App\Models\Reference;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReferenceController
{
    use AuthorizesRequests;

    private const DIR = 'references';

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Reference::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();

        $references = Reference::query()
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            });

        if ($sort === 'oldest') {
            $references->oldest();
        } elseif ($sort === 'name_asc') {
            $references->orderBy('name')->orderByDesc('id');
        } elseif ($sort === 'name_desc') {
            $references->orderByDesc('name')->orderByDesc('id');
        } else {
            $references->latest();
        }

        return view('admin.site.references.index', [
            'references' => $references->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Reference::class);

        return view('admin.site.references.create');
    }

    public function store(StoreReferenceRequest $request): RedirectResponse
    {
        $reference = new Reference;
        $reference->fill($request->safe()->except(['image', 'magicbox']));
        $reference->magicbox = $this->parseMagicbox($request->input('magicbox'));

        if ($request->hasFile('image')) {
            $reference->image = $request->file('image')->store(self::DIR, 'public');
        }

        $reference->save();

        return redirect()
            ->route('references.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Referans eklendi.',
            ]);
    }

    public function edit(Reference $reference): View
    {
        $this->authorize('view', $reference);

        return view('admin.site.references.edit', compact('reference'));
    }

    public function update(UpdateReferenceRequest $request, Reference $reference): RedirectResponse
    {
        $reference->fill($request->safe()->except(['image', 'magicbox']));
        $reference->magicbox = $this->parseMagicbox($request->input('magicbox'));

        if ($request->hasFile('image')) {
            $this->deleteStored($reference->image);
            $reference->image = $request->file('image')->store(self::DIR, 'public');
        }

        $reference->save();

        return redirect()
            ->route('references.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Referans kaydedildi.',
            ]);
    }

    public function destroy(Reference $reference): RedirectResponse
    {
        $this->authorize('delete', $reference);
        $this->deleteStored($reference->image);
        $reference->delete();

        return redirect()
            ->route('references.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Referans kaldırıldı.',
            ]);
    }

    /**
     * @return array<mixed>|null
     */
    private function parseMagicbox(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function deleteStored(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
