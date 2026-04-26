@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Sayfa kategorileri</h2>
                <p class="text-slate-500 text-sm mt-1">Sayfalar için kategori gruplarını bu alandan yönetebilirsiniz.</p>
            </div>
            @can('create', App\Models\PageCategory::class)
                <a href="{{ route('page-categories.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-add-line text-lg"></i>
                    Yeni kategori
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form method="GET" class="px-4 pt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kategori adı ara..."
                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                <select name="status" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="">Tüm durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="passive" @selected(request('status') === 'passive')>Pasif</option>
                </select>
                <select name="sort" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>En yeni</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>En eski</option>
                    <option value="name_asc" @selected(request('sort') === 'name_asc')>İsim: A-Z</option>
                    <option value="name_desc" @selected(request('sort') === 'name_desc')>İsim: Z-A</option>
                </select>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-soft">
                    <i class="ri-filter-3-line"></i> Filtrele
                </button>
            </form>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-y border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium">Sayfa sayısı</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                            <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">{{ $category->name }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">#{{ $category->id }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $category->pages_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($category->is_active)
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Pasif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('update', $category)
                                        <a href="{{ route('page-categories.edit', $category) }}"
                                            class="text-xs font-medium text-brand hover:underline">Düzenle</a>
                                    @endcan
                                    @can('delete', $category)
                                        <form method="POST" action="{{ route('page-categories.destroy', $category) }}"
                                            class="inline ml-2"
                                            onsubmit="return confirm('Bu sayfa kategorisini silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Sil</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                    Henüz sayfa kategorisi bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($categories, 'links'))
                <div class="px-4 py-4 border-t border-slate-100">
                    {{ $categories->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
