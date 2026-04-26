@extends('admin.layout')

@section('content')
    @php
        $navbarRows = old('navbar', $navbarMenu);
        $footerRows = old('footer', $footerMenu);

        $normalizeForJs = function ($rows) {
            return collect($rows)
                ->map(function ($row) {
                    $type = in_array($row['type'] ?? 'custom', ['custom', 'page', 'group'], true)
                        ? $row['type']
                        : 'custom';
                    return [
                        'type' => $type,
                        'parent' => $type === 'group' ? '' : (string) ($row['parent'] ?? ''),
                        'page_id' => isset($row['page_id']) && $row['page_id'] !== '' ? (int) $row['page_id'] : '',
                        'label' => (string) ($row['label'] ?? ''),
                        'url' => (string) ($row['url'] ?? ''),
                    ];
                })
                ->values()
                ->all();
        };

        $navbarInitial = $normalizeForJs($navbarRows);
        $footerInitial = $normalizeForJs($footerRows);
        $pagesForJs = $pages->map(fn ($p) => ['id' => $p->id, 'title' => $p->title, 'slug' => $p->slug])->values();
    @endphp

    <div class="fade-in space-y-6"
        x-data="menuBuilder({
            navbar: @js($navbarInitial),
            footer: @js($footerInitial),
            pages: @js($pagesForJs)
        })">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Menüler</h2>
            <p class="text-slate-500 text-sm mt-1">Sol panelden öğe ekle, sağdaki ağaçta sürükle-bırak ile sıralama ve hiyerarşiyi kur. Bir öğeyi grup başlığının üzerine bırakırsan alt menü olur.</p>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form action="{{ route('menus.update') }}" method="POST" class="flex flex-col">
                @csrf

                {{-- Tabs --}}
                <div class="border-b border-slate-200 px-4 lg:px-6">
                    <div class="flex gap-1 overflow-x-auto">
                        <button type="button" @click="activeTab = 'navbar'"
                            :class="['px-4 py-3 flex items-center gap-2 font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'navbar' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-menu-2-line"></i> Navbar
                            <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 rounded-full text-xs font-semibold"
                                :class="activeTab === 'navbar' ? 'bg-brand/10 text-brand' : 'bg-slate-100 text-slate-500'"
                                x-text="navbarRows.length"></span>
                        </button>
                        <button type="button" @click="activeTab = 'footer'"
                            :class="['px-4 py-3 flex items-center gap-2 font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'footer' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-layout-bottom-line"></i> Footer
                            <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 rounded-full text-xs font-semibold"
                                :class="activeTab === 'footer' ? 'bg-brand/10 text-brand' : 'bg-slate-100 text-slate-500'"
                                x-text="footerRows.length"></span>
                        </button>
                    </div>
                </div>

                <div class="p-4 lg:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                        {{-- =================== SOL: EKLE PANELI =================== --}}
                        <aside class="lg:col-span-2 space-y-4">
                            <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <h3 class="text-sm font-semibold text-slate-800">Yeni öğe ekle</h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Eklediğin öğe sağdaki ağaçta belirir; oradan sürükleyerek konumlandır.</p>
                                </div>

                                {{-- Segmented control --}}
                                <div class="px-4 pt-3">
                                    <div class="inline-flex p-1 bg-slate-100 rounded-lg w-full" role="group">
                                        <button type="button" @click="addMode = 'page'"
                                            :class="addMode === 'page' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-md text-xs font-semibold transition-soft">
                                            <i class="ri-file-text-line"></i> Sayfa
                                        </button>
                                        <button type="button" @click="addMode = 'group'"
                                            :class="addMode === 'group' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-md text-xs font-semibold transition-soft">
                                            <i class="ri-folder-2-line"></i> Grup
                                        </button>
                                        <button type="button" @click="addMode = 'custom'"
                                            :class="addMode === 'custom' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-md text-xs font-semibold transition-soft">
                                            <i class="ri-link-m"></i> Özel link
                                        </button>
                                    </div>
                                </div>

                                {{-- Mode: Sayfa --}}
                                <div x-show="addMode === 'page'" x-cloak class="p-4 space-y-3">
                                    <div class="relative">
                                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        <input type="text" x-model="pageQuery"
                                            class="input-base w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm"
                                            placeholder="Sayfa ara...">
                                    </div>
                                    <div class="space-y-1.5 max-h-72 overflow-auto pr-1">
                                        <template x-for="page in pagesFiltered()" :key="'pa_' + page.id">
                                            <div class="flex items-center justify-between gap-2 rounded-lg border border-slate-100 hover:border-slate-200 hover:bg-slate-50 px-3 py-2 transition">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-slate-800 truncate" x-text="page.title"></p>
                                                    <p class="text-[11px] text-slate-500 truncate font-mono">/<span x-text="page.slug"></span></p>
                                                </div>
                                                <template x-if="pageInUse(page.id)">
                                                    <span class="shrink-0 inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700">
                                                        <i class="ri-check-line"></i> Eklendi
                                                    </span>
                                                </template>
                                                <button type="button" @click="addPage(page.id)"
                                                    class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-slate-900 hover:bg-slate-800 px-2.5 py-1.5 text-xs font-semibold text-white transition">
                                                    <i class="ri-add-line"></i> Ekle
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="pagesFiltered().length === 0">
                                            <p class="text-xs text-slate-500 text-center py-6">Eşleşen sayfa yok.</p>
                                        </template>
                                    </div>
                                </div>

                                {{-- Mode: Grup --}}
                                <div x-show="addMode === 'group'" x-cloak class="p-4 space-y-2">
                                    <p class="text-xs text-slate-500">Bir grup başlığı oluştur, sonra ağaca sayfa veya özel link sürükleyerek alt menü kur.</p>
                                    <div class="flex items-center gap-2">
                                        <input type="text" x-model="newParentName" @keydown.enter.prevent="addParent()"
                                            class="input-base flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm"
                                            placeholder="Örn: Kurumsal">
                                        <button type="button" @click="addParent()"
                                            class="inline-flex items-center gap-1 rounded-lg bg-brand-solid hover:bg-brand-solid-hover px-3 py-2 text-xs font-semibold text-white">
                                            <i class="ri-add-line"></i> Ekle
                                        </button>
                                    </div>
                                </div>

                                {{-- Mode: Özel link --}}
                                <div x-show="addMode === 'custom'" x-cloak class="p-4 space-y-2">
                                    <input type="text" x-model="customLabel"
                                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"
                                        placeholder="Bağlantı başlığı (örn. KVKK)">
                                    <input type="text" x-model="customUrl"
                                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-mono text-xs"
                                        placeholder="/iletisim veya https://...">
                                    <button type="button" @click="addCustom()"
                                        class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-solid hover:bg-brand-solid-hover px-3 py-2 text-xs font-semibold text-white">
                                        <i class="ri-add-line"></i> Listeye ekle
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-xl border border-blue-100 bg-blue-50/70 p-3 text-xs text-blue-900">
                                <p class="font-medium flex items-center gap-1.5"><i class="ri-information-line"></i> Nasıl çalışır?</p>
                                <ul class="mt-1 space-y-1 leading-relaxed list-disc list-inside">
                                    <li>Bir öğeyi <strong>grubun</strong> üzerine sürüklersen alt menü olur.</li>
                                    <li><strong>Üst seviye</strong> alanına sürüklersen ana menüde gözükür.</li>
                                    <li>Boş satırlar kayıt sırasında atlanır.</li>
                                </ul>
                            </div>
                        </aside>

                        {{-- =================== SAG: AGAC =================== --}}
                        <main class="lg:col-span-3 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                    <i class="ri-node-tree text-slate-400"></i> Menü yapısı
                                </h3>
                                <p class="text-[11px] text-slate-500" x-text="rowSummary(activeTab)"></p>
                            </div>

                            {{-- Bos durum --}}
                            <template x-if="rowsOf(activeTab).length === 0">
                                <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/40 p-10 text-center">
                                    <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <i class="ri-inbox-line text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700">Bu menüde henüz öğe yok.</p>
                                    <p class="text-xs text-slate-500 mt-1">Sol panelden bir sayfa ya da grup ekleyerek başla.</p>
                                </div>
                            </template>

                            {{-- Tek treeview: ust seviye + gruplar ic ice --}}
                            <section x-show="rowsOf(activeTab).length > 0"
                                x-cloak
                                class="rounded-xl border-2 border-dashed transition"
                                :class="dropTarget === 'root' ? 'border-brand bg-brand/5' : 'border-slate-200 bg-slate-50/30'"
                                @dragover.prevent="onDragOver('root')"
                                @dragleave="onDragLeave('root')"
                                @drop.prevent="dropToRoot()">
                                <header class="flex items-center justify-between px-4 py-2.5 border-b border-slate-200/70">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-node-tree text-slate-400"></i>
                                        <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Menü ağacı</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500"
                                        x-text="rootRowsOf(activeTab).length + ' üst öğe'"></span>
                                </header>
                                <div class="p-3 space-y-2 min-h-[3rem]">
                                    <template x-for="row in rootRowsOf(activeTab)" :key="'root_' + row._id">
                                        <div>
                                            <template x-if="row.type !== 'group'">
                                                <div>
                                                    @include('admin.menus.partials.row', ['parentExpr' => 'row.parent'])
                                                </div>
                                            </template>
                                            <template x-if="row.type === 'group'">
                                                <div class="rounded-lg border-2 transition overflow-hidden"
                                                    :class="dropTarget === ('parent:' + row.label) ? 'border-brand bg-brand/5' : (dragging && dragging.rowId === row._id ? 'opacity-40 border-brand' : 'border-slate-200 bg-white')"
                                                    draggable="true"
                                                    @dragstart.stop="onDragStart(activeTab, row._id, $event)"
                                                    @dragend="onDragEnd()"
                                                    @dragover.prevent.stop="onDragOver('parent:' + row.label)"
                                                    @dragleave.stop="onDragLeave('parent:' + row.label)"
                                                    @drop.prevent.stop="dropToParent(row.label)">
                                                    <header class="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-brand/5 via-transparent to-transparent border-b border-slate-100 cursor-grab select-none">
                                                        <i class="ri-draggable text-slate-300 text-base shrink-0"></i>
                                                        <i class="ri-folder-2-fill text-brand shrink-0"></i>
                                                        <span class="text-sm font-semibold text-slate-800 truncate flex-1" x-text="row.label || '(Grup adı yok)'"></span>
                                                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-slate-100 text-[10px] font-semibold text-slate-600"
                                                            x-text="childrenOf(activeTab, row.label).length"></span>
                                                        <button type="button" @click.stop="row._editing = !row._editing"
                                                            :class="row._editing ? 'text-brand bg-brand/10' : 'text-slate-400 hover:text-slate-700 hover:bg-slate-100'"
                                                            class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md transition" title="Yeniden adlandır">
                                                            <i class="ri-pencil-line text-sm"></i>
                                                        </button>
                                                        <button type="button" @click.stop="removeGroupRow(activeTab, row._id)"
                                                            class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
                                                            title="Grubu sil (alt öğeler üst seviyeye taşınır)">
                                                            <i class="ri-delete-bin-line text-sm"></i>
                                                        </button>
                                                    </header>
                                                    <div x-show="row._editing" x-cloak class="px-3 pb-3 pt-2 border-b border-slate-100 bg-slate-50/40">
                                                        <label class="block text-[11px] font-medium uppercase tracking-wide text-slate-500 mb-1">Grup adı</label>
                                                        <input type="text"
                                                            :value="row.label"
                                                            @input="renameGroup(activeTab, row._id, $event.target.value)"
                                                            class="input-base w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm bg-white"
                                                            placeholder="Örn: Kurumsal">
                                                    </div>
                                                    <div class="p-3 pl-6 space-y-2 min-h-[3rem]">
                                                        <template x-for="child in childrenOf(activeTab, row.label)" :key="'ch_' + child._id">
                                                            <div x-data="{ row: child }">
                                                                @include('admin.menus.partials.row', ['parentExpr' => 'row.parent'])
                                                            </div>
                                                        </template>
                                                        <template x-if="childrenOf(activeTab, row.label).length === 0">
                                                            <p class="text-xs text-slate-400 text-center py-3 select-none">
                                                                Bu grup boş — üzerine bir öğe sürükle
                                                            </p>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </main>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 mt-6">
                            <p class="font-medium flex items-center gap-2"><i class="ri-error-warning-line"></i> Lütfen formu kontrol edin.</p>
                            <ul class="mt-2 ml-1 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Sticky save bar --}}
                <div class="sticky bottom-0 z-10 border-t border-slate-100 bg-white/95 backdrop-blur px-4 lg:px-6 py-3 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        Toplam <span class="font-semibold text-slate-700" x-text="navbarRows.length"></span> navbar,
                        <span class="font-semibold text-slate-700" x-text="footerRows.length"></span> footer satırı.
                    </p>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                        <i class="ri-save-3-line text-lg"></i>
                        Menüleri kaydet
                    </button>
                </div>

                {{-- Hidden submit alanlari --}}
                <div class="hidden">
                    <template x-for="(row, idx) in navbarRows" :key="'sn_' + row._id">
                        <div>
                            <input type="hidden" :name="`navbar[${idx}][type]`" :value="row.type">
                            <input type="hidden" :name="`navbar[${idx}][parent]`" :value="(row.parent || '').trim()">
                            <input type="hidden" :name="`navbar[${idx}][page_id]`" :value="row.page_id ?? ''">
                            <input type="hidden" :name="`navbar[${idx}][label]`" :value="row.label || ''">
                            <input type="hidden" :name="`navbar[${idx}][url]`" :value="row.url || ''">
                        </div>
                    </template>
                    <template x-for="(row, idx) in footerRows" :key="'sf_' + row._id">
                        <div>
                            <input type="hidden" :name="`footer[${idx}][type]`" :value="row.type">
                            <input type="hidden" :name="`footer[${idx}][parent]`" :value="(row.parent || '').trim()">
                            <input type="hidden" :name="`footer[${idx}][page_id]`" :value="row.page_id ?? ''">
                            <input type="hidden" :name="`footer[${idx}][label]`" :value="row.label || ''">
                            <input type="hidden" :name="`footer[${idx}][url]`" :value="row.url || ''">
                        </div>
                    </template>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function menuBuilder(initial) {
                const makeRow = (data = {}) => ({
                    _id: 'r_' + Math.random().toString(36).slice(2, 10),
                    type: data.type || 'custom',
                    parent: data.parent || '',
                    page_id: data.page_id ?? '',
                    label: data.label || '',
                    url: data.url || '',
                    _editing: false,
                });

                return {
                    activeTab: 'navbar',
                    pages: initial.pages || [],
                    navbarRows: (initial.navbar || []).map(makeRow),
                    footerRows: (initial.footer || []).map(makeRow),
                    addMode: 'page',
                    pageQuery: '',
                    newParentName: '',
                    customLabel: '',
                    customUrl: '',
                    dragging: null,
                    dropTarget: null,

                    // Selectors
                    rowsOf(group) { return this[group + 'Rows']; },
                    rootRowsOf(group) {
                        return this[group + 'Rows'].filter(r => r.type === 'group' || String(r.parent || '').trim() === '');
                    },
                    childrenOf(group, parent) {
                        const name = String(parent || '').trim();
                        if (name === '') return [];
                        return this[group + 'Rows'].filter(r => r.type !== 'group' && String(r.parent || '').trim() === name);
                    },
                    parentsOf(group) {
                        return this[group + 'Rows']
                            .filter(r => r.type === 'group')
                            .map(r => String(r.label || '').trim())
                            .filter(l => l !== '');
                    },
                    pagesFiltered() {
                        const q = this.pageQuery.trim().toLowerCase();
                        if (!q) return this.pages;
                        return this.pages.filter(p =>
                            String(p.title).toLowerCase().includes(q) ||
                            String(p.slug).toLowerCase().includes(q)
                        );
                    },
                    pageInUse(pageId) {
                        return this[this.activeTab + 'Rows'].some(r => String(r.page_id) === String(pageId));
                    },
                    pageLookup(id) {
                        if (id === '' || id === null || id === undefined) return null;
                        return this.pages.find(p => String(p.id) === String(id)) || null;
                    },
                    rowLabel(row) {
                        if (row.type === 'page') {
                            const p = this.pageLookup(row.page_id);
                            return p ? p.title : 'Sayfa seçilmedi';
                        }
                        return row.label || '(Başlık yok)';
                    },
                    rowUrl(row) {
                        if (row.type === 'page') {
                            const p = this.pageLookup(row.page_id);
                            return p ? '/' + String(p.slug).replace(/^\//, '') : '';
                        }
                        return row.url || '';
                    },
                    rowSummary(group) {
                        const rows = this[group + 'Rows'];
                        const groupCount = rows.filter(r => r.type === 'group').length;
                        const childCount = rows.filter(r => r.type !== 'group' && String(r.parent || '').trim() !== '').length;
                        const topLevelItems = rows.length - groupCount - childCount;
                        if (rows.length === 0) return 'Henüz öğe yok.';
                        const parts = [];
                        if (topLevelItems > 0) parts.push(`${topLevelItems} üst seviye`);
                        if (groupCount > 0) parts.push(`${groupCount} grup`);
                        if (childCount > 0) parts.push(`${childCount} alt öğe`);
                        return parts.length ? parts.join(' · ') : 'Henüz öğe yok.';
                    },

                    // Adders
                    addPage(pageId) {
                        const page = this.pageLookup(pageId);
                        if (!page) return;
                        this[this.activeTab + 'Rows'].push(makeRow({ type: 'page', page_id: page.id }));
                    },
                    addParent() {
                        const name = String(this.newParentName || '').trim();
                        if (!name) return;
                        const exists = this[this.activeTab + 'Rows']
                            .some(r => r.type === 'group' && String(r.label || '').trim() === name);
                        if (!exists) {
                            this[this.activeTab + 'Rows'].push(makeRow({ type: 'group', label: name }));
                        }
                        this.newParentName = '';
                    },
                    addCustom() {
                        const label = String(this.customLabel || '').trim();
                        const url = String(this.customUrl || '').trim();
                        if (!label && !url) return;
                        this[this.activeTab + 'Rows'].push(makeRow({ type: 'custom', label, url }));
                        this.customLabel = '';
                        this.customUrl = '';
                    },

                    // Mutations
                    removeById(group, rowId) {
                        const arr = this[group + 'Rows'];
                        const idx = arr.findIndex(r => r._id === rowId);
                        if (idx !== -1) arr.splice(idx, 1);
                    },
                    removeGroupRow(group, rowId) {
                        const arr = this[group + 'Rows'];
                        const target = arr.find(r => r._id === rowId);
                        if (!target || target.type !== 'group') return;
                        const oldLabel = String(target.label || '').trim();
                        for (const row of arr) {
                            if (row._id !== rowId && String(row.parent || '').trim() === oldLabel) {
                                row.parent = '';
                            }
                        }
                        const idx = arr.findIndex(r => r._id === rowId);
                        if (idx !== -1) arr.splice(idx, 1);
                    },
                    renameGroup(group, rowId, newLabel) {
                        const arr = this[group + 'Rows'];
                        const target = arr.find(r => r._id === rowId);
                        if (!target || target.type !== 'group') return;
                        const oldLabel = String(target.label || '');
                        const newVal = String(newLabel || '');
                        if (oldLabel === newVal) return;
                        for (const row of arr) {
                            if (row._id !== rowId && String(row.parent || '') === oldLabel) {
                                row.parent = newVal;
                            }
                        }
                        target.label = newVal;
                    },

                    // Drag & drop
                    onDragStart(group, rowId, e) {
                        this.dragging = { group, rowId };
                        if (e && e.dataTransfer) {
                            e.dataTransfer.effectAllowed = 'move';
                            try { e.dataTransfer.setData('text/plain', rowId); } catch (_) {}
                        }
                    },
                    onDragEnd() {
                        this.dragging = null;
                        this.dropTarget = null;
                    },
                    onDragOver(target) {
                        if (!this.dragging || this.dragging.group !== this.activeTab) return;
                        if (this.dropTarget !== target) this.dropTarget = target;
                    },
                    onDragLeave(target) {
                        if (this.dropTarget === target) this.dropTarget = null;
                    },
                    findRowById(group, rowId) {
                        return this[group + 'Rows'].find(r => r._id === rowId) || null;
                    },
                    dropToRoot() {
                        if (!this.dragging || this.dragging.group !== this.activeTab) {
                            this.dropTarget = null; return;
                        }
                        const row = this.findRowById(this.activeTab, this.dragging.rowId);
                        if (row) row.parent = '';
                        this.dragging = null;
                        this.dropTarget = null;
                    },
                    dropToParent(parentName) {
                        if (!this.dragging || this.dragging.group !== this.activeTab) {
                            this.dropTarget = null; return;
                        }
                        const row = this.findRowById(this.activeTab, this.dragging.rowId);
                        if (row) {
                            // Gruplar ic ice gecemez — hedef baska bir grup ise koruma
                            if (row.type === 'group') {
                                row.parent = '';
                            } else {
                                row.parent = String(parentName || '').trim();
                            }
                        }
                        this.dragging = null;
                        this.dropTarget = null;
                    },
                    dropAfterRow(targetRowId, parentName) {
                        if (!this.dragging || this.dragging.group !== this.activeTab) {
                            this.dropTarget = null; return;
                        }
                        if (this.dragging.rowId === targetRowId) {
                            this.dragging = null; this.dropTarget = null; return;
                        }
                        const arr = this[this.activeTab + 'Rows'];
                        const fromIdx = arr.findIndex(r => r._id === this.dragging.rowId);
                        const targetIdx = arr.findIndex(r => r._id === targetRowId);
                        if (fromIdx === -1 || targetIdx === -1) return;
                        const row = arr[fromIdx];
                        // Gruplar her zaman ust seviyede kalir
                        row.parent = row.type === 'group' ? '' : String(parentName || '').trim();
                        arr.splice(fromIdx, 1);
                        const insertAt = fromIdx < targetIdx ? targetIdx : targetIdx + 1;
                        arr.splice(insertAt, 0, row);
                        this.dragging = null;
                        this.dropTarget = null;
                    },
                };
            }
        </script>
    @endpush
@endsection
