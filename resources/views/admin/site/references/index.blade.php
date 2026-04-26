@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Bizden memnun olanlar</h2>
                <p class="text-slate-500 text-sm mt-1">Bizi tercih eden marka ve kurumsal referanslarınızı bu alandan yönetebilirsiniz.</p>
            </div>
            @can('create', App\Models\Reference::class)
                <a href="{{ route('references.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-add-line text-lg"></i>
                    Yeni referans
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form method="GET" class="px-4 pt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Marka adı veya başlık ara..."
                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                <select name="status" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="">Tüm durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="passive" @selected(request('status') === 'passive')>Pasif</option>
                </select>
                <select name="sort" class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>En yeni</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>En eski</option>
                    <option value="name_asc" @selected(request('sort') === 'name_asc')>Marka: A-Z</option>
                    <option value="name_desc" @selected(request('sort') === 'name_desc')>Marka: Z-A</option>
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
                            <th class="px-4 py-3 font-medium w-20">Logo</th>
                            <th class="px-4 py-3 font-medium">Marka</th>
                            <th class="px-4 py-3 font-medium">Başlık</th>
                            <th class="px-4 py-3 font-medium">Web sitesi</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                            <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($references as $reference)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    @if ($reference->image)
                                        <img src="{{ asset('storage/' . $reference->image) }}" alt="{{ $reference->name }}"
                                            class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                    @else
                                        <span
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                            <i class="ri-image-line"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">{{ $reference->name ?: '-' }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">#{{ $reference->id }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $reference->title ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($reference->link)
                                        <a href="{{ $reference->link }}" target="_blank" rel="noopener"
                                            class="text-brand hover:underline break-all">
                                            {{ $reference->link }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($reference->is_active)
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Pasif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('update', $reference)
                                        <a href="{{ route('references.edit', $reference) }}"
                                            class="text-xs font-medium text-brand hover:underline">Düzenle</a>
                                    @endcan
                                    @can('delete', $reference)
                                        <form method="POST" action="{{ route('references.destroy', $reference) }}"
                                            class="inline ml-2"
                                            onsubmit="return confirm('Bu referansı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Sil</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    Henüz referans kaydı bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($references, 'links'))
                <div class="px-4 py-4 border-t border-slate-100">
                    {{ $references->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
