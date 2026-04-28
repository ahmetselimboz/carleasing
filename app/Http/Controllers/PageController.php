<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\View\View;

class PageController
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        $relatedPages = Page::query()
            ->where('is_active', true)
            ->where('page_category_id', $page->page_category_id)
            ->whereKeyNot($page->id)
            ->orderBy('title')
            ->limit(8)
            ->get();

        $categories = PageCategory::query()
            ->where('is_active', true)
            ->withCount(['pages' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('theme.v1.page', compact('page', 'relatedPages', 'categories'));
    }
}
