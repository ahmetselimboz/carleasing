@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Anasayfa slaytları</h2>
                <p class="text-slate-500 text-sm mt-1">Anasayfada görünen büyük görsel alanını buradan yönetebilirsiniz.</p>
            </div>
            @can('create', App\Models\Slider::class)
                <a href="{{ route('sliders.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-image-add-line text-lg"></i>
                    Yeni slayt
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form method="GET" class="px-4 pt-4 grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Başlık/etiket ara..."
                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                <select name="status" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="">Tüm durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="passive" @selected(request('status') === 'passive')>Pasif</option>
                </select>
                <select name="sort" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="sort_order" @selected(request('sort', 'sort_order') === 'sort_order')>Sıra</option>
                    <option value="title" @selected(request('sort') === 'title')>Başlık</option>
                </select>
                <select name="direction" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="asc" @selected(request('direction', 'asc') === 'asc')>Artan</option>
                    <option value="desc" @selected(request('direction') === 'desc')>Azalan</option>
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                    <i class="ri-filter-3-line"></i> Filtrele
                </button>
            </form>
            <div class="space-y-4">
                <div class="px-4 pt-4 flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">Sürükle-bırak tamamlanınca sıralama otomatik kaydedilir.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium text-xs w-28">Sürükle-Bırak</th>
                                <th class="px-4 py-3 font-medium w-16">Sıra</th>
                                <th class="px-4 py-3 font-medium">Önizleme</th>
                                <th class="px-4 py-3 font-medium">Başlık</th>
                                <th class="px-4 py-3 font-medium">Durum</th>
                                <th class="px-4 py-3 font-medium text-right w-44">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="sliders-table-body" class="divide-y divide-slate-100">
                            @forelse ($sliders as $s)
                                <tr class="slider-row hover:bg-slate-50/80" draggable="true" data-id="{{ $s->id }}">
                                    <td class="px-4 py-3 text-slate-400 cursor-move">
                                        <span class="inline-flex items-center gap-1"><i class="ri-draggable"></i> Taşı</span>
                                    </td>
                                    <td class="px-4 py-3"><span class="slider-order">{{ $loop->iteration }}</span></td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            @if ($u = $s->image1Url())
                                                <img src="{{ $u }}" alt="" class="h-14 w-24 object-cover rounded-lg border border-slate-100" loading="lazy">
                                            @endif
                                            @if ($u2 = $s->image2Url())
                                                <img src="{{ $u2 }}" alt="" class="h-14 w-24 object-cover rounded-lg border border-slate-100 bg-slate-50" loading="lazy">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-slate-800">{{ $s->badge ?: '—' }}</span>
                                        <p class="text-slate-500 text-xs mt-0.5 line-clamp-2">{{ $s->title }}<span class="text-primary">{{ $s->title_highlight }}</span></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($s->is_active)
                                            <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                                        @else
                                            <span class="text-slate-400 text-xs font-medium">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1 flex-wrap">
                                            @can('view', $s)
                                                <a href="{{ route('sliders.edit', $s) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                                    <i class="ri-pencil-line"></i> Düzenle
                                                </a>
                                            @endcan
                                            @can('delete', $s)
                                                <form method="POST" action="{{ route('sliders.destroy', $s) }}" class="inline"
                                                    onsubmit="return confirm('Bu slaytı silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-soft">
                                                        <i class="ri-delete-bin-line"></i> Sil
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-500">Henüz slayt yok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <form id="sliders-reorder-form" method="POST" action="{{ route('sliders.reorder') }}" class="hidden">
                @csrf
                <div id="sliders-reorder-inputs"></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var body = document.getElementById('sliders-table-body');
            if (!body) return;
            var form = document.getElementById('sliders-reorder-form');
            var inputs = document.getElementById('sliders-reorder-inputs');
            var dragged = null;
            var moved = false;

            function refreshNumbers() {
                var rows = body.querySelectorAll('.slider-row');
                rows.forEach(function(row, index) {
                    var slot = row.querySelector('.slider-order');
                    if (slot) slot.textContent = String(index + 1);
                });
            }

            body.addEventListener('dragstart', function(e) {
                var row = e.target.closest('.slider-row');
                if (!row) return;
                dragged = row;
                moved = false;
                row.classList.add('opacity-60');
            });

            body.addEventListener('dragend', function() {
                if (dragged) dragged.classList.remove('opacity-60');
                dragged = null;
                refreshNumbers();
                if (moved && form && inputs) {
                    var rows = body.querySelectorAll('.slider-row');
                    inputs.innerHTML = '';
                    rows.forEach(function(row) {
                        var id = row.dataset.id;
                        if (!id) return;
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'slider_ids[]';
                        input.value = id;
                        inputs.appendChild(input);
                    });
                    form.submit();
                }
            });

            body.addEventListener('dragover', function(e) {
                e.preventDefault();
                var row = e.target.closest('.slider-row');
                if (!row || row === dragged) return;
                var rect = row.getBoundingClientRect();
                var isAfter = (e.clientY - rect.top) > rect.height / 2;
                if (isAfter && row.nextElementSibling !== dragged) {
                    row.after(dragged);
                    moved = true;
                } else if (!isAfter && row.previousElementSibling !== dragged) {
                    row.before(dragged);
                    moved = true;
                }
            });
        })();
    </script>
@endpush
