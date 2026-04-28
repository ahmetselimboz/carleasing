<?php

namespace App\Http\Controllers\Manage;

use App\Models\Message;
use App\Models\RentalRequest;
use App\Models\WeCallYou;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class NotificationController
{
    use AuthorizesRequests;

    public function markAllRead(): RedirectResponse
    {
        $this->authorize('viewAny', RentalRequest::class);
        $this->authorize('viewAny', Message::class);
        $this->authorize('viewAny', WeCallYou::class);

        $now = now();

        RentalRequest::query()->whereNull('read_at')->update(['read_at' => $now]);
        Message::query()->whereNull('read_at')->update(['read_at' => $now]);
        WeCallYou::query()->whereNull('read_at')->update(['read_at' => $now]);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Bildirimler güncellendi',
            'message' => 'Tüm bildirimler okundu olarak işaretlendi.',
        ]);
    }
}
