<div class="rounded-lg border bg-white shadow-sm transition group select-none"
    :class="dragging && dragging.rowId === row._id ? 'opacity-40 border-brand' : 'border-slate-200 hover:border-slate-300'"
    draggable="true" @dragstart="onDragStart(activeTab, row._id, $event)" @dragend="onDragEnd()" @dragover.prevent.stop
    @drop.prevent.stop="dropAfterRow(row._id, {{ $parentExpr }})">

    <div class="flex items-center gap-2 px-2.5 py-2">
        <i class="ri-draggable text-slate-300 group-hover:text-slate-500 cursor-grab text-base shrink-0"></i>

        <span class="inline-flex items-center justify-center w-7 h-7 rounded-md text-xs shrink-0"
            :class="row.type === 'page' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'">
            <i :class="row.type === 'page' ? 'ri-file-text-line' : 'ri-link-m'"></i>
        </span>

        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-800 truncate" x-text="rowLabel(row)"></p>
            <p class="text-[11px] text-slate-500 font-mono truncate" x-text="rowUrl(row) || '—'"></p>
        </div>

        <button type="button" @click="row._editing = !row._editing"
            :class="row._editing ? 'text-brand bg-brand/10' : 'text-slate-400 hover:text-slate-700 hover:bg-slate-100'"
            class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md transition" title="Düzenle">
            <i class="ri-pencil-line text-sm"></i>
        </button>
        <button type="button" @click.stop="removeById(activeTab, row._id)"
            class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition"
            title="Sil">
            <i class="ri-delete-bin-line text-sm"></i>
        </button>
    </div>

   
    <div x-show="row._editing" x-cloak class="px-3 pb-3 pt-2 border-t border-slate-100 space-y-2 bg-slate-50/40">
        <template x-if="row.type === 'page'">
            <div>
                <label class="block text-[11px] font-medium uppercase tracking-wide text-slate-500 mb-1">Sistem
                    sayfası</label>
                <select x-model="row.page_id"
                    class="input-base w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm bg-white">
                    <option value="">Seçiniz...</option>
                    <template x-for="page in pages" :key="'opt_' + row._id + '_' + page.id">
                        <option :value="page.id" x-text="page.title + ' (/' + page.slug + ')'"></option>
                    </template>
                </select>
            </div>
        </template>
        <template x-if="row.type === 'custom'">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label
                        class="block text-[11px] font-medium uppercase tracking-wide text-slate-500 mb-1">Başlık</label>
                    <input type="text" x-model="row.label"
                        class="input-base w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm"
                        placeholder="Örn: Hakkımızda">
                </div>
                <div>
                    <label class="block text-[11px] font-medium uppercase tracking-wide text-slate-500 mb-1">URL</label>
                    <input type="text" x-model="row.url"
                        class="input-base w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-sm font-mono text-xs"
                        placeholder="/hakkimizda veya https://...">
                </div>
            </div>
        </template>
    </div>
</div>
