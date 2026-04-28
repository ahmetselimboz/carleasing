@php
    $m = $model->magicbox ?? [];
@endphp
<div class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm space-y-4">
    <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
        <i class="ri-search-eye-line text-brand"></i> Arama Motoru Optimizasyonu (SEO)
    </h3>
    <p class="text-xs text-slate-500">Bu kısımdan kaydınızın Google gibi arama motorlarındaki görünümünü ve başlığını özelleştirebilirsiniz. Boş bırakırsanız sistem otomatik oluşturacaktır.</p>

    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">SEO Meta Başlığı (Meta Title)</label>
            <input type="text" name="magicbox[seo][meta_title]" 
                class="input-base w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand/20 text-sm"
                value="{{ old('magicbox.seo.meta_title', data_get($m, 'seo.meta_title')) }}"
                placeholder="Özel başlık girin... (önerilen 50-60 karakter)">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">SEO Meta Açıklaması (Meta Description)</label>
            <textarea name="magicbox[seo][meta_description]" rows="3"
                class="input-base w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand/20 text-sm resize-none"
                placeholder="Arama sonuçlarında başlığın altında çıkan kısa açıklama metni... (önerilen 150-160 karakter)">{{ old('magicbox.seo.meta_description', data_get($m, 'seo.meta_description')) }}</textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Anahtar Kelimeler (Meta Keywords)</label>
            <input type="text" name="magicbox[seo][meta_keywords]" 
                class="input-base w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand/20 text-sm"
                value="{{ old('magicbox.seo.meta_keywords', data_get($m, 'seo.meta_keywords')) }}"
                placeholder="Virgülle ayırarak girin (Örn: araç kiralama, araba, vb.)">
        </div>

        <div class="flex items-center justify-between gap-4 p-3 bg-slate-50 border border-slate-100 rounded-lg">
            <div>
                <p class="text-sm font-medium text-slate-800">Arama motorlarında listelensin mi?</p>
                <p class="text-xs text-slate-500">Hayır seçerseniz "noindex, nofollow" etiketi basılır.</p>
            </div>
            <label class="inline-flex cursor-pointer items-center">
                @php $allowIndex = old('magicbox.seo.allow_indexing', data_get($m, 'seo.allow_indexing', true)); @endphp
                <input type="hidden" name="magicbox[seo][allow_indexing]" value="0">
                <input type="checkbox" name="magicbox[seo][allow_indexing]" value="1"
                    class="peer sr-only" @checked($allowIndex === true || $allowIndex === 1 || $allowIndex === '1')>
                <span class="relative inline-flex h-6 w-12 shrink-0 rounded-full bg-slate-200 transition-colors peer-checked:bg-brand-solid after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:content-[''] peer-checked:after:translate-x-6"></span>
            </label>
        </div>
    </div>
</div>
