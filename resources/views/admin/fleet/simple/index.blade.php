@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}"
                    class="text-sm text-slate-500 hover:text-brand inline-flex items-center gap-1 mb-2">
                    <i class="ri-arrow-left-line"></i> Panel
                </a>
                <h2 class="text-3xl font-bold text-slate-800">{{ $title }}</h2>
                @if (!empty($description))
                    <p class="text-slate-500 text-sm mt-1">{{ $description }}</p>
                @endif
            </div>
            @can('create', $modelFqcn)
                <a href="{{ route($routePrefix . '.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-add-line text-lg"></i>
                    Yeni kayıt
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium w-16">#</th>
                            @foreach ($columns as $col)
                                <th class="px-4 py-3 font-medium">{{ $col['label'] }}</th>
                            @endforeach
                            <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 text-slate-500">{{ $item->id }}</td>
                                @foreach ($columns as $col)
                                    <td class="px-4 py-3 text-slate-800">
                                        @if (($col['type'] ?? '') === 'bool')
                                            @if ($item->{$col['key']})
                                                <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                                            @else
                                                <span class="text-slate-400 text-xs font-medium">Pasif</span>
                                            @endif
                                        @elseif (($col['type'] ?? '') === 'map')
                                            {{ $col['map'][$item->{$col['key']}] ?? $item->{$col['key']} }}
                                        @else
                                            {{ $item->{$col['key']} ?: '—' }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right ">
                                    <div class="flex items-center justify-end gap-1 ">
                                        @can('update', $item)
                                            <a href="{{ route($routePrefix . '.edit', $item) }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                                <i class="ri-pencil-line"></i> Düzenle
                                            </a>
                                        @endcan
                                        @can('delete', $item)
                                            <form method="POST" action="{{ route($routePrefix . '.destroy', $item) }}"
                                                class="inline"
                                                onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-500/10 cursor-pointer transition-soft">
                                                    <i class="ri-delete-bin-line"></i> Sil
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 2 }}" class="px-4 py-12 text-center text-slate-500">
                                    Henüz kayıt yok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
