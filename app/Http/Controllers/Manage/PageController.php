<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StorePageRequest;
use App\Http\Requests\Manage\UpdatePageRequest;
use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Page::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();
        $categoryId = (int) $request->integer('category_id');

        $pages = Page::query()
            ->with('category')
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($categoryId > 0, function ($query) use ($categoryId): void {
                $query->where('page_category_id', $categoryId);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        if ($sort === 'oldest') {
            $pages->oldest();
        } elseif ($sort === 'title_asc') {
            $pages->orderBy('title')->orderByDesc('id');
        } elseif ($sort === 'title_desc') {
            $pages->orderByDesc('title')->orderByDesc('id');
        } else {
            $pages->latest();
        }

        return view('admin.site.pages.index', [
            'pages' => $pages->paginate(15),
            'categories' => PageCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);

        return view('admin.site.pages.create', [
            'categories' => PageCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $page = new Page;
        $page->fill($request->safe()->except(['slug', 'magicbox']));
        $page->slug = $this->makeUniqueSlug($request->input('slug'), $request->input('title'));
        $page->slug_hash = abs(crc32($page->slug)) % 2147483647;
        $page->magicbox = $this->parseMagicbox($request->input('magicbox'));
        $page->save();

        return redirect()
            ->route('pages.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Sayfa eklendi.',
            ]);
    }

    public function edit(Page $page): View
    {
        $this->authorize('view', $page);

        return view('admin.site.pages.edit', [
            'page' => $page,
            'categories' => PageCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $page->fill($request->safe()->except(['slug', 'magicbox']));
        $page->slug = $this->makeUniqueSlug($request->input('slug'), $request->input('title'), $page->id);
        $page->slug_hash = abs(crc32($page->slug)) % 2147483647;
        $page->magicbox = $this->parseMagicbox($request->input('magicbox'));
        $page->save();

        return redirect()
            ->route('pages.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Sayfa kaydedildi.',
            ]);
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);
        $page->delete();

        return redirect()
            ->route('pages.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Sayfa kaldırıldı.',
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

    private function makeUniqueSlug(?string $slugInput, ?string $title, ?int $ignoreId = null): string
    {
        $slugBase = Str::slug(trim((string) $slugInput));
        if ($slugBase === '') {
            $slugBase = Str::slug(trim((string) $title));
        }
        if ($slugBase === '') {
            $slugBase = 'sayfa';
        }

        $slug = $slugBase;
        $suffix = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        return Page::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
