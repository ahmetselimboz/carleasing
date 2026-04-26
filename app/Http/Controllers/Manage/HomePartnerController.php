<?php

namespace App\Http\Controllers\Manage;

use App\Http\Requests\Manage\StoreHomePartnerRequest;
use App\Http\Requests\Manage\UpdateHomePartnerRequest;
use App\Models\HomePartner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomePartnerController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', HomePartner::class);

        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $partners = HomePartner::query()
            ->when($status !== '', function ($query) use ($status): void {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'passive') {
                    $query->where('is_active', false);
                }
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('sort_order', $direction)
            ->orderBy('id')
            ->get();

        return view('admin.site.partners.index', compact('partners'));
    }

    public function create(): View
    {
        $this->authorize('create', HomePartner::class);

        return view('admin.site.partners.create');
    }

    public function store(StoreHomePartnerRequest $request): RedirectResponse
    {
        $partner = new HomePartner;
        $partner->name = $request->input('name');
        $partner->sort_order = ((int) HomePartner::query()->max('sort_order')) + 1;
        $partner->is_active = $request->boolean('is_active');
        $partner->save();

        return redirect()
            ->route('home-partners.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Kaydedildi',
                'message' => 'Partner eklendi.',
            ]);
    }

    public function edit(HomePartner $home_partner): View
    {
        $this->authorize('view', $home_partner);

        $partner = $home_partner;

        return view('admin.site.partners.edit', compact('partner'));
    }

    public function update(UpdateHomePartnerRequest $request, HomePartner $home_partner): RedirectResponse
    {
        $home_partner->name = $request->input('name');
        $home_partner->is_active = $request->boolean('is_active');
        $home_partner->save();

        return redirect()
            ->route('home-partners.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Güncellendi',
                'message' => 'Partner kaydedildi.',
            ]);
    }

    public function destroy(HomePartner $home_partner): RedirectResponse
    {
        $this->authorize('delete', $home_partner);
        $home_partner->delete();

        return redirect()
            ->route('home-partners.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Silindi',
                'message' => 'Partner kaldırıldı.',
            ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', HomePartner::class);

        $validated = $request->validate([
            'partner_ids' => ['required', 'array', 'min:1'],
            'partner_ids.*' => ['required', 'integer', 'exists:home_partners,id'],
        ]);

        /** @var list<int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['partner_ids'])));
        $partners = HomePartner::query()->whereIn('id', $ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            $partner = $partners->get($id);
            if ($partner) {
                $this->authorize('update', $partner);
            }
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $index => $id) {
                HomePartner::query()->whereKey($id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('home-partners.index')
            ->with('toast', [
                'type' => 'success',
                'title' => 'Sıralama güncellendi',
                'message' => 'İş ortakları sırası kaydedildi.',
            ]);
    }
}
