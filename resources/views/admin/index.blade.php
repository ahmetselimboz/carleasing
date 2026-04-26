@extends('admin.layout')

@section('content')
    <!-- Welcome -->
    <div class="fade-in">
        <h2 class="text-xl font-bold text-slate-800">Hoş geldiniz, Ahmet!</h2>
        <p class="text-slate-500 text-sm mt-1">İşte bugünkü özetiniz.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-soft">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Toplam Kullanıcı</span>
                <span class="p-2.5 bg-[#37008a]/10 rounded-xl text-[#37008a]"><i class="ri-user-3-line text-xl"></i></span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-3">1,234</p>
            <p class="text-sm text-emerald-600 mt-1 flex items-center gap-1"><i class="ri-arrow-up-line"></i>
                +12% bu ay</p>
        </div>
        <div class="stat-card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-soft">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Gelir</span>
                <span class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600"><i
                        class="ri-money-dollar-circle-line text-xl"></i></span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-3">₺45,678</p>
            <p class="text-sm text-emerald-600 mt-1 flex items-center gap-1"><i class="ri-arrow-up-line"></i>
                +8% bu ay</p>
        </div>
        <div class="stat-card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-soft">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Siparişler</span>
                <span class="p-2.5 bg-amber-50 rounded-xl text-amber-600"><i
                        class="ri-shopping-bag-3-line text-xl"></i></span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-3">567</p>
            <p class="text-sm text-red-600 mt-1 flex items-center gap-1"><i class="ri-arrow-down-line"></i>
                -3% bu ay</p>
        </div>
        <div class="stat-card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-soft">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Dönüşüm</span>
                <span class="p-2.5 bg-blue-50 rounded-xl text-blue-600"><i class="ri-line-chart-line text-xl"></i></span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-3">%24</p>
            <p class="text-sm text-[#37008a] mt-1 flex items-center gap-1"><i class="ri-arrow-up-line"></i>
                +2% bu ay</p>
        </div>
    </div>

    <!-- Chart & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Satış Grafiği</h3>
                <select class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-[#37008a]/20">
                    <option>Son 7 gün</option>
                    <option>Son 30 gün</option>
                    <option>Bu yıl</option>
                </select>
            </div>
            <div class="h-72 flex items-center justify-center bg-slate-50 rounded-xl border border-slate-100">
                <div class="text-center text-slate-400">
                    <i class="ri-line-chart-line text-5xl mb-3 block"></i>
                    <p class="text-sm">Grafik alanı</p>
                    <p class="text-xs mt-1">Chart.js entegre edilebilir</p>
                </div>
            </div>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-4">Son Aktiviteler</h3>
            <div class="space-y-4">
                <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition-soft">
                    <span
                        class="w-10 h-10 rounded-lg bg-[#37008a]/10 flex items-center justify-center text-[#37008a] flex-shrink-0"><i
                            class="ri-shopping-cart-line"></i></span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Yeni sipariş alındı</p>
                        <p class="text-xs text-slate-500">2 dakika önce</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition-soft">
                    <span
                        class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0"><i
                            class="ri-checkbox-circle-line"></i></span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Ödeme onaylandı</p>
                        <p class="text-xs text-slate-500">15 dakika önce</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition-soft">
                    <span
                        class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0"><i
                            class="ri-user-add-line"></i></span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Yeni kullanıcı kaydı</p>
                        <p class="text-xs text-slate-500">1 saat önce</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 rounded-xl hover:bg-slate-50 transition-soft">
                    <span
                        class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0"><i
                            class="ri-archive-line"></i></span>
                    <div>
                        <p class="text-sm font-medium text-slate-800">Stok güncellendi</p>
                        <p class="text-xs text-slate-500">2 saat önce</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Son Siparişler</h3>
                <a href="#" class="text-sm text-[#37008a] hover:underline font-medium">Tümünü Gör</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-4 font-medium text-slate-500">Sipariş No</th>
                            <th class="text-left py-3 px-4 font-medium text-slate-500">Müşteri</th>
                            <th class="text-left py-3 px-4 font-medium text-slate-500">Durum</th>
                            <th class="text-right py-3 px-4 font-medium text-slate-500">Tutar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-soft">
                            <td class="py-3 px-4 font-medium">#ORD-1001</td>
                            <td class="py-3 px-4 text-slate-600">Ayşe Demir</td>
                            <td class="py-3 px-4"><span
                                    class="px-2 py-1 text-xs rounded-lg bg-emerald-100 text-emerald-700">Tamamlandı</span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium">₺299</td>
                        </tr>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-soft">
                            <td class="py-3 px-4 font-medium">#ORD-1002</td>
                            <td class="py-3 px-4 text-slate-600">Mehmet Kaya</td>
                            <td class="py-3 px-4"><span
                                    class="px-2 py-1 text-xs rounded-lg bg-amber-100 text-amber-700">Kargoda</span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium">₺1.499</td>
                        </tr>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-soft">
                            <td class="py-3 px-4 font-medium">#ORD-1003</td>
                            <td class="py-3 px-4 text-slate-600">Zeynep Yıldız</td>
                            <td class="py-3 px-4"><span
                                    class="px-2 py-1 text-xs rounded-lg bg-blue-100 text-blue-700">Hazırlanıyor</span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium">₺449</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800 mb-4">Hızlı İşlemler</h3>
            <div class="space-y-3">
                <a href="pages/index.html"
                    class="flex items-center gap-3 p-3 rounded-xl bg-[#37008a] text-white hover:bg-[#4d00a3] transition-soft">
                    <i class="ri-palette-line text-xl"></i>
                    <span class="font-medium">Komponentler</span>
                </a>
                <a href="pages/forms.html"
                    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-soft">
                    <i class="ri-file-list-3-line text-xl text-slate-600"></i>
                    <span class="font-medium text-slate-700">Form Örnekleri</span>
                </a>
                <a href="pages/tables.html"
                    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-soft">
                    <i class="ri-table-line text-xl text-slate-600"></i>
                    <span class="font-medium text-slate-700">Tablolar</span>
                </a>
                <a href="pages/buttons.html"
                    class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-soft">
                    <i class="ri-cursor-line text-xl text-slate-600"></i>
                    <span class="font-medium text-slate-700">Butonlar</span>
                </a>
            </div>
        </div>
    </div>
@endsection
