@php
    $carModel = $car ?? null;
    $placeholder = \App\Models\Setting::cachedForViews()['placeholder_image_url'] ?? null;
@endphp
<div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 space-y-3"
    x-data="{
        previewUrl: null,
        initial: @js($carModel?->imageUrl()),
        fallback: @js($placeholder),
    }">
    <div>
        <span class="block text-sm font-medium text-slate-700">Araç görseli</span>
        <p class="text-xs text-slate-500 mt-0.5">JPEG, PNG, WebP veya GIF. En fazla 5 MB. Ön yüz listelerinde kullanılır.</p>
    </div>
    <div class="flex flex-wrap items-start gap-4">
        <div
            class="relative h-28 w-40 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white flex items-center justify-center">
            <img x-show="previewUrl || initial || fallback" x-cloak
                :src="previewUrl || initial || fallback" alt=""
                class="h-full w-full object-cover">
            <i class="ri-image-2-line text-3xl text-slate-300" x-show="!(previewUrl || initial || fallback)"></i>
        </div>
        <div class="flex-1 min-w-[200px] space-y-2">
            <input type="file" name="image" id="car_image_upload" accept="image/jpeg,image/png,image/webp,image/gif"
                class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-solid file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-brand-solid-hover"
                @change="previewUrl = $event.target.files?.[0] ? URL.createObjectURL($event.target.files[0]) : null">
            @error('image')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
            @if ($carModel?->image)
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                    <input type="checkbox" name="remove_image" value="1"
                        class="rounded border-slate-300 text-brand focus:ring-brand"
                        @change="if ($event.target.checked) { previewUrl = null; initial = null; }">
                    Mevcut görseli kaldır
                </label>
            @endif
        </div>
    </div>
</div>
