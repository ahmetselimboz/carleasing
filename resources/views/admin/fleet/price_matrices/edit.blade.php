@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('cars.edit', $car) }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> {{ $car->title ?: $car->slug }}
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Fiyatı düzenle</h2>
            <p class="text-slate-500 text-sm mt-1">Mevcut fiyat bilgisini güncelleyebilirsiniz.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('price-matrices.update', $price_matrix) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="car_package_id" class="block text-sm font-medium text-slate-700 mb-2">Paket</label>
                        <select name="car_package_id" id="car_package_id" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white @error('car_package_id') border-red-500 @enderror">
                            @foreach ($packages as $p)
                                <option value="{{ $p->id }}" @selected(old('car_package_id', $price_matrix->car_package_id) == $p->id)>{{ $p->name ?: 'Paket #'.$p->id }}</option>
                            @endforeach
                        </select>
                        @error('car_package_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="car_duration_id" class="block text-sm font-medium text-slate-700 mb-2">Süre (ay)</label>
                        <select name="car_duration_id" id="car_duration_id" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white @error('car_duration_id') border-red-500 @enderror">
                            @foreach ($durations as $d)
                                <option value="{{ $d->id }}" @selected(old('car_duration_id', $price_matrix->car_duration_id) == $d->id)>{{ $d->months }}</option>
                            @endforeach
                        </select>
                        @error('car_duration_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="car_kilometer_option_id" class="block text-sm font-medium text-slate-700 mb-2">Kilometre</label>
                        <select name="car_kilometer_option_id" id="car_kilometer_option_id" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white @error('car_kilometer_option_id') border-red-500 @enderror">
                            @foreach ($kilometerOptions as $k)
                                <option value="{{ $k->id }}" @selected(old('car_kilometer_option_id', $price_matrix->car_kilometer_option_id) == $k->id)>{{ $k->kilometer }}</option>
                            @endforeach
                        </select>
                        @error('car_kilometer_option_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="car_down_payment_id" class="block text-sm font-medium text-slate-700 mb-2">Peşinat</label>
                        <select name="car_down_payment_id" id="car_down_payment_id" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white @error('car_down_payment_id') border-red-500 @enderror">
                            @foreach ($downPayments as $dp)
                                <option value="{{ $dp->id }}" @selected(old('car_down_payment_id', $price_matrix->car_down_payment_id) == $dp->id)>{{ $dp->amount }}</option>
                            @endforeach
                        </select>
                        @error('car_down_payment_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="monthly_price" class="block text-sm font-medium text-slate-700 mb-2">Aylık fiyat</label>
                    <input type="text" name="monthly_price" id="monthly_price"
                        value="{{ old('monthly_price', $price_matrix->monthly_price) }}" required
                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl @error('monthly_price') border-red-500 @enderror">
                    @error('monthly_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                        class="rounded border-slate-300 text-brand focus:ring-brand" @checked(old('is_active', $price_matrix->is_active))>
                    <span class="text-sm text-slate-700">Aktif</span>
                </label>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('cars.edit', $car) }}"
                        class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">Vazgeç</a>
                    @can('update', $price_matrix)
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line"></i> Güncelle
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
@endsection
