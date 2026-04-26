@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Araçlar</h2>
                <p class="text-slate-500 text-sm mt-1">Araçları, fiyatlarını ve özelliklerini buradan yönetebilirsiniz.</p>
            </div>
            @can('create', App\Models\Car::class)
                <a href="{{ route('cars.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-car-line text-lg"></i>
                    Yeni araç
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium w-24">Görsel</th>
                            <th class="px-4 py-3 font-medium">Başlık</th>
                            <th class="px-4 py-3 font-medium">Marka / Model</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                            <th class="px-4 py-3 font-medium">Oluşturulma tarihi</th>
                            <th class="px-4 py-3 font-medium text-right w-44">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cars as $c)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 align-middle">
                                    @if ($url = $c->displayImageUrl())
                                        <img src="{{ $url }}" alt="" width="64" height="48"
                                            class="h-12 w-16 object-cover rounded-lg border border-slate-100 bg-slate-50"
                                            loading="lazy" decoding="async">
                                    @else
                                        <div
                                            class="h-12 w-16 rounded-lg border border-dashed border-slate-200 flex items-center justify-center bg-slate-50 text-slate-300">
                                            <i class="ri-car-line text-lg"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $c->title ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ trim(($c->brand ?: '') . ' ' . ($c->model ?: '')) ?: '—' }}
                                </td>
                           
                                <td class="px-4 py-3">
                                    @if ($c->is_active)
                                        <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                                    @else
                                        <span class="text-slate-400 text-xs font-medium">Pasif</span>
                                    @endif
                                    @if ($c->status)
                                        <span class="ml-2 text-brand text-xs font-medium">Yayında</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs hidden md:table-cell whitespace-nowrap">
                                    {{ $c->created_at?->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1 flex-wrap">
                                        @can('view', $c)
                                            <a href="{{ route('cars.edit', $c) }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                                <i class="ri-pencil-line"></i> Düzenle
                                            </a>
                                        @endcan
                                        @can('delete', $c)
                                            <form method="POST" action="{{ route('cars.destroy', $c) }}" class="inline"
                                                onsubmit="return confirm('Bu aracı silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-500/10 cursor-pointer transition-soft">
                                                    <i class="ri-delete-bin-line"></i> Sil
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    Henüz araç eklenmedi. Sağ üstten yeni araç ekleyebilirsiniz.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cars->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $cars->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
