@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('messages.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Mesajlar
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Mesaj #{{ $message->id }}</h2>
                    <p class="text-slate-500 text-sm mt-1">{{ $message->created_at?->format('d.m.Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('update', $message)
                        @if ($message->read_at === null)
                            <form method="POST" action="{{ route('messages.update', $message) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="mark_read">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                                    Okundu işaretle
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('messages.update', $message) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="mark_unread">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">
                                    Okunmadı yap
                                </button>
                            </form>
                        @endif
                    @endcan
                    @can('delete', $message)
                        <form method="POST" action="{{ route('messages.destroy', $message) }}"
                            onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-soft">
                                Sil
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-semibold text-slate-800">İletişim</h3>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Kategori</dt>
                        <dd class="text-slate-800 text-right">{{ $message->categoryLabel() }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Ad Soyad</dt>
                        <dd class="text-slate-800 text-right">{{ trim(($message->name ?: '') . ' ' . ($message->surname ?: '')) ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">E-posta</dt>
                        <dd class="text-slate-800 text-right break-all">{{ $message->email ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Telefon</dt>
                        <dd class="text-slate-800 text-right">{{ $message->phone_number ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 pb-2">
                        <dt class="text-slate-500">Durum</dt>
                        <dd class="text-slate-800 text-right">{{ $message->read_at ? 'Okundu' : 'Okunmadı' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-semibold text-slate-800">Mesaj İçeriği</h3>
                <p class="text-sm text-slate-700 whitespace-pre-line leading-6">{{ $message->content ?: '—' }}</p>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-2 text-xs text-slate-500">
            <p><span class="font-medium text-slate-600">IP:</span> {{ $message->ip_address ?: '—' }}</p>
            <p class="break-all"><span class="font-medium text-slate-600">User-Agent:</span> {{ $message->user_agent ?: '—' }}</p>
        </div>
    </div>
@endsection
