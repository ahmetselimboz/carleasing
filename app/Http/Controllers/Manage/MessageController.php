<?php

namespace App\Http\Controllers\Manage;

use App\Models\Message;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Message::class);

        $status = $request->input('status') ?? 'all';
        $status = in_array($status, ['all', 'pending', 'read'], true) ? $status : 'all';

        $messages = Message::query()
            ->orderByDesc('id')
            ->when($status === 'pending', fn ($query) => $query->whereNull('read_at'))
            ->when($status === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->paginate(20)
            ->withQueryString();

        return view('admin.support.messages.index', [
            'messages' => $messages,
        ]);
    }

    public function show(Message $message): View
    {
        $this->authorize('view', $message);

        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);
            $message->refresh();
        }

        return view('admin.support.messages.show', [
            'message' => $message,
        ]);
    }

    public function update(Request $request, Message $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $request->validate([
            'action' => ['required', 'in:mark_read,mark_unread'],
        ]);

        if ($request->input('action') === 'mark_read') {
            $message->update(['read_at' => now()]);
            $text = 'Mesaj okundu olarak işaretlendi.';
        } else {
            $message->update(['read_at' => null]);
            $text = 'Mesaj okunmadı olarak işaretlendi.';
        }

        return redirect()
            ->route('messages.show', $message)
            ->with('toast', ['type' => 'success', 'title' => 'Güncellendi', 'message' => $text]);
    }

    public function destroy(Message $message): RedirectResponse
    {
        $this->authorize('delete', $message);
        $message->delete();

        return redirect()
            ->route('messages.index')
            ->with('toast', ['type' => 'success', 'title' => 'Silindi', 'message' => 'Mesaj kaldırıldı.']);
    }
}
