@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Mesajlar</h2>
            <p class="text-slate-500 text-sm mt-1">Bize Yazın formundan gelen mesajlar burada listelenir.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Gönderen</th>
                            <th class="px-4 py-3 font-medium hidden lg:table-cell">Kategori</th>
                            <th class="px-4 py-3 font-medium">İletişim</th>
                            <th class="px-4 py-3 font-medium">Okuma</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Tarih</th>
                            <th class="px-4 py-3 font-medium text-right w-28">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($messages as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-800">{{ trim(($item->name ?: '') . ' ' . ($item->surname ?: '')) ?: '—' }}</p>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $item->content ?: '—' }}</p>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell text-slate-700">
                                    {{ $item->categoryLabel() }}
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs">
                                    <div>{{ $item->email ?: '—' }}</div>
                                    <div>{{ $item->phone_number ?: '' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->read_at)
                                        <span class="text-xs text-slate-500">Okundu</span>
                                    @else
                                        <span class="text-xs font-medium border border-red-500/10 rounded-lg px-2 py-1 bg-red-500/10 text-red-600">Yeni</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs hidden md:table-cell whitespace-nowrap">
                                    {{ $item->created_at?->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('view', $item)
                                        <a href="{{ route('messages.show', $item) }}"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                            Detay
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">Henüz mesaj yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($messages->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
