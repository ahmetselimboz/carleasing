@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('we-call-you.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Geri arama talepleri
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Talep #{{ $item->id }}</h2>
                    <p class="text-slate-500 text-sm mt-1">{{ $item->created_at?->format('d.m.Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('update', $item)
                        @if ($item->read_at === null)
                            <form method="POST" action="{{ route('we-call-you.update', $item) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="mark_read">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                                    Okundu işaretle
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('we-call-you.update', $item) }}">
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
                    @can('delete', $item)
                        <form method="POST" action="{{ route('we-call-you.destroy', $item) }}"
                            onsubmit="return confirm('Bu talebi silmek istediğinize emin misiniz?');">
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
                        <dt class="text-slate-500">Ad Soyad</dt>
                        <dd class="text-slate-800 text-right">{{ trim(($item->name ?: '') . ' ' . ($item->surname ?: '')) ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Telefon</dt>
                        <dd class="text-slate-800 text-right">
                            @if ($item->phone_number)
                                <a href="tel:{{ preg_replace('/\s+/', '', $item->phone_number) }}" class="text-brand hover:underline">{{ $item->phone_number }}</a>
                            @else — @endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">E-posta</dt>
                        <dd class="text-slate-800 text-right break-all">{{ $item->email ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Şehir</dt>
                        <dd class="text-slate-800 text-right">{{ $item->city ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Aranma zamanı</dt>
                        <dd class="text-slate-800 text-right">{{ $item->preferred_time ?: 'Fark etmez' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 pb-2">
                        <dt class="text-slate-500">Durum</dt>
                        <dd class="text-slate-800 text-right">{{ $item->read_at ? 'Okundu — '.$item->read_at->format('d.m.Y H:i') : 'Okunmadı' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                    <h3 class="font-semibold text-slate-800">İlgilenilen araç</h3>
                    @if ($item->car)
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-14 rounded-lg overflow-hidden bg-slate-100 shrink-0">
                                @if ($url = $item->car->displayImageUrl())
                                    <img src="{{ $url }}" alt="{{ $item->car->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line"></i></div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $item->car->title }}</p>
                                <a href="{{ route('cars.show', $item->car->slug) }}" target="_blank"
                                    class="text-xs text-brand hover:underline inline-flex items-center gap-1">
                                    Detayı görüntüle <i class="ri-external-link-line"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-500">Araç bilgisi yok (genel form)</p>
                    @endif
                </div>

                <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                    <h3 class="font-semibold text-slate-800">Notu</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line leading-6">{{ $item->note ?: '—' }}</p>
                </div>
            </div>
        </div>

        @if (! empty($configDisplay))
            <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-semibold text-slate-800">Seçilen yapılandırma</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    @foreach ($configDisplay as $label => $value)
                        <div class="rounded-lg bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">{{ $label }}</p>
                            <p class="font-bold text-slate-800">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-2 text-xs text-slate-500">
            <p><span class="font-medium text-slate-600">IP:</span> {{ $item->ip_address ?: '—' }}</p>
            <p class="break-all"><span class="font-medium text-slate-600">User-Agent:</span> {{ $item->user_agent ?: '—' }}</p>
        </div>
    </div>
@endsection
