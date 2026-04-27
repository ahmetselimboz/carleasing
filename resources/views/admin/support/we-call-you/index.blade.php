@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Geri arama talepleri</h2>
                <p class="text-slate-500 text-sm mt-1">"Biz Sizi Arayalım" formundan gelen talepler burada listelenir.</p>
            </div>
            <div class="flex gap-1 rounded-xl bg-slate-100 p-1">
                @foreach ([
                    'all' => 'Tümü',
                    'pending' => 'Bekleyen',
                    'read' => 'Okundu',
                ] as $value => $label)
                    <a href="{{ route('we-call-you.index', ['status' => $value]) }}"
                        class="px-4 py-2 rounded-lg text-xs font-semibold transition-soft {{ $status === $value ? 'bg-white text-brand shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Gönderen</th>
                            <th class="px-4 py-3 font-medium">Telefon</th>
                            <th class="px-4 py-3 font-medium hidden lg:table-cell">Araç</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Tercih edilen zaman</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Tarih</th>
                            <th class="px-4 py-3 font-medium text-right w-28">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-800">
                                        {{ trim(($item->name ?: '') . ' ' . ($item->surname ?: '')) ?: '—' }}
                                    </p>
                                    @if ($item->city)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $item->city }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                    {{ $item->phone_number ?: '—' }}
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell text-slate-600 text-xs">
                                    @if ($item->car)
                                        <a href="{{ route('cars.show', $item->car->slug) }}" target="_blank"
                                            class="text-brand hover:underline">{{ $item->car->title }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell text-slate-600 text-xs">
                                    {{ $item->preferred_time ?: 'Fark etmez' }}
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
                                        <a href="{{ route('we-call-you.show', $item) }}"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                            Detay
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-500">Henüz talep yok.</td>
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
