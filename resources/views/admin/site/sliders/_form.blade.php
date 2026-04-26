@php
    $slider = $slider ?? new \App\Models\Slider();
@endphp
<div>
    <label for="is_active" class="block text-sm font-medium text-slate-700 mb-2">Durum</label>
    <label class="inline-flex items-center gap-3 cursor-pointer">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $slider->is_active ?? true))>
        <span class="relative h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid before:absolute before:left-1 before:top-1 before:h-5 before:w-5 before:rounded-full before:bg-white before:transition-transform peer-checked:before:translate-x-5"></span>
        <span class="text-sm text-slate-700">Aktif</span>
    </label>
</div>
<div>
        <label for="badge" class="block text-sm font-medium text-slate-700 mb-2">Üst etiket</label>
    <input type="text" name="badge" id="badge" value="{{ old('badge', $slider->badge) }}"
        placeholder="Örn. Dev Filo"
        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
</div>
<div class="grid grid-cols-1 gap-4">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Ana başlık (ilk satır)</label>
        <input type="text" name="title" id="title" value="{{ old('title', $slider->title) }}"
            placeholder="Örn. Ticari Araçlarla"
            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
    </div>
    <div>
        <label for="title_highlight" class="block text-sm font-medium text-slate-700 mb-2">Vurgulu başlık (renkli satır)</label>
        <input type="text" name="title_highlight" id="title_highlight" value="{{ old('title_highlight', $slider->title_highlight) }}"
            placeholder="Örn. Yükünüzü Hafifletiyoruz"
            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
    </div>
</div>
<div>
    <label for="subtitle" class="block text-sm font-medium text-slate-700 mb-2">Alt metin</label>
    <textarea name="subtitle" id="subtitle" rows="3"
        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">{{ old('subtitle', $slider->subtitle) }}</textarea>
</div>
<div>
    <label for="link" class="block text-sm font-medium text-slate-700 mb-2">İsteğe bağlı bağlantı</label>
    <input type="url" name="link" id="link" value="{{ old('link', $slider->link) }}"
        placeholder="https://"
        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Arka plan görseli</label>
        @if (!empty($slider->image_1))
            <p class="text-xs text-slate-500 mb-2">Mevcut: {{ $slider->image_1 }}</p>
            <img src="{{ $slider->image1Url() }}" alt="" class="h-24 w-full max-w-xs object-cover rounded-lg border border-slate-100 mb-2">
            @if(isset($edit) && $edit)
                <label class="inline-flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_image_1" value="1" class="rounded border-slate-300"> Görseli kaldır
                </label>
            @endif
        @endif
        <input type="file" name="image_1" accept="image/*"
            class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Öndeki araç görseli</label>
        @if (!empty($slider->image_2))
            <p class="text-xs text-slate-500 mb-2">Mevcut: {{ $slider->image_2 }}</p>
            <img src="{{ $slider->image2Url() }}" alt="" class="h-24 w-full max-w-xs object-cover rounded-lg border border-slate-100 mb-2">
            @if(isset($edit) && $edit)
                <label class="inline-flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_image_2" value="1" class="rounded border-slate-300"> Görseli kaldır
                </label>
            @endif
        @endif
        <input type="file" name="image_2" accept="image/*"
            class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
    </div>
</div>
