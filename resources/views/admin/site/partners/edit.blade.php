@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('home-partners.index') }}" class="text-sm text-slate-500 hover:text-brand inline-flex gap-1 mb-2"><i class="ri-arrow-left-line"></i> Liste</a>
            <h2 class="text-2xl font-bold text-slate-800">İş ortağını düzenle</h2>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('home-partners.update', $partner) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="is_active" class="block text-sm font-medium text-slate-700 mb-2">Durum</label>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $partner->is_active))>
                        <span class="relative h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid before:absolute before:left-1 before:top-1 before:h-5 before:w-5 before:rounded-full before:bg-white before:transition-transform peer-checked:before:translate-x-5"></span>
                        <span class="text-sm text-slate-700">Aktif</span>
                    </label>
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Görünen isim</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $partner->name) }}" required class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                </div>
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white"><i class="ri-save-3-line"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
