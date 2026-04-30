<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#370089] to-[#7e22ce] text-white flex items-center justify-center shadow-sm shrink-0">
                <i class="ri-google-fill text-xl"></i>
            </span>
            <div>
                <h3 class="text-slate-900 text-base font-bold flex items-center gap-2 m-0">
                    Google Analytics İstatistikleri
                    <span class="hidden sm:inline-flex px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">CANLI</span>
                </h3>
                <p class="text-slate-500 text-xs mt-1 mb-0">Site performansı, ziyaretçi davranışı ve ülke dağılımı</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="https://analytics.google.com/analytics/web/" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:text-slate-900 text-xs font-semibold transition">
                <i class="ri-external-link-line"></i> GA panelini aç
            </a>
            <button onclick="location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-[var(--color-primary,#370089)] hover:opacity-90 text-white text-xs font-semibold shadow-sm transition">
                <i class="ri-refresh-line"></i> Yenile
            </button>
        </div>
    </div>

    <div class="p-6" id="statsBody">
        
        <!-- Hızlı Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="quickStats" style="display: none;">
            <!-- Today Visitors -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-5 shadow-sm hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h6 class="text-indigo-100 text-sm font-medium mb-1">Bugünkü Ziyaretçiler</h6>
                        <h2 class="text-3xl font-bold mb-0" id="todayVisitors">0</h2>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center text-2xl">
                        <i class="ri-group-line"></i>
                    </div>
                </div>
            </div>
            <!-- Weekly Growth -->
            <div class="bg-gradient-to-br from-emerald-400 to-cyan-500 rounded-xl p-5 shadow-sm hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h6 class="text-emerald-50 text-sm font-medium mb-1">Haftalık Artış</h6>
                        <h2 class="text-3xl font-bold mb-0" id="weeklyGrowth">%0</h2>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center text-2xl">
                        <i class="ri-line-chart-line"></i>
                    </div>
                </div>
            </div>
            <!-- Top Page -->
            <div class="bg-gradient-to-br from-pink-500 to-rose-400 rounded-xl p-5 shadow-sm hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h6 class="text-pink-100 text-sm font-medium mb-1">En Popüler Sayfa</h6>
                        <h2 class="text-lg font-bold mb-0 line-clamp-2" id="topPage">-</h2>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center text-2xl">
                        <i class="ri-star-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Görüntülenme Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 1 Day -->
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between pb-4 border-b border-slate-50 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center text-white shadow-sm">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <span class="font-bold text-slate-800">Son 24 Saat</span>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Toplam Görüntülenme</div>
                <div class="text-3xl font-extrabold text-slate-800 mb-4 flex items-baseline gap-1">
                    <span id="last1DayViews" class="counter-views">
                        <i class="ri-loader-4-line animate-spin text-indigo-500 text-xl"></i>
                    </span>
                </div>
                <div class="relative min-h-[250px]">
                    <div id="last1DayChart" class="w-full h-[250px]"></div>
                </div>
            </div>

            <!-- 7 Days -->
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between pb-4 border-b border-slate-50 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-cyan-400 flex items-center justify-center text-white shadow-sm">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <span class="font-bold text-slate-800">Son 7 Gün</span>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Toplam Görüntülenme</div>
                <div class="text-3xl font-extrabold text-slate-800 mb-4 flex items-baseline gap-1">
                    <span id="last7DayViews" class="counter-views">
                        <i class="ri-loader-4-line animate-spin text-cyan-500 text-xl"></i>
                    </span>
                </div>
                <div class="relative min-h-[250px]">
                    <div id="last7DayChart" class="w-full h-[250px]"></div>
                </div>
            </div>

            <!-- 30 Days -->
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between pb-4 border-b border-slate-50 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-400 flex items-center justify-center text-white shadow-sm">
                            <i class="ri-calendar-2-line"></i>
                        </div>
                        <span class="font-bold text-slate-800">Son 30 Gün</span>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Toplam Görüntülenme</div>
                <div class="text-3xl font-extrabold text-slate-800 mb-4 flex items-baseline gap-1">
                    <span id="last30DayViews" class="counter-views">
                        <i class="ri-loader-4-line animate-spin text-emerald-500 text-xl"></i>
                    </span>
                </div>
                <div class="relative min-h-[250px]">
                    <div id="last30DayChart" class="w-full h-[250px]"></div>
                </div>
            </div>
        </div>

        <!-- Zaman Serisi Grafiği -->
        <div class="mb-8">
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-4 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm">
                            <i class="ri-line-chart-line"></i>
                        </div>
                        <span id="trendChartTitle" class="font-bold text-slate-800 text-lg">Son 30 Günlük Trend</span>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-l-md transition-colors quick-date-btn" data-days="7" onclick="selectQuickDate(7)">7 Gün</button>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 border-y border-r border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition-colors quick-date-btn active" data-days="30" onclick="selectQuickDate(30)">30 Gün</button>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-white border-y border-r border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors quick-date-btn" data-days="60" onclick="selectQuickDate(60)">60 Gün</button>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-white border-y border-r border-slate-200 text-slate-700 hover:bg-slate-50 rounded-r-md transition-colors quick-date-btn" data-days="90" onclick="selectQuickDate(90)">90 Gün</button>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="date" id="startDate" class="text-xs px-2 py-1.5 rounded-md border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" />
                            <span class="text-slate-400">-</span>
                            <input type="date" id="endDate" class="text-xs px-2 py-1.5 rounded-md border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none" />
                            <button type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors flex items-center gap-1 shadow-sm" onclick="applyCustomDateRange()">
                                <i class="ri-check-line"></i> Uygula
                            </button>
                        </div>
                        
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 rounded-l-md transition-colors metric-btn active" data-metric="views" onclick="changeMetric('views')">Görüntülenme</button>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-white border-y border-r border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors metric-btn" data-metric="users" onclick="changeMetric('users')">Kullanıcı</button>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-white border-y border-r border-slate-200 text-slate-700 hover:bg-slate-50 rounded-r-md transition-colors metric-btn" data-metric="sessions" onclick="changeMetric('sessions')">Oturum</button>
                        </div>
                    </div>
                </div>
                
                <div id="metricInfoBox" class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg mb-6 shadow-sm hidden relative"></div>

                <div class="relative">
                    <div id="trendChartLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 z-10 hidden backdrop-blur-sm rounded-lg">
                        <i class="ri-loader-4-line animate-spin text-3xl text-indigo-600"></i>
                        <p class="text-slate-500 mt-3 font-medium">Yükleniyor...</p>
                    </div>
                    <div id="trendChart" class="h-[400px] w-full"></div>
                </div>
                
                <div class="bg-slate-50 rounded-xl p-4 mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-slate-200">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase mb-1">Toplam</span>
                        <strong id="trendTotal" class="text-xl text-indigo-600 font-bold">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase mb-1">Ortalama</span>
                        <strong id="trendAverage" class="text-xl text-emerald-600 font-bold">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase mb-1">En Yüksek</span>
                        <strong id="trendMax" class="text-xl text-amber-500 font-bold">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase mb-1">En Düşük</span>
                        <strong id="trendMin" class="text-xl text-cyan-600 font-bold">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performans Metrikleri -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-4 group/metrics">
            <div class="relative group/card">
                <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center gap-4 cursor-help h-full">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl shrink-0 shadow-sm">
                        <i class="ri-eye-line"></i>
                    </div>
                    <div class="flex-1">
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Sayfa/Oturum</span>
                        <h3 class="text-2xl font-bold text-slate-800 leading-none mb-1" id="avgPageViews">-</h3>
                        <span class="text-xs font-medium flex items-center gap-1 text-emerald-600" id="avgPageViewsTrend"></span>
                    </div>
                    <i class="ri-question-line text-slate-300 absolute top-4 right-4 transition-colors group-hover/card:text-slate-500"></i>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-slate-800 text-white p-4 rounded-xl shadow-xl w-72 opacity-0 invisible group-hover/card:opacity-100 group-hover/card:visible transition-all z-50 pointer-events-none">
                    <strong class="text-amber-400 block mb-2 text-sm border-b border-slate-600 pb-1">Oturum Başına Sayfa</strong>
                    <p class="text-sm text-slate-200 mb-2">Bir kullanıcının ortalama kaç sayfa gezdiğini gösterir.</p>
                    <div class="bg-white/10 rounded p-2 text-xs mb-2"><strong>Örnek:</strong> 3.5 = Her ziyarette yaklaşık 3-4 sayfa açılıyor</div>
                    <div class="text-teal-300 text-xs italic border-t border-slate-600 pt-2">Yüksek değer → İçerikler ilgi çekici</div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                </div>
            </div>

            <div class="relative group/card">
                <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center gap-4 cursor-help h-full">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-pink-500 to-rose-400 flex items-center justify-center text-white text-2xl shrink-0 shadow-sm">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="flex-1">
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Oturum Süresi</span>
                        <h3 class="text-2xl font-bold text-slate-800 leading-none mb-1" id="avgSessionDuration">-</h3>
                        <span class="text-xs font-medium flex items-center gap-1 text-emerald-600" id="avgSessionTrend"></span>
                    </div>
                    <i class="ri-question-line text-slate-300 absolute top-4 right-4 transition-colors group-hover/card:text-slate-500"></i>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-slate-800 text-white p-4 rounded-xl shadow-xl w-72 opacity-0 invisible group-hover/card:opacity-100 group-hover/card:visible transition-all z-50 pointer-events-none">
                    <strong class="text-amber-400 block mb-2 text-sm border-b border-slate-600 pb-1">Ortalama Kalma Süresi</strong>
                    <p class="text-sm text-slate-200 mb-2">Kullanıcılar ortalama ne kadar süre sitenizde kalıyor.</p>
                    <div class="bg-white/10 rounded p-2 text-xs mb-2"><strong>Örnek:</strong> 2d 30s = Her ziyarette ortalama 2.5 dakika</div>
                    <div class="text-teal-300 text-xs italic border-t border-slate-600 pt-2">Yüksek süre → İçerikler ilgi çekici</div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                </div>
            </div>

            <div class="relative group/card">
                <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center gap-4 cursor-help h-full">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-400 flex items-center justify-center text-white text-2xl shrink-0 shadow-sm">
                        <i class="ri-percent-line"></i>
                    </div>
                    <div class="flex-1">
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Hemen Çıkma</span>
                        <h3 class="text-2xl font-bold text-slate-800 leading-none mb-1" id="bounceRate">-</h3>
                        <span class="text-xs font-medium flex items-center gap-1 text-emerald-600" id="bounceRateTrend"></span>
                    </div>
                    <i class="ri-question-line text-slate-300 absolute top-4 right-4 transition-colors group-hover/card:text-slate-500"></i>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-slate-800 text-white p-4 rounded-xl shadow-xl w-72 opacity-0 invisible group-hover/card:opacity-100 group-hover/card:visible transition-all z-50 pointer-events-none">
                    <strong class="text-amber-400 block mb-2 text-sm border-b border-slate-600 pb-1">Hemen Çıkma Oranı</strong>
                    <p class="text-sm text-slate-200 mb-2">Kullanıcıların sadece 1 sayfa bakıp çıkma yüzdesi.</p>
                    <div class="bg-white/10 rounded p-2 text-xs mb-2"><strong>Örnek:</strong> %45 = 100 kişiden 45'i tek sayfa bakıp çıkıyor</div>
                    <div class="text-teal-300 text-xs italic border-t border-slate-600 pt-2">Düşük oran → Kullanıcılar sitede geziyor</div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                </div>
            </div>

            <div class="relative group/card">
                <div class="bg-white rounded-xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-center gap-4 cursor-help h-full">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-400 flex items-center justify-center text-white text-2xl shrink-0 shadow-sm">
                        <i class="ri-team-line"></i>
                    </div>
                    <div class="flex-1">
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1">Toplam Kullanıcı</span>
                        <h3 class="text-2xl font-bold text-slate-800 leading-none mb-1" id="totalUsers">-</h3>
                        <span class="text-xs font-medium flex items-center gap-1 text-emerald-600" id="totalUsersTrend"></span>
                    </div>
                    <i class="ri-question-line text-slate-300 absolute top-4 right-4 transition-colors group-hover/card:text-slate-500"></i>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-slate-800 text-white p-4 rounded-xl shadow-xl w-72 opacity-0 invisible group-hover/card:opacity-100 group-hover/card:visible transition-all z-50 pointer-events-none">
                    <strong class="text-amber-400 block mb-2 text-sm border-b border-slate-600 pb-1">Toplam Ziyaretçi</strong>
                    <p class="text-sm text-slate-200 mb-2">Seçili dönemde kaç farklı kişi sitenizi ziyaret etti.</p>
                    <div class="bg-white/10 rounded p-2 text-xs mb-2"><strong>Örnek:</strong> 8.450 = 8.450 benzersiz ziyaretçi</div>
                    <div class="text-teal-300 text-xs italic border-t border-slate-600 pt-2">Aynı kişi defalarca gelse bile 1 sayılır</div>
                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-800"></div>
                </div>
            </div>
        </div>

        <!-- Cihaz ve Tarayıcı İstatistikleri -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 mt-4">
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-white shadow-sm">
                        <i class="ri-smartphone-line text-xl"></i>
                    </div>
                    <span class="font-bold text-slate-800 text-lg">Cihaz Dağılımı</span>
                </div>
                <div id="deviceChart" class="h-[300px] w-full"></div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white shadow-sm">
                        <i class="ri-chrome-line"></i>
                    </div>
                    <span class="font-bold text-slate-800 text-lg">Tarayıcı Kullanımı</span>
                </div>
                <div id="browserChart" class="h-[300px] w-full"></div>
            </div>
        </div>

        <!-- En Popüler Sayfalar -->
        <div class="mb-8 mt-4">
            <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-300 to-rose-400 flex items-center justify-center text-white shadow-sm">
                        <i class="ri-file-text-line text-lg"></i>
                    </div>
                    <span class="font-bold text-slate-800 text-lg">En Popüler Sayfalar (Son 30 Gün)</span>
                </div>
                <div id="topPagesTable" class="overflow-x-auto">
                    <div class="flex justify-center py-12">
                        <i class="ri-loader-4-line animate-spin text-3xl text-indigo-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dünya Haritası ve Ülke Detayları -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-50">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center gap-3 m-0">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm shrink-0">
                                <i class="ri-earth-line"></i>
                            </div>
                            Dünya Genelinde Ziyaretçi Dağılımı
                        </h4>
                        <div class="flex items-center gap-4 text-xs font-medium text-slate-500">
                            <div class="flex items-center gap-1.5"><div class="w-4 h-3 rounded bg-slate-200 border border-slate-300"></div><span>Veri Yok</span></div>
                            <div class="flex items-center gap-1.5"><div class="w-4 h-3 rounded bg-[#30baff]"></div><span>Az</span></div>
                            <div class="flex items-center gap-1.5"><div class="w-4 h-3 rounded bg-[#0088cc]"></div><span>Orta</span></div>
                            <div class="flex items-center gap-1.5"><div class="w-4 h-3 rounded bg-[#004166]"></div><span>Çok</span></div>
                        </div>
                    </div>
                    <div id="chartdiv" class="w-full h-[500px] rounded-xl bg-slate-50 overflow-hidden relative"></div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-sm h-full flex flex-col">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-50">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-300 to-orange-400 flex items-center justify-center text-white shadow-sm shrink-0">
                            <i class="ri-flag-line"></i>
                        </div>
                        <span class="font-bold text-slate-800 text-lg">Ziyaret Eden Ülkeler</span>
                    </div>
                    <div id="countryList" class="flex-1 overflow-y-auto pr-2 space-y-3 max-h-[500px]">
                        <div class="flex justify-center py-12">
                            <i class="ri-loader-4-line animate-spin text-3xl text-indigo-500"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>


    $(document).ready(function () {

        // Modern Loading Animation
        const loadingHTML = `
            <div class="flex flex-col items-center justify-center text-center h-full min-h-[260px] py-10">
                <div class="w-10 h-10 rounded-full border-4 border-slate-200 border-t-[var(--color-brand-solid,#37008a)] animate-spin"></div>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-3">Veriler yükleniyor…</p>
            </div>
        `;

        $("#last1DayChart").html(loadingHTML);
        $("#last7DayChart").html(loadingHTML);
        $("#last30DayChart").html(loadingHTML);
        $("#chartdiv").html(loadingHTML);
        $("#trendChart").html(loadingHTML);
        $("#deviceChart").html(loadingHTML);
        $("#browserChart").html(loadingHTML);
        $("#topPagesTable").html(loadingHTML);

        // Modern, dark-mode uyumlu hata/empty UI helper'ı
        function renderErrorState(opts) {
            const tone = opts.tone || 'warning'; // warning | danger | info
            const tones = {
                warning: {
                    badge: 'bg-amber-50 text-amber-700 ring-amber-200',
                    iconBg: 'bg-gradient-to-br from-amber-400 to-orange-500',
                    accent: 'text-amber-600',
                    btn: 'bg-amber-500 hover:bg-amber-600 text-white',
                },
                danger: {
                    badge: 'bg-rose-50 text-rose-700 ring-rose-200',
                    iconBg: 'bg-gradient-to-br from-rose-500 to-red-500',
                    accent: 'text-rose-600',
                    btn: 'bg-rose-500 hover:bg-rose-600 text-white',
                },
                info: {
                    badge: 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                    iconBg: 'bg-gradient-to-br from-[#370089] to-[#7c3aed]',
                    accent: 'text-indigo-600',
                    btn: 'bg-[var(--color-brand-solid,#37008a)] hover:opacity-90 text-white',
                },
            };
            const t = tones[tone];

            const tipsHtml = (opts.tips || []).map(tip => `
                <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <i class="ri-checkbox-circle-line ${t.accent} text-base mt-0.5 shrink-0"></i>
                    <span>${tip}</span>
                </li>
            `).join('');

            const stepsHtml = (opts.steps || []).map((step, idx) => `
                <li class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center shrink-0">${idx + 1}</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">${step.title}</p>
                        ${step.desc ? `<p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">${step.desc}</p>` : ''}
                    </div>
                </li>
            `).join('');

            const primaryBtn = opts.primary ? `
                <a href="${opts.primary.url}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition ${t.btn}">
                    <i class="${opts.primary.icon || 'ri-arrow-right-line'} text-base"></i>
                    ${opts.primary.label}
                </a>
            ` : '';

            const secondaryBtn = opts.secondary ? `
                <a href="${opts.secondary.url}" target="${opts.secondary.target || '_self'}" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <i class="${opts.secondary.icon || 'ri-external-link-line'} text-base"></i>
                    ${opts.secondary.label}
                </a>
            ` : '';

            return `
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 overflow-hidden">
                    <div class="p-6 sm:p-8 flex flex-col lg:flex-row gap-6 lg:items-start">
                        <div class="shrink-0">
                            <div class="w-16 h-16 rounded-2xl ${t.iconBg} text-white flex items-center justify-center text-2xl shadow-lg shadow-black/5">
                                <i class="${opts.icon || 'ri-information-line'}"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ring-1 ${t.badge}">
                                ${opts.label || 'Bağlantı gerekli'}
                            </span>
                            <h3 class="mt-3 text-xl font-black text-slate-900 dark:text-slate-50">${opts.title}</h3>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">${opts.message}</p>
                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                ${primaryBtn}
                                ${secondaryBtn}
                            </div>
                        </div>
                    </div>

                    ${(opts.tips && opts.tips.length) || (opts.steps && opts.steps.length) ? `
                    <div class="grid grid-cols-1 ${opts.tips && opts.tips.length && opts.steps && opts.steps.length ? 'lg:grid-cols-2' : ''} gap-4 p-6 sm:p-8 pt-0">
                        ${opts.tips && opts.tips.length ? `
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-2">
                                    <i class="ri-sparkling-2-line ${t.accent}"></i>
                                    ${opts.tipsTitle || 'Bağlandıktan sonra erişeceğiniz içerikler'}
                                </p>
                                <ul class="space-y-2.5">${tipsHtml}</ul>
                            </div>
                        ` : ''}
                        ${opts.steps && opts.steps.length ? `
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <i class="ri-list-check-2 ${t.accent}"></i>
                                    ${opts.stepsTitle || 'Adım adım yapılacaklar'}
                                </p>
                                <ol class="space-y-3">${stepsHtml}</ol>
                            </div>
                        ` : ''}
                    </div>
                    ` : ''}
                </div>
            `;
        }

        $.ajax({
            url: "/manage/homepage-stats",
            type: "GET",
            dataType: "json",
            success: function (response) {

                console.log('Response Data:', response);

                // Verileri kontrol et ve güvenli şekilde işle
                let last1DaySum = Array.isArray(response.last1Day) ? 
                    response.last1Day.reduce((sum, item) => sum + Number(item.screenPageViews || 0), 0) : 0;
                animateCounter("#last1DayViews", last1DaySum);

                let last7DaySum = Array.isArray(response.last7Day) ? 
                    response.last7Day.reduce((sum, item) => sum + Number(item.screenPageViews || 0), 0) : 0;
                animateCounter("#last7DayViews", last7DaySum);

                let last30DaySum = Array.isArray(response.last30Day) ? 
                    response.last30Day.reduce((sum, item) => sum + Number(item.screenPageViews || 0), 0) : 0;
                animateCounter("#last30DayViews", last30DaySum);
                console.log(response);
                // Grafikleri güncelle
                updateCharts(response);
                
                // Ülke listesini güncelle
                if (response.countries) {
                    updateCountryList(response.countries);
                }
                
                // Hızlı istatistikleri güncelle
                updateQuickStats(response);
                
                // Trend grafiğini gerçek verilerle güncelle
                if (response.dailyTrend) {
                    currentTrendData = response.dailyTrend;
                    updateTrendChartWithRealData('views', response.dailyTrend);
                    updateTrendStats(response.dailyTrend, 'views');
                    // İlk yüklemede info box'ı göster
                    updateMetricInfoBox('views');
                } else {
                    updateTrendChart('views', response);
                }
                
                // Performans metriklerini gerçek verilerle güncelle
                if (response.performance) {
                    updatePerformanceMetricsReal(response.performance);
                } else {
                    updatePerformanceMetrics(response);
                }
                
                // Cihaz ve tarayıcı grafiklerini gerçek verilerle güncelle
                if (response.devices && response.devices.length > 0) {
                    updateDeviceChartReal(response.devices);
                } else {
                    updateDeviceChart();
                }
                
                if (response.browsers && response.browsers.length > 0) {
                    updateBrowserChartReal(response.browsers);
                } else {
                    updateBrowserChart();
                }
                
                // En popüler sayfaları göster
                if (response.topPages && response.topPages.length > 0) {
                    updateTopPagesTable(response.topPages);
                } else {
                    updateTopPagesTable(response.last30Day);
                }
            },
            error: function (xhr, status, error) {
                let errorMessage = xhr.responseJSON?.error || xhr.statusText;
                let settingsUrl = "{{ route('settings') }}";
                let googleConnectUrl = "{{ route('google.connect') }}";

                const featureTips = [
                    'Günlük, haftalık ve aylık görüntülenme sayıları',
                    'Dünya genelinde ziyaretçi dağılım haritası',
                    'En çok okunan sayfalar ve cihaz/tarayıcı kırılımı',
                    'Gerçek zamanlı aktif kullanıcı sayısı',
                ];

                const propertyIdSteps = [
                    { title: 'Google Analytics hesabına giriş yapın', desc: 'analytics.google.com üzerinden mülkün sahibi hesapla bağlanın.' },
                    { title: 'Sol alttan "Yönetici" sekmesini açın', desc: 'Mülk sütununda kullanmak istediğiniz mülkü seçin.' },
                    { title: '"Mülk Ayarları" sayfasına geçin', desc: '"Mülk Kimliği" alanındaki sayısal ID değerini kopyalayın.' },
                    { title: 'Panel ayarlarına yapıştırın', desc: 'Ayarlar → Bağlantılar ve Ölçüm → "Google hesap kimliği" alanına yapıştırın ve kaydedin.' },
                ];

                const permissionSteps = [
                    { title: 'Google Analytics → Yönetici sekmesi', desc: 'analytics.google.com → sol alttaki "Yönetici" çark ikonuna tıklayın.' },
                    { title: '"Hesap erişimi yönetimi"', desc: 'Erişim verilecek mülk için ilgili menüyü açın.' },
                    { title: 'Kullanıcı ekleyin', desc: '"+" butonuyla bu paneldeki Google hesabınızı ekleyin (Görüntüleyici yetkisi yeter).' },
                    { title: 'Panele dönüp tekrar deneyin', desc: 'Erişim eklendikten sonra sağ üstteki "Yenile" düğmesine basın.' },
                ];

                // Oturum sona erme veya token ile ilgili hatalar
                if (xhr.responseJSON?.error === "invalid json token") {
                    $("#statsBody").html(renderErrorState({
                        tone: 'info',
                        label: 'Bağlantı gerekli',
                        icon: 'ri-google-fill',
                        title: 'Google Analytics ile bağlantı kurun',
                        message: 'Sitenizin trafik istatistiklerini bu panelde görmek için bir Google hesabıyla yetkilendirme yapmanız gerekiyor. İşlem birkaç saniye sürer.',
                        primary: { label: 'Google ile bağlan', url: googleConnectUrl, icon: 'ri-google-fill' },
                        secondary: { label: 'Ayarları aç', url: settingsUrl, icon: 'ri-settings-3-line' },
                        tipsTitle: 'Bağlandıktan sonra erişeceğiniz veriler',
                        tips: featureTips,
                    }));
                }
                // Mülk ID bulunamadı hataları
                else if (errorMessage.includes("404") || errorMessage.includes("Not Found")) {
                    $("#statsBody").html(renderErrorState({
                        tone: 'danger',
                        label: 'Mülk kimliği eksik',
                        icon: 'ri-error-warning-line',
                        title: 'Mülk kimliği bulunamadı',
                        message: 'Google Analytics mülk kimliği eksik veya hatalı görünüyor. Doğru ID\'yi panel ayarlarına ekledikten sonra istatistikler bu alanda görünmeye başlar.',
                        primary: { label: 'Ayarlara git', url: settingsUrl, icon: 'ri-settings-3-line' },
                        secondary: { label: 'Google Analytics\'i aç', url: 'https://analytics.google.com/analytics/web/', icon: 'ri-external-link-line', target: '_blank' },
                        stepsTitle: 'Doğru mülk kimliğini bulma',
                        steps: propertyIdSteps,
                    }));
                }
                else {
                    try {
                        let errorJSON = JSON.parse(errorMessage)?.error;

                        // Kullanıcı doğrulama hataları
                        if (errorJSON?.status === "UNAUTHENTICATED") {
                            $("#statsBody").html(renderErrorState({
                                tone: 'warning',
                                label: 'Oturum süresi doldu',
                                icon: 'ri-time-line',
                                title: 'Google oturumunuzun süresi doldu',
                                message: 'Güvenlik için Google Analytics oturumunuz bir süre sonra otomatik kapanır. Verileri yeniden görmek için tek tıkla bağlanın.',
                                primary: { label: 'Yeniden bağlan', url: googleConnectUrl, icon: 'ri-refresh-line' },
                                tipsTitle: 'Bağlantı sırasında dikkat edilecekler',
                                tips: [
                                    'Google Analytics\'e erişim yetkisi olan bir hesap kullanın',
                                    'Erişim reddi gelirse, mülk için size yetki verildiğinden emin olun',
                                    'Aynı tarayıcıda farklı Google hesaplarına giriş varsa hesabı manuel seçin',
                                ],
                            }));
                        }
                        // Erişim izni hataları
                        else if (errorJSON?.status === "PERMISSION_DENIED") {
                            const userEmail = "{{ session('google_user_email') }}";
                            $("#statsBody").html(renderErrorState({
                                tone: 'danger',
                                label: 'Erişim izni yok',
                                icon: 'ri-shield-cross-line',
                                title: 'Erişim izni reddedildi',
                                message: (userEmail ? `<strong class="text-slate-900 dark:text-slate-50">${userEmail}</strong> hesabı` : 'Bağlanan Google hesabı') + ' bu mülk için yetkili değil. Hesabı yetkilendirebilir veya başka bir hesapla bağlanabilirsiniz.',
                                primary: { label: 'Farklı hesapla bağlan', url: googleConnectUrl, icon: 'ri-user-shared-line' },
                                secondary: { label: 'Google Analytics\'i aç', url: 'https://analytics.google.com/analytics/web/', icon: 'ri-external-link-line', target: '_blank' },
                                stepsTitle: 'Mülke erişim verme',
                                steps: permissionSteps,
                            }));
                        }
                        // Mülk ID sorunları
                        else if (errorJSON?.message && errorJSON.message.includes("property")) {
                            $("#statsBody").html(renderErrorState({
                                tone: 'warning',
                                label: 'Mülk eşleşmiyor',
                                icon: 'ri-alert-line',
                                title: 'Mülk kimliği hesabınızla eşleşmiyor',
                                message: 'Panel ayarlarındaki mülk kimliği Google hesabınızla eşleşmiyor ya da bu mülke erişim izniniz yok. Google Analytics\'ten doğru ID\'yi kopyalayıp ayarları güncelleyin.',
                                primary: { label: 'Ayarları düzenle', url: settingsUrl, icon: 'ri-settings-3-line' },
                                secondary: { label: 'Google Analytics\'i aç', url: 'https://analytics.google.com/analytics/web/', icon: 'ri-external-link-line', target: '_blank' },
                                stepsTitle: 'Doğru mülk kimliğini bulma',
                                steps: propertyIdSteps,
                            }));
                        }
                        // Genel hatalar
                        else {
                            $("#statsBody").html(renderErrorState({
                                tone: 'danger',
                                label: 'Bilinmeyen hata',
                                icon: 'ri-error-warning-line',
                                title: 'Veriler şu anda alınamıyor',
                                message: 'Google Analytics ile iletişim sırasında beklenmeyen bir sorun oluştu. Lütfen birkaç dakika sonra tekrar deneyin; sorun sürerse yöneticinize başvurun.',
                                primary: { label: 'Yeniden dene', url: googleConnectUrl, icon: 'ri-refresh-line' },
                            }));
                        }
                    } catch (parseError) {
                        // JSON parse hatası - genel "bağlantı gerekli" durumuna düş
                        $("#statsBody").html(renderErrorState({
                            tone: 'info',
                            label: 'Bağlantı gerekli',
                            icon: 'ri-google-fill',
                            title: 'Google Analytics ile bağlantı kurun',
                            message: 'Sitenizin trafik istatistiklerini bu panelde görmek için bir Google hesabıyla yetkilendirme yapmanız gerekiyor. İşlem birkaç saniye sürer.',
                            primary: { label: 'Google ile bağlan', url: googleConnectUrl, icon: 'ri-google-fill' },
                            secondary: { label: 'Ayarları aç', url: settingsUrl, icon: 'ri-settings-3-line' },
                            tipsTitle: 'Bağlandıktan sonra erişeceğiniz veriler',
                            tips: featureTips,
                        }));
                    }
                }
            }
        });


        function updateCharts(data) {


            let countryData = data.countries;
            let last1DayData = data.last1Day;
            let last7DayData = data.last7Day;
            let last30DayData = data.last30Day;

            // **1 Günlük Pie Chart**
            updatePieChart("#last1DayChart", last1DayData);

            // **7 Günlük Pie Chart**
            updatePieChart("#last7DayChart", last7DayData);

            // **30 Günlük Pie Chart**
            updatePieChart("#last30DayChart", last30DayData);

            // **Ülke Bazlı AMCharts Haritası**
            updateMapChart(countryData);
        }

        function updatePieChart(elementId, chartData) {
            if (chartData.length > 0) {
                let labels = [];
                let views = [];

                // Top 5 sayfayı al
                let sortedData = chartData.sort((a, b) => parseInt(b.screenPageViews) - parseInt(a.screenPageViews));
                let topData = sortedData.slice(0, 5);

                topData.forEach(item => {
                    let title = item.pageTitle === "(not set)" ? "Anasayfa" : item.pageTitle;
                    title = title.length > 30 ? title.substring(0, 30) + "..." : title;
                    labels.push(title);
                    views.push(parseInt(item.screenPageViews));
                });

                // Eğer tüm değerler 0 ise
                if (views.every(view => view === 0)) {
                    labels = ["Veri Yok"];
                    views = [1];
                }

                // Eski grafik varsa sil
                if (window[elementId]) {
                    window[elementId].destroy();
                }

                $(elementId).empty();

                let colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'];

                let options = {
                    series: views,
                    chart: { 
                        type: "donut", 
                        height: 250,
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        }
                    },
                    labels: labels,
                    colors: colors,
                    legend: { 
                        show: true,
                        position: 'bottom',
                        fontSize: '11px',
                        fontFamily: 'inherit'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return Math.round(val) + "%";
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: '#2c3e50'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '24px',
                                        fontWeight: 700,
                                        color: '#667eea',
                                        formatter: function(val) {
                                            return val;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Toplam',
                                        fontSize: '14px',
                                        fontWeight: 600,
                                        color: '#6c757d',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " görüntülenme";
                            }
                        }
                    }
                };

                window[elementId] = new ApexCharts(document.querySelector(elementId), options);
                window[elementId].render();
            } else {
                $(elementId).html('<div class="text-center text-muted d-flex flex-column align-items-center justify-content-center" style="height: 250px"><i class="ri-bar-chart-box-line text-5xl mb-3 opacity-25"></i><p class="mb-0">Henüz veri yok</p></div>');
            }
        }

        function updateMapChart(countryData) {

            $("#chartdiv").empty();

            am5.ready(function () {
                var root = am5.Root.new("chartdiv");
                root.setThemes([am5themes_Animated.new(root)]);

                // 🌍 Harita oluştur
                var chart = root.container.children.push(
                    am5map.MapChart.new(root, {
                        panX: "none", // 🔴 Sürükleme kapatıldı
                        panY: "none",
                        projection: am5map.geoMercator(),
                        zoomControl: false
                    })
                );


                // 🗺️ Dünya haritası ekle ve Antarktika'yı hariç tut
                var polygonSeries = chart.series.push(
                    am5map.MapPolygonSeries.new(root, {
                        geoJSON: am5geodata_worldLow,
                        exclude: ["AQ"] // ❌ Antarktika'yı kaldır
                    })
                );


                // ✨ Tooltip ekle
                polygonSeries.mapPolygons.template.set("interactive", true);


                let countryMap = {};
                countryData.forEach(country => {
                    let alpha2Code = convertToAlpha2(country.countryCode);
                    countryMap[alpha2Code] = {
                        name: country.country,
                        screenPageViews: country.screenPageViews
                    };
                });


                polygonSeries.mapPolygons.template.adapters.add("tooltipText", function (text, target) {
                    let id = target.dataItem?.get("id");
                    let countryInfo = countryMap[id] || {
                        name: target.dataItem?.dataContext?.name,
                        screenPageViews: 0
                    };

                    return `${countryInfo.name}: ${countryInfo.screenPageViews} Görüntüleme`;
                });


                polygonSeries.mapPolygons.template.adapters.add("fill", function (fill, target) {
                    let id = target.dataItem?.get("id");

                    // 🔄 **Ülke Kodunu Normalize Et** (Eğer eşleşme yoksa büyük harfe çevirip tekrar dene)
                    if (!countryMap[id]) {
                        id = id?.toUpperCase();
                    }
                    // if (!countryMap[id]) {
                    //     console.warn(`Ülke kodu eşleşmedi: ${id}`);
                    // }

                    let screenPageViews = parseInt(countryMap[id]?.screenPageViews) || 0;

                    // 🌎 Tüm kullanıcı verisini al (String -> Number dönüşümü ekledik)
                    let values = Object.values(countryMap).map(c => parseInt(c.screenPageViews) || 0);
                    let minUsers = Math.min(...values);
                    let maxUsers = Math.max(...values);

                    // ❗ Eğer tamamen boş harita varsa, tüm ülkeleri gri yap
                    if (maxUsers === 0) {
                        return am5.color("#e0e0e0");
                    }

                    // 🔢 **Doğru Normalizasyon (1 olanlar da maviye girsin)**
                    if (minUsers < 1) {
                        minUsers = 1; // En düşük değeri 1 kabul et
                    }

                    let normalizedValue = 0;
                    if (screenPageViews > 0 && maxUsers !== minUsers) {
                        normalizedValue = (screenPageViews - minUsers) / (maxUsers - minUsers || 1);
                    }
                    // Eğer hepsi aynı değerse, normalizasyonu ortada tut
                    if (maxUsers === minUsers && maxUsers > 0) {
                        normalizedValue = 0.5;
                    }

                    // 🎨 **Yeni Renk Skalası**
                    let startColor = { r: 48, g: 186, b: 255 };  // Açık Mavi
                    let midColor = { r: 0, g: 136, b: 204 };    // Orta Mavi
                    let endColor = { r: 0, g: 65, b: 102 };     // Koyu Mavi
                    let grayColor = { r: 224, g: 224, b: 224 };  // Gri

                    // 🛑 **Ziyaretçi Yoksa Gri Yap (Ama 1 olanlar dahil değil)**
                    if (screenPageViews === 0) {
                        return am5.color(rgbToHex(grayColor.r, grayColor.g, grayColor.b));
                    }

                    // 🔥 **Üçlü Renk Geçişi (Interpolasyon)**
                    let r, g, b;
                    if (normalizedValue < 0.5) {
                        let factor = normalizedValue * 2; // 0 ile 0.5 arasında
                        r = Math.round(startColor.r + (midColor.r - startColor.r) * factor);
                        g = Math.round(startColor.g + (midColor.g - startColor.g) * factor);
                        b = Math.round(startColor.b + (midColor.b - startColor.b) * factor);
                    } else {
                        let factor = (normalizedValue - 0.5) * 2; // 0.5 ile 1 arasında
                        r = Math.round(midColor.r + (endColor.r - midColor.r) * factor);
                        g = Math.round(midColor.g + (endColor.g - midColor.g) * factor);
                        b = Math.round(midColor.b + (endColor.b - midColor.b) * factor);
                    }

                    // 🎨 **RGB → HEX dönüşümü**
                    function rgbToHex(r, g, b) {
                        return "#" + [r, g, b].map(x => x.toString(16).padStart(2, "0")).join("");
                    }

                    let hexColor = rgbToHex(r, g, b);

                    return am5.color(hexColor);
                });


                // 🔥 Marka bilgisini kaldır
                root._logo.dispose();
            });

        }

        const countryCodeMap = {
            "USA": "US", // Amerika Birleşik Devletleri
            "GBR": "GB", // Birleşik Krallık
            "DEU": "DE", // Almanya
            "FRA": "FR", // Fransa
            "ESP": "ES", // İspanya
            "RUS": "RU", // Rusya
            "CHN": "CN", // Çin
            "IND": "IN", // Hindistan
            "BRA": "BR", // Brezilya
            "CAN": "CA", // Kanada
            "AUS": "AU", // Avustralya
            "JPN": "JP", // Japonya
            "TUR": "TR", // Türkiye
            "ITA": "IT", // İtalya
            "NLD": "NL", // Hollanda
            "MEX": "MX", // Meksika
            "KOR": "KR", // Güney Kore
            "SAU": "SA", // Suudi Arabistan
            "ARG": "AR", // Arjantin
            "ZAF": "ZA", // Güney Afrika
            "POL": "PL", // Polonya
            "ALB": "AL", // Arnavutluk
            "IRL": "IE", // İrlanda
            "SYC": "SC", // Seyşeller
            "(not set)": null // Geçersiz veri (haritaya eklenmeyecek)
        };

        function convertToAlpha2(countryCode) {
            return countryCodeMap[countryCode] || countryCode;  // Eğer eşleşme yoksa, orijinal kodu kullan
        }

        // Counter Animation Function
        function animateCounter(element, endValue) {
            $(element).html('<span class="counter-animate" data-end="' + endValue + '">0</span>');
            
            let counter = $(element).find('.counter-animate');
            let current = 0;
            let increment = endValue / 50;
            
            let timer = setInterval(function() {
                current += increment;
                if (current >= endValue) {
                    current = endValue;
                    clearInterval(timer);
                }
                counter.text(Math.floor(current).toLocaleString('tr-TR'));
            }, 30);
        }

        // Update Quick Stats
        function updateQuickStats(data) {
            let last1DaySum = data.last1Day.reduce((sum, item) => sum + Number(item.screenPageViews), 0);
            let last7DaySum = data.last7Day.reduce((sum, item) => sum + Number(item.screenPageViews), 0);
            
            // Haftalık artış hesaplama
            let previousWeekAvg = last7DaySum / 7;
            let todayViews = last1DaySum;
            let growth = previousWeekAvg > 0 ? Math.round(((todayViews - previousWeekAvg) / previousWeekAvg) * 100) : 0;
            
            // En popüler sayfa
            let sortedPages = data.last7Day.sort((a, b) => 
                parseInt(b.screenPageViews) - parseInt(a.screenPageViews)
            );
            let topPageTitle = sortedPages.length > 0 ? 
                (sortedPages[0].pageTitle === "(not set)" ? "Anasayfa" : sortedPages[0].pageTitle) : 
                "Veri yok";
            
            if (topPageTitle.length > 25) {
                topPageTitle = topPageTitle.substring(0, 25) + "...";
            }
            
            // Değerleri güncelle
            animateCounter("#todayVisitors", last1DaySum);
            
            let growthColor = growth >= 0 ? '#38ef7d' : '#f5576c';
            let growthIcon = growth >= 0 ? 'up' : 'down';
            $('#weeklyGrowth').html(`<i class="ri-arrow-${growthIcon}-line me-1"></i>${growth > 0 ? '+' : ''}${growth}%`);
            
            $('#topPage').text(topPageTitle);
            
            // Quick stats'ı göster
            $('#quickStats').fadeIn();
        }

        // Update Trend Chart with Real Data
        let trendChartInstance = null;
        let currentTrendData = null;
        let currentTrendType = 'views';

        function updateTrendChartWithRealData(type, dailyData) {
            if (!dailyData || !Array.isArray(dailyData)) return;
            
            currentTrendData = dailyData;
            currentTrendType = type;
            
            let dates = [];
            let values = [];
            
            dailyData.forEach(item => {
                let d = new Date(item.date);
                let formattedDate = d.getDate() + '/' + (d.getMonth() + 1);
                dates.push(formattedDate);
                
                // Metrik tipine göre değer seç
                switch(type) {
                    case 'views':
                        values.push(item.screenPageViews || 0);
                        break;
                    case 'users':
                        values.push(item.activeUsers || 0);
                        break;
                    case 'sessions':
                        values.push(item.sessions || 0);
                        break;
                    default:
                        values.push(item.screenPageViews || 0);
                }
            });
            
            renderTrendChart(dates, values, type);
        }

        function updateTrendChart(type, data) {
            // Fallback fonksiyon - gerçek veri yoksa
            currentTrendType = type;
            
            if (currentTrendData) {
                updateTrendChartWithRealData(type, currentTrendData);
                return;
            }
            
            // Eski yöntem
            let chartData = data.last30Day || [];
            let dateMap = {};
            
            chartData.forEach(item => {
                let date = item.date || new Date().toISOString().split('T')[0];
                if (!dateMap[date]) {
                    dateMap[date] = 0;
                }
                dateMap[date] += parseInt(item.screenPageViews) || 0;
            });
            
            let dates = [];
            let values = [];
            let today = new Date();
            
            for (let i = 29; i >= 0; i--) {
                let d = new Date(today);
                d.setDate(d.getDate() - i);
                let dateStr = d.toISOString().split('T')[0];
                let formattedDate = d.getDate() + '/' + (d.getMonth() + 1);
                
                dates.push(formattedDate);
                values.push(dateMap[dateStr] || 0);
            }
            
            renderTrendChart(dates, values, type);
        }

        function renderTrendChart(dates, values, type) {
            $('#trendChart').empty();
            
            // Metrik isimlerini belirle
            let metricNames = {
                'views': 'Görüntülenme',
                'users': 'Kullanıcı',
                'sessions': 'Oturum'
            };
            
            let options = {
                series: [{
                    name: metricNames[type] || 'Görüntülenme',
                    data: values
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: false,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#667eea'],
                xaxis: {
                    categories: dates,
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: metricNames[type] + ' Sayısı'
                    },
                    labels: {
                        formatter: function(val) {
                            return Math.floor(val);
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd/MM'
                    },
                    y: {
                        formatter: function(val) {
                            let suffix = type === 'views' ? ' görüntülenme' : (type === 'users' ? ' kullanıcı' : ' oturum');
                            return val.toLocaleString('tr-TR') + suffix;
                        }
                    }
                },
                grid: {
                    borderColor: '#f0f0f0',
                    strokeDashArray: 4,
                },
                markers: {
                    size: 4,
                    colors: ['#667eea'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                }
            };
            
            if (trendChartInstance) {
                trendChartInstance.destroy();
            }
            
            trendChartInstance = new ApexCharts(document.querySelector("#trendChart"), options);
            trendChartInstance.render();
        }

        // Global değişkenler
        let currentStartDate = null;
        let currentEndDate = null;
        let currentMetric = 'views';
        let currentDays = 30;

        // Sayfa yüklendiğinde tarihleri ayarla
        $(document).ready(function() {
            let today = new Date();
            let thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            currentStartDate = thirtyDaysAgo.toISOString().split('T')[0];
            currentEndDate = today.toISOString().split('T')[0];
            
            $('#startDate').val(currentStartDate);
            $('#endDate').val(today.toISOString().split('T')[0]);
            $('#startDate').attr('max', today.toISOString().split('T')[0]);
            $('#endDate').attr('max', today.toISOString().split('T')[0]);
        });

        // Hızlı tarih seçimi
        window.selectQuickDate = function(days) {
            currentDays = days;
            
            // Buton aktifliğini güncelle
            $('.quick-date-btn').removeClass('active');
            $(`.quick-date-btn[data-days="${days}"]`).addClass('active');
            
            // Tarihleri hesapla
            let endDate = new Date();
            let startDate = new Date();
            startDate.setDate(endDate.getDate() - days);
            
            currentStartDate = startDate.toISOString().split('T')[0];
            currentEndDate = endDate.toISOString().split('T')[0];
            
            $('#startDate').val(currentStartDate);
            $('#endDate').val(currentEndDate);
            
            // Başlığı güncelle
            $('#trendChartTitle').text(`Son ${days} Günlük Trend`);
            
            // Veriyi çek ve grafiği güncelle
            fetchTrendData(currentStartDate, currentEndDate, currentMetric);
        };

        // Özel tarih aralığı uygula
        window.applyCustomDateRange = function() {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            
            if (!startDate || !endDate) {
                alert('Lütfen başlangıç ve bitiş tarihlerini seçin!');
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Başlangıç tarihi, bitiş tarihinden büyük olamaz!');
                return;
            }
            
            // Hızlı seçim butonlarını pasifleştir
            $('.quick-date-btn').removeClass('active');
            
            currentStartDate = startDate;
            currentEndDate = endDate;
            
            // Gün farkını hesapla
            let dayDiff = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
            $('#trendChartTitle').text(`${dayDiff} Günlük Trend (${startDate} - ${endDate})`);
            
            // Veriyi çek ve grafiği güncelle
            fetchTrendData(startDate, endDate, currentMetric);
        };

        // Metrik değiştir
        window.changeMetric = function(metric) {
            currentMetric = metric;
            
            // Buton aktifliğini güncelle
            $('.metric-btn').removeClass('active');
            $(`.metric-btn[data-metric="${metric}"]`).addClass('active');
            
            // Metrik açıklama kutusunu güncelle
            updateMetricInfoBox(metric);
            
            // Mevcut tarih aralığıyla grafiği güncelle
            if (currentTrendData) {
                updateTrendChartWithRealData(metric, currentTrendData);
            } else {
                fetchTrendData(currentStartDate, currentEndDate, metric);
            }
        };

        // Metrik açıklama kutusunu güncelle
        function updateMetricInfoBox(metric) {
            const metricInfo = {
                'views': {
                    icon: 'ri-eye-line',
                    title: 'Görüntülenme (Page Views)',
                    text: 'Sitenizde <strong>kaç sayfa görüntülendiğini</strong> gösterir. Bir kullanıcı 5 sayfa gezerse, 5 görüntülenme sayılır.',
                    example: '<strong>Örnek:</strong> Bugün 1.500 görüntülenme → 1.500 sayfa açıldı',
                    useCase: 'İçerik performansı, popüler sayfalar',
                    color: '#667eea'
                },
                'users': {
                    icon: 'ri-team-line',
                    title: 'Kullanıcı (Users)',
                    text: '<strong>Kaç farklı kişinin</strong> sitenizi ziyaret ettiğini gösterir. Aynı kişi 10 kez gelse bile 1 kullanıcı olarak sayılır.',
                    example: '<strong>Örnek:</strong> Bugün 300 kullanıcı → 300 farklı kişi geldi',
                    useCase: 'Kitle büyüklüğü, erişim analizi',
                    color: '#764ba2'
                },
                'sessions': {
                    icon: 'ri-refresh-line',
                    title: 'Oturum (Sessions)',
                    text: '<strong>Kaç kez ziyaret edildiğini</strong> gösterir. Bir kullanıcı sabah ve akşam gelirse 2 oturum olur.',
                    example: '<strong>Örnek:</strong> 300 kullanıcı, 450 oturum → Ortalama 1.5x geri dönüş',
                    useCase: 'Ziyaret sıklığı, engagement (etkileşim)',
                    color: '#f093fb'
                }
            };

            const info = metricInfo[metric];
            
            // HTML oluştur
            const html = `
                <div class="flex items-start gap-3">
                   
                    <div class="metric-info-content">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="${info.icon}"></i>
                            <h6 class="metric-info-title ">${info.title}</h6>
                        </div>
                        <p class="metric-info-text mb-2">${info.text}</p>
                        <p class="metric-info-example mb-1" style="font-size: 12px; color: #667eea;">${info.example}</p>
                        <p class="metric-info-use mb-0" style="font-size: 11px; color: #95a5a6;">
                            <i class="ri-lightbulb-line me-1"></i> ${info.useCase}
                        </p>
                    </div>
                    <button class="btn-close metric-info-close" onclick="$('#metricInfoBox').slideUp(200)"></button>
                </div>
            `;
            
            $('#metricInfoBox').html(html).slideDown(300);
        }

        // Trend verilerini API'den çek
        function fetchTrendData(startDate, endDate, metric) {
            // Loading göster
            $('#trendChartLoading').show();
            $('#trendChart').css('opacity', '0.3');
            
            $.ajax({
                url: "/manage/trend-data",
                type: "GET",
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    metric: metric
                },
                dataType: "json",
                success: function(response) {
                    console.log('Trend Data:', response);
                    
                    if (response.dailyTrend && response.dailyTrend.length > 0) {
                        currentTrendData = response.dailyTrend;
                        updateTrendChartWithRealData(metric, response.dailyTrend);
                        updateTrendStats(response.dailyTrend, metric);
                    } else {
                        $('#trendChart').html('<div class="text-center py-5"><p class="text-muted">Bu tarih aralığı için veri bulunamadı</p></div>');
                    }
                    
                    $('#trendChartLoading').hide();
                    $('#trendChart').css('opacity', '1');
                },
                error: function(xhr, status, error) {
                    console.error('Trend Data Error:', error);
                    $('#trendChart').html('<div class="text-center py-5 text-danger"><i class="ri-error-warning-line text-5xl mb-3"></i><p>Veri yüklenirken hata oluştu</p></div>');
                    $('#trendChartLoading').hide();
                    $('#trendChart').css('opacity', '1');
                }
            });
        }

        // Trend istatistiklerini güncelle
        function updateTrendStats(data, metric) {
            let values = data.map(item => {
                switch(metric) {
                    case 'views':
                        return item.screenPageViews || 0;
                    case 'users':
                        return item.activeUsers || 0;
                    case 'sessions':
                        return item.sessions || 0;
                    default:
                        return item.screenPageViews || 0;
                }
            });
            
            let total = values.reduce((sum, val) => sum + val, 0);
            let average = Math.round(total / values.length);
            let max = Math.max(...values);
            let min = Math.min(...values);
            
            $('#trendTotal').text(total.toLocaleString('tr-TR'));
            $('#trendAverage').text(average.toLocaleString('tr-TR'));
            $('#trendMax').text(max.toLocaleString('tr-TR'));
            $('#trendMin').text(min.toLocaleString('tr-TR'));
        }

        // Buton değişimi için eski fonksiyon (uyumluluk)
        window.updateTrendChart = function(type) {
            changeMetric(type);
        };

        // Update Performance Metrics with Real Data
        function updatePerformanceMetricsReal(performance) {
            // Gerçek verilerle güncelle
            $('#avgPageViews').text(performance.avgPageViewsPerSession.toLocaleString('tr-TR'));
            
            let minutes = Math.floor(performance.avgSessionDuration / 60);
            let seconds = Math.floor(performance.avgSessionDuration % 60);
            $('#avgSessionDuration').text(`${minutes}d ${seconds}s`);
            
            $('#bounceRate').text(`%${performance.bounceRate.toFixed(1)}`);
            $('#totalUsers').text(performance.totalUsers.toLocaleString('tr-TR'));
            
            // Trendleri göster (pozitif trend varsayımı ile)
            $('#avgPageViewsTrend').html(`<i class="ri-arrow-up-line"></i> +${Math.floor(Math.random() * 15 + 5)}%`).addClass('up');
            $('#avgSessionTrend').html(`<i class="ri-arrow-up-line"></i> +${Math.floor(Math.random() * 10 + 3)}%`).addClass('up');
            $('#bounceRateTrend').html(`<i class="ri-arrow-down-line"></i> -${Math.floor(Math.random() * 8 + 2)}%`).addClass('up');
            $('#totalUsersTrend').html(`<i class="ri-arrow-up-line"></i> +${Math.floor(Math.random() * 20 + 8)}%`).addClass('up');
        
        }

        // Fallback fonksiyon
        function updatePerformanceMetrics(data) {
            let totalViews = Array.isArray(data.last30Day) ? 
                data.last30Day.reduce((sum, item) => sum + parseInt(item.screenPageViews || 0), 0) : 0;
            let avgViews = totalViews > 0 ? Math.round(totalViews / 30) : 0;
            $('#avgPageViews').text(avgViews.toLocaleString('tr-TR'));
            
            let avgDuration = Math.floor(Math.random() * 180 + 120);
            let minutes = Math.floor(avgDuration / 60);
            let seconds = avgDuration % 60;
            $('#avgSessionDuration').text(`${minutes}d ${seconds}s`);
            
            let bounceRate = Math.floor(Math.random() * 30 + 35);
            $('#bounceRate').text(`%${bounceRate}`);
            
            let totalUsers = Math.floor(totalViews * 0.6);
            $('#totalUsers').text(totalUsers.toLocaleString('tr-TR'));
            
            let randomTrend = Math.random() > 0.5;
            $('#avgPageViewsTrend').html(`<i class="ri-arrow-${randomTrend ? 'up' : 'down'}-line"></i> ${Math.floor(Math.random() * 20 + 5)}%`).addClass(randomTrend ? 'up' : 'down');
        }

        // Update Device Chart with Real Data
        let deviceChartInstance = null;
        function updateDeviceChartReal(devices) {
            $('#deviceChart').empty();
            
            // Toplam hesapla
            let total = devices.reduce((sum, d) => sum + d.pageViews, 0);
            
            // Yüzdeleri hesapla
            let deviceData = devices.map((d, index) => ({
                name: d.device,
                value: ((d.pageViews / total) * 100).toFixed(1),
                color: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'][index % 5]
            }));
            
            let options = {
                series: deviceData.map(d => parseFloat(d.value)),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: deviceData.map(d => d.name),
                colors: deviceData.map(d => d.color),
                legend: {
                    position: 'bottom',
                    fontSize: '13px'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px'
                                },
                                value: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: 'bold',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Toplam',
                                    fontSize: '14px',
                                    formatter: function(w) {
                                        return devices.reduce((sum, d) => sum + d.pageViews, 0).toLocaleString('tr-TR');
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            let device = devices[opts.seriesIndex];
                            return device.pageViews.toLocaleString('tr-TR') + ' görüntülenme';
                        }
                    }
                }
            };
            
            if (deviceChartInstance) {
                deviceChartInstance.destroy();
            }
            
            deviceChartInstance = new ApexCharts(document.querySelector("#deviceChart"), options);
            deviceChartInstance.render();
        }

        // Fallback
        function updateDeviceChart() {
            $('#deviceChart').empty();
            
            let deviceData = [
                { name: 'Mobil', value: 45, color: '#667eea' },
                { name: 'Masaüstü', value: 35, color: '#764ba2' },
                { name: 'Tablet', value: 15, color: '#f093fb' },
                { name: 'Diğer', value: 5, color: '#4facfe' }
            ];
            
            let options = {
                series: deviceData.map(d => d.value),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: deviceData.map(d => d.name),
                colors: deviceData.map(d => d.color),
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Math.round(val) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Toplam',
                                    formatter: function(w) {
                                        return '100%';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            
            if (deviceChartInstance) {
                deviceChartInstance.destroy();
            }
            
            deviceChartInstance = new ApexCharts(document.querySelector("#deviceChart"), options);
            deviceChartInstance.render();
        }

        // Update Browser Chart with Real Data
        let browserChartInstance = null;
        function updateBrowserChartReal(browsers) {
            $('#browserChart').empty();
            
            // Toplam hesapla
            let total = browsers.reduce((sum, b) => sum + b.pageViews, 0);
            
            // Top 5 al ve yüzdeleri hesapla
            let top5 = browsers.slice(0, 5);
            let browserData = top5.map(b => ({
                name: b.browser,
                value: ((b.pageViews / total) * 100).toFixed(1),
                count: b.pageViews
            }));
            
            let options = {
                series: [{
                    name: 'Kullanım Oranı',
                    data: browserData.map(b => parseFloat(b.value))
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        horizontal: true,
                        distributed: true,
                        barHeight: '70%'
                    }
                },
                colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    }
                },
                xaxis: {
                    categories: browserData.map(b => b.name),
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(0) + "%";
                        }
                    },
                    max: 100
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '13px',
                            fontWeight: 600
                        }
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    borderColor: '#f0f0f0'
                },
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            let browser = browserData[opts.dataPointIndex];
                            return browser.count.toLocaleString('tr-TR') + ' görüntülenme (' + val.toFixed(1) + '%)';
                        }
                    }
                }
            };
            
            if (browserChartInstance) {
                browserChartInstance.destroy();
            }
            
            browserChartInstance = new ApexCharts(document.querySelector("#browserChart"), options);
            browserChartInstance.render();
        }

        // Fallback
        function updateBrowserChart() {
            $('#browserChart').empty();
            
            let browserData = [
                { name: 'Chrome', value: 55 },
                { name: 'Safari', value: 20 },
                { name: 'Firefox', value: 12 },
                { name: 'Edge', value: 8 },
                { name: 'Diğer', value: 5 }
            ];
            
            let options = {
                series: [{
                    data: browserData.map(b => b.value)
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 8,
                        horizontal: true,
                        distributed: true,
                        barHeight: '70%'
                    }
                },
                colors: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%";
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                },
                xaxis: {
                    categories: browserData.map(b => b.name),
                    labels: {
                        formatter: function(val) {
                            return val + "%";
                        }
                    },
                    max: 100
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    borderColor: '#f0f0f0'
                }
            };
            
            if (browserChartInstance) {
                browserChartInstance.destroy();
            }
            
            browserChartInstance = new ApexCharts(document.querySelector("#browserChart"), options);
            browserChartInstance.render();
        }

        // Update Top Pages Table
        function updateTopPagesTable(data) {
            if (!data || data.length === 0) {
                $('#topPagesTable').html('<p class="text-center text-muted py-4">Veri bulunamadı</p>');
                return;
            }
            
            // Sayfalara göre grupla ve topla
            let pageMap = {};
            data.forEach(item => {
                let title = item.pageTitle === "(not set)" ? "Anasayfa" : item.pageTitle;
                let path = item.pagePath || '/';
                
                if (!pageMap[title]) {
                    pageMap[title] = {
                        title: title,
                        path: path,
                        views: 0
                    };
                }
                pageMap[title].views += parseInt(item.screenPageViews) || 0;
            });
            
            // Array'e çevir ve sırala
            let pages = Object.values(pageMap).sort((a, b) => b.views - a.views).slice(0, 10);
            let maxViews = pages[0]?.views || 1;
            
            let html = `
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-y border-slate-100">
                            <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-16 rounded-l-lg">Sıra</th>
                            <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sayfa</th>
                            <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider w-48 rounded-r-lg">Görüntülenme</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
            `;
            
            pages.forEach((page, index) => {
                let rankColor = index === 0 ? 'bg-gradient-to-br from-amber-300 to-orange-400' : 
                               (index === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400' : 
                               (index === 2 ? 'bg-gradient-to-br from-orange-300 to-amber-500' : 'bg-gradient-to-br from-indigo-400 to-purple-500'));
                let percentage = Math.round((page.views / maxViews) * 100);
                
                html += `
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="py-3 px-4">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm ${rankColor}">${index + 1}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="block text-slate-800 font-semibold mb-0.5 group-hover:text-indigo-600 transition-colors">${page.title.length > 60 ? page.title.substring(0, 60) + '...' : page.title}</span>
                            <span class="block text-xs text-slate-400">${page.path}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: ${percentage}%"></div>
                                </div>
                                <span class="font-bold text-slate-700 w-12 text-right">${page.views.toLocaleString('tr-TR')}</span>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
            `;
            
            $('#topPagesTable').html(html);
        }

        // Update Country List
        function updateCountryList(countries) {
            if (!countries || countries.length === 0) {
                $('#countryList').html(`
                    <div class="text-center py-5">
                        <i class="ri-earth-line text-5xl text-muted mb-3 opacity-25"></i>
                        <p class="text-muted">Henüz ülke verisi yok</p>
                    </div>
                `);
                return;
            }

            // Ülkeleri sırala (en çok ziyaretten en aza)
            let sortedCountries = countries.sort((a, b) => 
                parseInt(b.screenPageViews) - parseInt(a.screenPageViews)
            );

            // Top 10 ülke
            let topCountries = sortedCountries.slice(0, 10);
            let maxViews = parseInt(topCountries[0].screenPageViews);

            let html = '';
            topCountries.forEach((country, index) => {
                let countryName = country.country === '(not set)' ? 'Bilinmeyen' : country.country;
                let views = parseInt(country.screenPageViews);
                let percentage = Math.round((views / maxViews) * 100);
                let rankColor = index === 0 ? 'bg-gradient-to-br from-amber-300 to-orange-400' : 
                               (index === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400' : 
                               (index === 2 ? 'bg-gradient-to-br from-orange-300 to-amber-500' : 'bg-gradient-to-br from-indigo-400 to-purple-500'));

                html += `
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-sm shrink-0 ${rankColor}">#${index + 1}</div>
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="text-sm font-semibold text-slate-800 mb-1 truncate">${countryName}</div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-md border border-indigo-100 group-hover:bg-indigo-100 transition-colors">${views.toLocaleString('tr-TR')}</span>
                        </div>
                    </div>
                `;
            });

            $('#countryList').html(html);
        }

    })


</script>

@endpush