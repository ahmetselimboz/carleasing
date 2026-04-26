@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Kullanıcılar</h2>
                <p class="text-slate-500 text-sm mt-1">Panel kullanıcılarını buradan yönetebilirsiniz.</p>
            </div>
            @can('create', App\Models\User::class)
                <a href="{{ route('users.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                    <i class="ri-user-add-line text-lg"></i>
                    Yeni kullanıcı
                </a>
            @endcan
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Ad</th>
                            <th class="px-4 py-3 font-medium">E-posta</th>
                            <th class="px-4 py-3 font-medium">Rol</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                            <th class="px-4 py-3 font-medium hidden md:table-cell">Kayıt</th>
                            <th class="px-4 py-3 font-medium text-right w-40">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-medium border
                                        @if($u->role === \App\Models\User::ROLE_ADMIN) bg-[var(--color-brand-soft)] text-brand border-brand
                                        @else bg-slate-100 text-slate-700 border-slate-200 @endif">
                                        {{ $u->roleLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($u->active)
                                        <span class="text-emerald-600 text-xs font-medium">Aktif</span>
                                    @else
                                        <span class="text-slate-400 text-xs font-medium">Pasif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs hidden md:table-cell whitespace-nowrap">
                                    {{ $u->created_at?->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1 flex-wrap">
                                        @can('update', $u)
                                            <a href="{{ route('users.edit', $u) }}"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-brand hover:bg-[#37008a]/10 transition-soft">
                                                <i class="ri-pencil-line"></i> Düzenle
                                            </a>
                                        @endcan
                                        @can('delete', $u)
                                            <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline"
                                                onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-soft">
                                                    <i class="ri-delete-bin-line"></i> Sil
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                    Henüz kayıtlı kullanıcı yok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
