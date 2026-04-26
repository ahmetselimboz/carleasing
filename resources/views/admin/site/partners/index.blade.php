@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">İş ortakları</h2>
                <p class="text-slate-500 text-sm mt-1">Anasayfadaki kayan marka isimlerini buradan düzenleyebilirsiniz.</p>
            </div>
            @can('create', App\Models\HomePartner::class)
                <a href="{{ route('home-partners.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-handshake-line text-lg"></i>
                    Yeni iş ortağı
                </a>
            @endcan
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form method="GET" class="px-4 pt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="İsim ara..."
                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                <select name="status" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="">Tüm durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="passive" @selected(request('status') === 'passive')>Pasif</option>
                </select>
                <select name="direction" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="asc" @selected(request('direction', 'asc') === 'asc')>Sıra: Artan</option>
                    <option value="desc" @selected(request('direction') === 'desc')>Sıra: Azalan</option>
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
                                <th class="px-4 py-3 font-medium">İsim</th>
                                <th class="px-4 py-3 font-medium">Durum</th>
                                <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="partners-table-body" class="divide-y divide-slate-100">
                            @forelse ($partners as $p)
                                <tr class="partner-row hover:bg-slate-50/80" draggable="true" data-id="{{ $p->id }}">
                                    <td class="px-4 py-3 text-slate-400 cursor-move">
                                        <span class="inline-flex items-center gap-1"><i class="ri-draggable"></i> Taşı</span>
                                    </td>
                                    <td class="px-4 py-3"><span class="partner-order">{{ $loop->iteration }}</span></td>
                                    <td class="px-4 py-3 font-semibold tracking-tight">{{ $p->name }}</td>
                                    <td class="px-4 py-3">
                                        @if ($p->is_active)
                                            <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                                        @else
                                            <span class="text-slate-400 text-xs font-medium">Pasif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('home-partners.edit', $p) }}" class="text-xs font-medium text-brand">Düzenle</a>
                                        <form method="POST" action="{{ route('home-partners.destroy', $p) }}" class="inline ml-2" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Kayıt yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <form id="partners-reorder-form" method="POST" action="{{ route('home-partners.reorder') }}" class="hidden">
                @csrf
                <div id="partners-reorder-inputs"></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var body = document.getElementById('partners-table-body');
            if (!body) return;
            var form = document.getElementById('partners-reorder-form');
            var inputs = document.getElementById('partners-reorder-inputs');
            var dragged = null;
            var moved = false;

            function refreshNumbers() {
                var rows = body.querySelectorAll('.partner-row');
                rows.forEach(function(row, index) {
                    var slot = row.querySelector('.partner-order');
                    if (slot) slot.textContent = String(index + 1);
                });
            }

            body.addEventListener('dragstart', function(e) {
                var row = e.target.closest('.partner-row');
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
                    var rows = body.querySelectorAll('.partner-row');
                    inputs.innerHTML = '';
                    rows.forEach(function(row) {
                        var id = row.dataset.id;
                        if (!id) return;
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'partner_ids[]';
                        input.value = id;
                        inputs.appendChild(input);
                    });
                    form.submit();
                }
            });

            body.addEventListener('dragover', function(e) {
                e.preventDefault();
                var row = e.target.closest('.partner-row');
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
