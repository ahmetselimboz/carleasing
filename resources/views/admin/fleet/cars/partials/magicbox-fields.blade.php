@php
    $mbInitial = old('mb', $mbRows ?? [\App\Support\MagicboxForm::emptyRow()]);
@endphp
<div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 space-y-3"
    x-data="{
        rows: @js($mbInitial),
        addRow() {
            this.rows.push({ key: '', type: 'string', value: '' });
        },
        removeRow(i) {
            this.rows.splice(i, 1);
            if (this.rows.length === 0) {
                this.addRow();
            }
        },
        onTypeChange(row) {
            if (row.type === 'bool') {
                row.value = (row.value === '1' || row.value === 1 || row.value === true) ? '1' : '0';
            }
        }
    }">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
        <div>
            <span class="block text-sm font-medium text-slate-700">Ek bilgiler</span>
            <p class="text-xs text-slate-500 mt-0.5 max-w-xl">
                Araçla ilgili ek notları buraya alan adı ve değer olarak girebilirsiniz.
                Örnek: <strong>segment</strong> -> <strong>C</strong>, <strong>one-cikan</strong> -> Evet/Hayır.
            </p>
        </div>
        <button type="button" @click="addRow()"
            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-soft shrink-0">
            <i class="ri-add-line"></i> Alan ekle
        </button>
    </div>

    @error('mb')
        <p class="text-red-500 text-sm">{{ $message }}</p>
    @enderror

    <div class="space-y-3">
        <template x-for="(row, index) in rows" :key="index">
            <div
                class="grid grid-cols-1 md:grid-cols-12 gap-2 items-end p-3 rounded-xl bg-white border border-slate-100">
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1" :for="'mb-key-' + index">Alan adı</label>
                    <input type="text" :id="'mb-key-' + index" :name="'mb[' + index + '][key]'" x-model="row.key"
                        placeholder="ör. segment"
                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand/20 focus:border-brand">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1" :for="'mb-type-' + index">Bilgi türü</label>
                    <select :id="'mb-type-' + index" :name="'mb[' + index + '][type]'" x-model="row.type"
                        @change="onTypeChange(row)"
                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <option value="string">Metin</option>
                        <option value="int">Tam sayı</option>
                        <option value="bool">Evet / Hayır</option>
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1" :for="'mb-val-' + index">Değer</label>
                    <input type="text" :id="'mb-val-' + index"
                        :name="row.type === 'string' ? 'mb[' + index + '][value]' : false" x-model="row.value"
                        x-show="row.type === 'string'"
                        placeholder="Metin"
                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <input type="number" step="1"
                        :name="row.type === 'int' ? 'mb[' + index + '][value]' : false" x-model="row.value"
                        x-show="row.type === 'int'"
                        placeholder="0"
                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand/20 focus:border-brand">
                    <select :name="row.type === 'bool' ? 'mb[' + index + '][value]' : false" x-model="row.value"
                        x-show="row.type === 'bool'"
                        class="input-base w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand">
                        <option value="0">Hayır</option>
                        <option value="1">Evet</option>
                    </select>
                </div>
                <div class="md:col-span-1 flex md:justify-end pb-0.5">
                    <button type="button" @click="removeRow(index)" title="Satırı kaldır"
                        class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-soft">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
