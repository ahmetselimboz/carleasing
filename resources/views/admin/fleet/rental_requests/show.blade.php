@extends('admin.layout')

@section('content')
    @php($req = $rentalRequest)
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('rental-requests.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Talepler
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Talep #{{ $req->id }}</h2>
                    <p class="text-slate-500 text-sm mt-1">{{ $req->created_at?->format('d.m.Y H:i') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @can('update', $req)
                        @if ($req->read_at === null)
                            <form method="POST" action="{{ route('rental-requests.update', $req) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="mark_read">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                                    Okundu işaretle
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('rental-requests.update', $req) }}">
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
                    @can('delete', $req)
                        <form method="POST" action="{{ route('rental-requests.destroy', $req) }}"
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
                        <dd class="text-slate-800 text-right">{{ trim(($req->name ?: '') . ' ' . ($req->surname ?: '')) ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">E-posta</dt>
                        <dd class="text-slate-800 text-right break-all">{{ $req->email ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Telefon</dt>
                        <dd class="text-slate-800 text-right">{{ $req->phone_number ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">İl / İlçe</dt>
                        <dd class="text-slate-800 text-right">{{ $req->city ?: '—' }} / {{ $req->district ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-3">
                <h3 class="font-semibold text-slate-800">Şirket bilgisi</h3>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Talep edilen araç sayısı</dt>
                        <dd class="text-slate-800">{{ $req->requested_car_count ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Şirketteki toplam araç sayısı</dt>
                        <dd class="text-slate-800">{{ $req->company_total_car_count ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">Vergi dairesi</dt>
                        <dd class="text-slate-800 text-right">{{ $req->tax_office ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-500">VKN / TCKN</dt>
                        <dd class="text-slate-800 text-right font-mono text-xs">{{ $req->tax_number_or_tckn ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="font-semibold text-slate-800">Talep edilen araçlar</h3>
                <p class="text-sm text-slate-500 mt-1">Talepteki araçlar sistemde kayıtlıysa detayları burada görünür.</p>
            </div>
            @forelse ($resolvedCars as $row)
                @php($c = $row['car'])
                @php($raw = $row['raw'])
                <div
                    class="flex flex-col sm:flex-row gap-4 p-4 rounded-xl border border-slate-100 bg-slate-300/10 hover:bg-slate-50/0 transition-soft">
                    <div class="shrink-0">
                        @if ($c && ($img = $c->displayImageUrl()))
                            <img src="{{ $img }}" alt="" width="120" height="90"
                                class="w-full sm:w-36 h-24 sm:h-28 object-cover rounded-xl border border-slate-200 bg-white"
                                loading="lazy" decoding="async">
                        @else
                            <div
                                class="w-full sm:w-36 h-24 sm:h-28 rounded-xl border border-dashed border-slate-200 flex items-center justify-center bg-white text-slate-300">
                                <i class="ri-car-line text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                @if ($c)
                                    <p class="font-semibold text-slate-800">{{ $c->title ?: $c->slug }}</p>
                                    <p class="text-sm text-slate-600">
                                        {{ trim(($c->brand ?: '') . ' ' . ($c->model ?: '')) ?: '—' }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-mono mt-1">{{ $c->slug }}</p>
                                @else
                                    <p class="font-semibold text-slate-800">Kayıt bulunamadı</p>
                                    <p class="text-sm text-slate-600">Araç kodu: <span
                                            class="font-mono">{{ $raw['slug'] ?? '—' }}</span></p>
                                    <p class="text-xs text-amber-700 mt-1">Bu kodla eşleşen araç bulunamadı; bilgiler başvurudan alınmıştır.</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                @if ($row['quantity'] !== null && $row['quantity'] !== '')
                                    <span
                                        class="inline-flex items-center rounded-lg bg-brand-solid/10 text-brand px-2.5 py-1 text-xs font-semibold">
                                        Adet: {{ $row['quantity'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if (!empty($raw['not']) || !empty($raw['note']))
                            <p class="text-sm text-slate-600 border-t border-slate-100 pt-2">
                                <span class="font-medium text-slate-700">Not:</span>
                                {{ $raw['not'] ?? $raw['note'] ?? '' }}
                            </p>
                        @endif
                        @if ($c)
                            @can('view', $c)
                                <a href="{{ route('cars.edit', $c) }}"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-brand hover:underline">
                                    <i class="ri-external-link-line"></i> Araç kaydını aç
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Bu talepte araç bilgisi yok.</p>
            @endforelse
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-2 text-xs text-slate-500">
            <p><span class="font-medium text-slate-600">IP:</span> {{ $req->ip_address ?: '—' }}</p>
            <p class="break-all"><span class="font-medium text-slate-600">User-Agent:</span> {{ $req->user_agent ?: '—' }}</p>
        </div>
    </div>
@endsection
