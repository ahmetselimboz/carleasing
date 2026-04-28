<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StorePageCategoryRequest;
use App\Http\Requests\Manage\UpdatePageCategoryRequest;
use App\Models\PageCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageCategoryController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PageCategory::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();

        $categories = PageCategory::query()
            ->withCount('pages')
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            });

        if ($sort === 'oldest') {
            $categories->oldest();
        } elseif ($sort === 'name_asc') {
            $categories->orderBy('name')->orderByDesc('id');
        } elseif ($sort === 'name_desc') {
            $categories->orderByDesc('name')->orderByDesc('id');
        } else {
            $categories->latest();
        }

        return view('admin.site.page-categories.index', [
            'categories' => $categories->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PageCategory::class);

        return view('admin.site.page-categories.create');
    }

    public function store(StorePageCategoryRequest $request): RedirectResponse
    {
        $category = new PageCategory;
        $category->fill($request->safe()->except(['magicbox']));
        $category->magicbox = $this->parseMagicbox($request->input('magicbox'));
        $category->save();

        return redirect()
            ->route('page-categories.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Sayfa kategorisi eklendi.',
            ]);
    }

    public function edit(PageCategory $page_category): View
    {
        $this->authorize('view', $page_category);

        $category = $page_category;

        return view('admin.site.page-categories.edit', compact('category'));
    }

    public function update(UpdatePageCategoryRequest $request, PageCategory $page_category): RedirectResponse
    {
        $page_category->fill($request->safe()->except(['magicbox']));
        $page_category->magicbox = $this->parseMagicbox($request->input('magicbox'));
        $page_category->save();

        return redirect()
            ->route('page-categories.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Sayfa kategorisi kaydedildi.',
            ]);
    }

    public function destroy(PageCategory $page_category): RedirectResponse
    {
        $this->authorize('delete', $page_category);

        if ($page_category->pages()->exists()) {
            return redirect()
                ->route('page-categories.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Silinemedi',
                    'message' => 'Bu kategoriye bağlı sayfalar olduğu için silinemez.',
                ]);
        }

        $page_category->delete();

        return redirect()
            ->route('page-categories.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Sayfa kategorisi kaldırıldı.',
            ]);
    }

    /**
     * @return array<mixed>|null
     */
    private function parseMagicbox(mixed $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : null;
    }
}
