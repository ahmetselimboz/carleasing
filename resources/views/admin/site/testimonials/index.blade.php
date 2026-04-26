@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Müşteri yorumları</h2>
                <p class="text-slate-500 text-sm mt-1">Anasayfada görünen müşteri yorumları alanı.</p>
            </div>
            @can('create', App\Models\HomeTestimonial::class)
                <a href="{{ route('home-testimonials.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-chat-quote-line text-lg"></i>
                    Yeni yorum
                </a>
            @endcan
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
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
                            <th class="px-4 py-3 font-medium">Puan</th>
                            <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="testimonials-table-body" class="divide-y divide-slate-100">
                        @forelse ($testimonials as $t)
                            <tr class="testimonial-row hover:bg-slate-50/80" draggable="true" data-id="{{ $t->id }}">
                                <td class="px-4 py-3 text-slate-400 cursor-move">
                                    <span class="inline-flex items-center gap-1"><i class="ri-draggable"></i> Taşı</span>
                                </td>
                                <td class="px-4 py-3"><span class="testimonial-order">{{ $loop->iteration }}</span></td>
                                <td class="px-4 py-3 font-medium">{{ $t->name }}</td>
                                <td class="px-4 py-3">{{ $t->rating }}/5</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('home-testimonials.edit', $t) }}" class="text-xs font-medium text-brand">Düzenle</a>
                                    <form method="POST" action="{{ route('home-testimonials.destroy', $t) }}" class="inline ml-2" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
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
            <form id="testimonials-reorder-form" method="POST" action="{{ route('home-testimonials.reorder') }}" class="hidden">
                @csrf
                <div id="testimonials-reorder-inputs"></div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var body = document.getElementById('testimonials-table-body');
            if (!body) return;
            var form = document.getElementById('testimonials-reorder-form');
            var inputs = document.getElementById('testimonials-reorder-inputs');
            var dragged = null;
            var moved = false;

            function refreshNumbers() {
                var rows = body.querySelectorAll('.testimonial-row');
                rows.forEach(function(row, index) {
                    var slot = row.querySelector('.testimonial-order');
                    if (slot) slot.textContent = String(index + 1);
                });
            }

            body.addEventListener('dragstart', function(e) {
                var row = e.target.closest('.testimonial-row');
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
                    var rows = body.querySelectorAll('.testimonial-row');
                    inputs.innerHTML = '';
                    rows.forEach(function(row) {
                        var id = row.dataset.id;
                        if (!id) return;
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'testimonial_ids[]';
                        input.value = id;
                        inputs.appendChild(input);
                    });
                    form.submit();
                }
            });

            body.addEventListener('dragover', function(e) {
                e.preventDefault();
                var row = e.target.closest('.testimonial-row');
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

            refreshNumbers();
        })();
    </script>
@endpush
