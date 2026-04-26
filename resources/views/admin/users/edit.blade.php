@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <a href="{{ route('users.index') }}"
                class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                <i class="ri-arrow-left-line"></i> Kullanıcı listesi
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Kullanıcıyı düzenle</h2>
            <p class="text-slate-500 text-sm mt-1">{{ $user->email }}</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:p-8">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Ad Soyad</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">E-posta</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            required autocomplete="email"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Telefon <span
                                class="text-slate-400 font-normal">(isteğe bağlı)</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Rol</label>
                        <select name="role" id="role" required
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft bg-white">
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3 text-sm text-amber-900">
                    <p class="font-medium">Şifre değişikliği</p>
                    <p class="text-xs mt-1 opacity-90">Yalnızca değiştirmek istiyorsanız doldurun; boş bırakırsanız mevcut
                        şifre korunur.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Yeni şifre</label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Yeni şifre
                            tekrar</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            autocomplete="new-password"
                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft">
                    </div>
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="active" value="1"
                            class="rounded border-slate-300 text-brand focus:ring-brand" @checked(old('active', $user->active))>
                        <span class="text-sm text-slate-700">Hesap aktif</span>
                    </label>
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('users.index') }}"
                        class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-soft">Vazgeç</a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                        <i class="ri-save-3-line"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>

        @can('delete', $user)
            <div
                class="card bg-white rounded-2xl border border-red-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 !mt-6">
                <p class="text-sm text-slate-600">Bu kullanıcıyı sistemden tamamen kaldırır.</p>
                <form method="POST" action="{{ route('users.destroy', $user) }}"
                    onsubmit="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-soft w-full sm:w-auto">
                        <i class="ri-delete-bin-line"></i> Kullanıcıyı sil
                    </button>
                </form>
            </div>
        @endcan
    </div>
@endsection
