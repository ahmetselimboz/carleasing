<?php

namespace App\Http\Controllers\Manage;

use App\Models\CarDownPayment;
use App\Models\CarDuration;
use App\Models\CarKilometerOption;
use App\Models\CarPackage;
use App\Models\WeCallYou;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeCallYouController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', WeCallYou::class);

        $status = $request->input('status') ?? 'all';
        $status = in_array($status, ['all', 'pending', 'read'], true) ? $status : 'all';

        $items = WeCallYou::query()
            ->with('car:id,title,slug')
            ->orderByDesc('id')
            ->when($status === 'pending', fn ($q) => $q->whereNull('read_at'))
            ->when($status === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->paginate(20)
            ->withQueryString();

        return view('admin.support.we-call-you.index', [
            'items' => $items,
            'status' => $status,
        ]);
    }

    public function show(WeCallYou $we_call_you): View
    {
        $this->authorize('view', $we_call_you);

        if ($we_call_you->read_at === null) {
            $we_call_you->update(['read_at' => now()]);
            $we_call_you->refresh();
        }

        $we_call_you->loadMissing('car');
        $cfg = data_get($we_call_you->magicbox, 'config', []);

        $configDisplay = [];

        if (isset($cfg['package_id'])) {
            $configDisplay['Paket'] = CarPackage::query()->whereKey((int) $cfg['package_id'])->value('name') ?? ('#'.$cfg['package_id']);
        }
        if (isset($cfg['duration_id'])) {
            $months = CarDuration::query()->whereKey((int) $cfg['duration_id'])->value('months');
            $configDisplay['Süre'] = $months ? ($months.' ay') : ('#'.$cfg['duration_id']);
        }
        if (isset($cfg['kilometer_id'])) {
            $km = CarKilometerOption::query()->whereKey((int) $cfg['kilometer_id'])->value('kilometer');
            $configDisplay['Yıllık km'] = $km ?? ('#'.$cfg['kilometer_id']);
        }
        if (isset($cfg['down_payment_id'])) {
            $dp = CarDownPayment::query()->whereKey((int) $cfg['down_payment_id'])->value('amount');
            $configDisplay['Peşinat'] = $dp ?? ('#'.$cfg['down_payment_id']);
        }

        return view('admin.support.we-call-you.show', [
            'item' => $we_call_you,
            'configDisplay' => $configDisplay,
        ]);
    }

    public function update(Request $request, WeCallYou $we_call_you): RedirectResponse
    {
        $this->authorize('update', $we_call_you);

        $request->validate([
            'action' => ['required', 'in:mark_read,mark_unread'],
        ]);

        if ($request->input('action') === 'mark_read') {
            $we_call_you->update(['read_at' => now()]);
            $msg = 'Talep okundu olarak işaretlendi.';
        } else {
            $we_call_you->update(['read_at' => null]);
            $msg = 'Talep okunmadı olarak işaretlendi.';
        }

        return redirect()
            ->route('we-call-you.show', $we_call_you)
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => $msg]);
    }

    public function destroy(WeCallYou $we_call_you): RedirectResponse
    {
        $this->authorize('delete', $we_call_you);
        $we_call_you->delete();

        return redirect()
            ->route('we-call-you.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Talep kaldırıldı.']);
    }
}
