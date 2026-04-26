<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreFaqRequest;
use App\Http\Requests\Manage\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FaqController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Faq::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $faqs = Faq::query()
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('question', 'like', "%{$search}%")
                        ->orWhere('answer_body', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort === 'question' ? 'question' : 'sort_order', $direction)
            ->orderBy('id')
            ->get();

        return view('admin.site.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        $this->authorize('create', Faq::class);

        return view('admin.site.faqs.create');
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        $faq = new Faq;
        $faq->sort_order = ((int) Faq::query()->max('sort_order')) + 1;
        $faq->question = $request->input('question');
        $faq->answer_body = $request->input('answer_body');
        $faq->answer = $faq->answer_body !== null && $faq->answer_body !== ''
            ? mb_substr($faq->answer_body, 0, 255)
            : null;
        $faq->is_active = $request->boolean('is_active');
        $faq->save();

        return redirect()
            ->route('faqs.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'SSS maddesi eklendi.',
            ]);
    }

    public function edit(Faq $faq): View
    {
        $this->authorize('view', $faq);

        return view('admin.site.faqs.edit', compact('faq'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->question = $request->input('question');
        $faq->answer_body = $request->input('answer_body');
        $faq->answer = $faq->answer_body !== null && $faq->answer_body !== ''
            ? mb_substr($faq->answer_body, 0, 255)
            : null;
        $faq->is_active = $request->boolean('is_active');
        $faq->save();

        return redirect()
            ->route('faqs.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'SSS kaydedildi.',
            ]);
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $this->authorize('delete', $faq);
        $faq->delete();

        return redirect()
            ->route('faqs.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'SSS arşivlendi.',
            ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Faq::class);

        $validated = $request->validate([
            'faq_ids' => ['required', 'array', 'min:1'],
            'faq_ids.*' => ['required', 'integer', 'exists:faqs,id'],
        ]);

        /** @var list<int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['faq_ids'])));
        $faqs = Faq::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $faq = $faqs->get($id);
            if ($faq) {
                $this->authorize('update', $faq);
            }
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $index => $id) {
                Faq::query()->whereKey($id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('faqs.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Sıralama güncellendi',
                'message' => 'SSS sırası kaydedildi.',
            ]);
    }
}
