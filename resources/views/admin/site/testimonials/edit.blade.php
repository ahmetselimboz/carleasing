@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-8">
        <div>
            <a href="{{ route('home-testimonials.index') }}" class="text-sm text-slate-500 hover:text-brand inline-flex gap-1 mb-2"><i class="ri-arrow-left-line"></i> Liste</a>
            <h2 class="text-2xl font-bold text-slate-800">Yorum düzenle</h2>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('home-testimonials.update', $testimonial) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
               
                    <div>
                        <label for="rating" class="block text-sm font-medium text-slate-700 mb-2">Puan</label>
                        <input type="number" name="rating" id="rating" min="1" max="5" value="{{ old('rating', $testimonial->rating) }}" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                            <input id="is_active" type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $testimonial->is_active))>
                            <span class="relative h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-brand-solid before:absolute before:left-1 before:top-1 before:h-5 before:w-5 before:rounded-full before:bg-white before:transition-transform peer-checked:before:translate-x-5"></span>
                            <span class="text-sm text-slate-700">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">İsim</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $testimonial->name) }}" required class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Ünvan</label>
                        <input type="text" name="role" id="role" value="{{ old('role', $testimonial->role) }}" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div>
                    <label for="quote" class="block text-sm font-medium text-slate-700 mb-2">Alıntı</label>
                    <textarea name="quote" id="quote" rows="4" required class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">{{ old('quote', $testimonial->quote) }}</textarea>
                </div>
                @if ($testimonial->avatarUrl())
                    <div>
                        <p class="text-sm mb-2">Mevcut avatar</p>
                        <img src="{{ $testimonial->avatarUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover border border-slate-100">
                        <label class="inline-flex items-center gap-2 mt-2 text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300"> Kaldır
                        </label>
                    </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Yeni avatar</label>
                    <input type="file" name="avatar" accept="image/*" class="block w-full text-sm">
                </div>
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white"><i class="ri-save-3-line"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
