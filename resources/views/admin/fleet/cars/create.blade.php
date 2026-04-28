@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('cars.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Araç listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Yeni araç</h2>
            <p class="text-slate-500 text-sm mt-1">Kaydettikten sonra fiyatları ve araç özelliklerini ekleyebilirsiniz.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('cars.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Başlık</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-slate-700 mb-2">Kısa bağlantı <span
                                class="text-slate-400 font-normal">(isteğe bağlı)</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-slate-700 mb-2">Marka</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('brand') border-red-500 @enderror">
                        @error('brand')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="model" class="block text-sm font-medium text-slate-700 mb-2">Model</label>
                        <input type="text" name="model" id="model" value="{{ old('model') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('model') border-red-500 @enderror">
                        @error('model')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="fuel_type" class="block text-sm font-medium text-slate-700 mb-2">Yakıt</label>
                        <input type="text" name="fuel_type" id="fuel_type" value="{{ old('fuel_type') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                    </div>
                    <div>
                        <label for="transmission_type" class="block text-sm font-medium text-slate-700 mb-2">Şanzıman</label>
                        <input type="text" name="transmission_type" id="transmission_type"
                            value="{{ old('transmission_type') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                    </div>
                    <div>
                        <label for="body_type" class="block text-sm font-medium text-slate-700 mb-2">Kasa</label>
                        <input type="text" name="body_type" id="body_type" value="{{ old('body_type') }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                    <textarea name="description" id="description" rows="4"
                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @include('admin.fleet.cars.partials.car-image-field', ['car' => null])
                @include('admin.components.seo-fields', ['model' => null])
                @include('admin.fleet.cars.partials.magicbox-fields', ['mbRows' => $mbRows])
                @error('magicbox')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label for="home_sort_order" class="block text-sm font-medium text-slate-700 mb-2">Anasayfa sırası</label>
                        <input type="number" name="home_sort_order" id="home_sort_order" min="0" step="1"
                            value="{{ old('home_sort_order') }}"
                            placeholder="Örn. 1"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                    </div>
                    <div class="flex flex-wrap items-center gap-6 mt-6">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                @checked(old('is_active', true))>
                            <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                            <span class="text-sm text-slate-700">Aktif</span>
                        </label>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="status" value="1" class="sr-only peer"
                                @checked(old('status', false))>
                            <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                            <span class="text-sm text-slate-700">Sitede yayınla</span>
                        </label>
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="home_featured" value="1" class="sr-only peer"
                                @checked(old('home_featured', false))>
                            <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                            <span class="text-sm text-slate-700">Anasayfada öne çıkan araçlarda göster</span>
                        </label>
                    </div>
                </div>
         
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('cars.index') }}"
                        class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">Vazgeç</a>
                    @can('create', App\Models\Car::class)
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line"></i> Kaydet ve düzenlemeye geç
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
@endsection
