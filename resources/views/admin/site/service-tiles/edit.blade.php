@extends('admin.layout')

@section('content')
    @php
        $iconOptions = config('admin-icons.service_tile_icons', []);
    @endphp
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('home-service-tiles.index') }}" class="text-sm text-slate-500 hover:text-brand inline-flex gap-1 mb-2"><i class="ri-arrow-left-line"></i> Liste</a>
            <h2 class="text-2xl font-bold text-slate-800">Hizmet kutusu düzenle</h2>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('home-service-tiles.update', $tile) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div>
                    <label for="is_active" class="block text-sm font-medium text-slate-700 mb-2">Durum</label>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $tile->is_active))>
                        <span class="relative h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid before:absolute before:left-1 before:top-1 before:h-5 before:w-5 before:rounded-full before:bg-white before:transition-transform peer-checked:before:translate-x-5"></span>
                        <span class="text-sm text-slate-700">Aktif</span>
                    </label>
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">İkon</label>
                    <input type="hidden" name="icon" id="icon" value="{{ old('icon', $tile->icon ?: 'ri-flight-takeoff-line') }}">
                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span id="icon-preview" class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-white border border-slate-200 text-2xl text-slate-700"><i class="{{ old('icon', $tile->icon ?: 'ri-flight-takeoff-line') }}"></i></span>
                        <div class="flex-1 min-w-[220px]">
                            <p class="text-sm font-medium text-slate-700">Seçilen ikon</p>
                            <p id="icon-label" class="text-xs text-slate-500 font-mono">{{ old('icon', $tile->icon ?: 'ri-flight-takeoff-line') }}</p>
                        </div>
                        <button type="button" id="open-icon-modal" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100">
                            <i class="ri-apps-2-line"></i> İkon seç
                        </button>
                    </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Başlık</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $tile->title) }}" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                    <textarea name="description" id="description" rows="3" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">{{ old('description', $tile->description) }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div>
                    <label for="link_url" class="block text-sm font-medium text-slate-700 mb-2">Bağlantı</label>
                    <input type="url" name="link_url" id="link_url" value="{{ old('link_url', $tile->link_url) }}" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    </div>
                @if ($tile->imageUrl())
                    <div id="current-image-wrap">
                        <p class="text-sm text-slate-600 mb-2">Mevcut görsel</p>
                        <img src="{{ $tile->imageUrl() }}" alt="" class="h-32 rounded-lg border border-slate-100 object-cover">
                        <form method="POST" action="{{ route('home-service-tiles.update', $tile) }}" class="mt-3"
                            onsubmit="return confirm('Görseli silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="remove_image" value="1">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                                <i class="ri-delete-bin-line"></i> Görseli kaldır
                            </button>
                        </form>
                    </div>
                @else
                    <div></div>
                @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Yeni görsel</label>
                    <label for="image" id="image-dropzone" class="block rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center cursor-pointer hover:border-brand hover:bg-brand/5 transition-soft">
                        <i class="ri-image-add-line text-3xl text-slate-400"></i>
                        <p class="mt-2 text-sm font-medium text-slate-700">Görsel yüklemek için tıklayın veya sürükleyip bırakın</p>
                        <p class="text-xs text-slate-500 mt-1">PNG, JPG, WEBP, AVIF, GIF - Maks. 8MB</p>
                        <p id="image-file-name" class="text-xs text-brand mt-3 hidden"></p>
                    </label>
                    <input type="file" name="image" id="image" accept="image/*" class="hidden">
                </div>
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white"><i class="ri-save-3-line"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>

    <div id="icon-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative mx-auto mt-10 w-[95%] max-w-5xl rounded-2xl bg-white shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 p-4">
                <h3 class="text-lg font-semibold text-slate-800">İkon seçici</h3>
                <button type="button" id="close-icon-modal" class="h-9 w-9 rounded-lg hover:bg-slate-100 text-slate-500"><i class="ri-close-line text-xl"></i></button>
            </div>
            <div class="p-4 space-y-4">
                <input type="text" id="icon-search" placeholder="İkon ara (ör: car, shield, money)" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                <div id="icon-grid" class="max-h-[60vh] overflow-y-auto grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                    @foreach ($iconOptions as $icon)
                        <button type="button" class="icon-option rounded-xl border border-slate-200 bg-white p-3 hover:border-brand hover:bg-brand/5 transition-soft" data-icon="{{ $icon }}" data-keywords="{{ str_replace(['ri-', '-line'], '', $icon) }}">
                            <i class="{{ $icon }} text-xl text-slate-700"></i>
                            <span class="mt-2 block text-[10px] text-slate-500 font-mono truncate">{{ $icon }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var fileInput = document.getElementById('image');
            var fileName = document.getElementById('image-file-name');
            var dropzone = document.getElementById('image-dropzone');
            var iconInput = document.getElementById('icon');
            var iconPreview = document.getElementById('icon-preview');
            var iconLabel = document.getElementById('icon-label');
            var iconModal = document.getElementById('icon-modal');

            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('border-brand');
            });
            dropzone.addEventListener('dragleave', function() {
                dropzone.classList.remove('border-brand');
            });
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-brand');
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            });
            fileInput.addEventListener('change', function() {
                if (fileInput.files && fileInput.files[0]) {
                    fileName.textContent = 'Seçilen dosya: ' + fileInput.files[0].name;
                    fileName.classList.remove('hidden');
                }
            });

            document.getElementById('open-icon-modal').addEventListener('click', function() {
                iconModal.classList.remove('hidden');
            });
            document.getElementById('close-icon-modal').addEventListener('click', function() {
                iconModal.classList.add('hidden');
            });
            iconModal.addEventListener('click', function(e) {
                if (e.target === iconModal || e.target.classList.contains('bg-black/50')) {
                    iconModal.classList.add('hidden');
                }
            });

            document.getElementById('icon-grid').addEventListener('click', function(e) {
                var btn = e.target.closest('.icon-option');
                if (!btn) return;
                var icon = btn.dataset.icon;
                iconInput.value = icon;
                iconPreview.innerHTML = '<i class="' + icon + '"></i>';
                iconLabel.textContent = icon;
                iconModal.classList.add('hidden');
            });

            document.getElementById('icon-search').addEventListener('input', function(e) {
                var q = e.target.value.toLowerCase().trim();
                var options = document.querySelectorAll('.icon-option');
                options.forEach(function(option) {
                    var text = (option.dataset.icon + ' ' + option.dataset.keywords).toLowerCase();
                    option.classList.toggle('hidden', q && text.indexOf(q) === -1);
                });
            });
        })();
    </script>
@endpush
