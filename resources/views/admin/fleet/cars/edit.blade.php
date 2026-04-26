@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('cars.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Araç listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Araç düzenle</h2>
            <p class="text-slate-500 text-sm mt-1">Bu aracın bilgilerini ve fiyatlarını buradan güncelleyebilirsiniz.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            @can('update', $car)
                <form method="POST" action="{{ route('cars.update', $car) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Başlık</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $car->title) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="slug" class="block text-sm font-medium text-slate-700 mb-2">Kısa bağlantı <span
                                    class="text-slate-400 font-normal">(isteğe bağlı)</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $car->slug) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-slate-700 mb-2">Marka</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand', $car->brand) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        </div>
                        <div>
                            <label for="model" class="block text-sm font-medium text-slate-700 mb-2">Model</label>
                            <input type="text" name="model" id="model" value="{{ old('model', $car->model) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="fuel_type" class="block text-sm font-medium text-slate-700 mb-2">Yakıt</label>
                            <input type="text" name="fuel_type" id="fuel_type"
                                value="{{ old('fuel_type', $car->fuel_type) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        </div>
                        <div>
                            <label for="transmission_type" class="block text-sm font-medium text-slate-700 mb-2">Şanzıman</label>
                            <input type="text" name="transmission_type" id="transmission_type"
                                value="{{ old('transmission_type', $car->transmission_type) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        </div>
                        <div>
                            <label for="body_type" class="block text-sm font-medium text-slate-700 mb-2">Kasa</label>
                            <input type="text" name="body_type" id="body_type"
                                value="{{ old('body_type', $car->body_type) }}"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                        <textarea name="description" id="description" rows="4"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">{{ old('description', $car->description) }}</textarea>
                    </div>
                    @include('admin.fleet.cars.partials.car-image-field', ['car' => $car])
                    @include('admin.fleet.cars.partials.magicbox-fields', ['mbRows' => $mbRows])
                    @error('magicbox')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                    <div class="grid grid-cols-1 sm:grid-cols-2 items-center gap-4">
                        <div>
                            <label for="home_sort_order" class="block text-sm font-medium text-slate-700 mb-2">Anasayfa sırası</label>
                            <input type="number" name="home_sort_order" id="home_sort_order" min="0" step="1"
                                value="{{ old('home_sort_order', $car->home_sort_order) }}"
                                placeholder="Örn. 1"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                            <p class="text-xs text-slate-500 mt-1">Anasayfada gösterilen araçların sırasını belirler.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-6 ">
                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                    @checked(old('is_active', $car->is_active))>
                                <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                                <span class="text-sm text-slate-700">Aktif</span>
                            </label>
                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="status" value="1" class="sr-only peer"
                                    @checked(old('status', $car->status))>
                                <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                                <span class="text-sm text-slate-700">Sitede yayınla</span>
                            </label>
                            <label class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="home_featured" value="1" class="sr-only peer"
                                    @checked(old('home_featured', $car->home_featured))>
                                <span class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                                <span class="text-sm text-slate-700">Anasayfada öne çıkan araçlarda göster</span>
                            </label>
                        </div>
                    </div>
                 
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line"></i> Araç bilgisini kaydet
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-slate-500">Bu araç kaydını yalnızca görüntüleyebilirsiniz.</p>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8 space-y-4 ">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Fiyat tablosu</h3>
                    <p class="text-sm text-slate-500">Farklı seçenekler için aylık fiyatları buradan yönetebilirsiniz.</p>
                </div>
                @can('create', App\Models\CarPriceMatrix::class)
                    <a href="{{ route('cars.price-matrices.create', $car) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                        <i class="ri-add-line"></i> Yeni fiyat ekle
                    </a>
                @endcan
            </div>
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 font-medium">Paket</th>
                            <th class="px-3 py-2 font-medium">Süre</th>
                            <th class="px-3 py-2 font-medium">Km</th>
                            <th class="px-3 py-2 font-medium">Peşinat</th>
                            <th class="px-3 py-2 font-medium">Aylık</th>
                            <th class="px-3 py-2 font-medium text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($car->priceMatrices as $row)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2">{{ $row->package?->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $row->duration?->months ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $row->kilometerOption?->kilometer ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $row->downPayment?->amount ?? '—' }}</td>
                                <td class="px-3 py-2 font-medium text-slate-800">{{ $row->monthly_price }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    @can('update', $row)
                                        <a href="{{ route('price-matrices.edit', $row) }}"
                                            class="text-xs font-medium text-brand hover:underline">Düzenle</a>
                                    @endcan
                                    @can('delete', $row)
                                        <form method="POST" action="{{ route('price-matrices.destroy', $row) }}"
                                            class="inline ml-2"
                                            onsubmit="return confirm('Bu fiyat satırını silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline cursor-pointer">Sil</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-slate-500 text-sm">Henüz fiyat bilgisi yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8 space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Araç özellikleri</h3>
                <p class="text-sm text-slate-500">Araca ait özellikleri buradan ekleyip kaldırabilirsiniz.</p>
            </div>

            @can('update', $car)
                <form method="POST" action="{{ route('cars.attribute-pivots.store', $car) }}"
                    class="grid grid-cols-1 lg:grid-cols-4 gap-3 items-end rounded-xl bg-slate-50 p-4 border border-slate-100">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Kategori</label>
                        <select name="attribute_category_id" required
                            class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white">
                            <option value="">Seçin</option>
                            @foreach ($attributeCategories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('attribute_category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Özellik</label>
                        <select name="attribute_id" required
                            class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white">
                            <option value="">Seçin</option>
                            @foreach ($attributes as $attr)
                                <option value="{{ $attr->id }}" @selected(old('attribute_id') == $attr->id)>{{ $attr->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Değer</label>
                        <select name="attribute_value_id" required
                            class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white">
                            <option value="">Seçin</option>
                            @foreach ($attributeValues as $val)
                                <option value="{{ $val->id }}" @selected(old('attribute_value_id') == $val->id)>{{ $val->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full rounded-lg bg-brand-solid py-2 text-sm font-semibold text-white hover:bg-brand-solid-hover transition-soft">
                            Özellik ekle
                        </button>
                    </div>
                    @error('pivot')
                        <p class="lg:col-span-4 text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </form>
            @endcan

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 font-medium">Kategori</th>
                            <th class="px-3 py-2 font-medium">Özellik</th>
                            <th class="px-3 py-2 font-medium">Değer</th>
                            <th class="px-3 py-2 font-medium text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($car->attributePivots as $pivot)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2">{{ $pivot->category?->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $pivot->attribute?->title ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $pivot->value?->title ?? '—' }}</td>
                                <td class="px-3 py-2 text-right">
                                    @can('delete', $pivot)
                                        <form method="POST"
                                            action="{{ route('cars.attribute-pivots.destroy', ['car' => $car, 'car_attribute_pivot' => $pivot]) }}"
                                            class="inline"
                                            onsubmit="return confirm('Bu satırı kaldırmak istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline cursor-pointer">Sil</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-slate-500 text-sm">Henüz özellik eklenmedi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('delete', $car)
            <div
                class="card bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-sm text-slate-600">Aracı siler <span class="text-slate-400 text-xs">(Çöp kutusuna taşır)</span></p>
                <form method="POST" action="{{ route('cars.destroy', $car) }}"
                    onsubmit="return confirm('Bu aracı silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-soft w-full sm:w-auto">
                        <i class="ri-delete-bin-line"></i> Aracı sil
                    </button>
                </form>
            </div>
        @endcan
    </div>
@endsection
