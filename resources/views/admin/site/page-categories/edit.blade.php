@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('page-categories.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Kategori listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Sayfa kategorisi düzenle</h2>
            <p class="text-slate-500 text-sm mt-1">Kategori bilgilerini güncelleyebilirsiniz.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('page-categories.update', $category) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Kategori adı</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

           
                <div class="flex flex-wrap items-center gap-6">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            @checked(old('is_active', $category->is_active))>
                        <span
                            class="relative h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand/40 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-5"></span>
                        <span class="text-sm text-slate-700">Aktif</span>
                    </label>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('page-categories.index') }}"
                        class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">
                        Vazgeç
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                        <i class="ri-save-3-line"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
