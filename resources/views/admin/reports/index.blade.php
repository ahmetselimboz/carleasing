@extends('admin.layout')

@section('content')
    <div class="fade-in space-y-6">
        @php
            $timelineLabels = $timeline->pluck('bucket')->values();
            $timelineTotals = $timeline->pluck('total')->map(fn ($value) => (int) $value)->values();
            $timelineRental = $timeline->pluck('rental')->map(fn ($value) => (int) $value)->values();
            $timelineMessage = $timeline->pluck('message')->map(fn ($value) => (int) $value)->values();
            $timelineCallback = $timeline->pluck('callback')->map(fn ($value) => (int) $value)->values();
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Raporlar</h2>
                <p class="text-slate-500 text-sm mt-1">Dönemsel raporlar, trendler ve dışa aktarma işlemleri.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.export', ['format' => 'excel'] + request()->query()) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100">
                    Excel
                </a>
                <a href="{{ route('reports.export', ['format' => 'csv'] + request()->query()) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-sky-50 text-sky-700 text-sm font-semibold hover:bg-sky-100">
                    CSV
                </a>
                <a href="{{ route('reports.export', ['format' => 'pdf'] + request()->query()) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100">
                    PDF
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.index') }}"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Rapor filtresi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Dönem, tarih ve zaman dilimine göre verileri anında güncelle.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium rounded-full px-2.5 py-1 bg-brand-solid/10 text-brand w-fit">
                        <i class="ri-calendar-2-line"></i>
                        {{ $filters['range_human'] }}
                    </span>
                </div>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">
                <div class="xl:col-span-3">
                    <label class="text-xs font-medium text-slate-600">Dönem</label>
                    <div class="relative mt-1">
                        <select name="period"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-700 shadow-sm transition-soft focus:border-brand focus:outline-none  focus:ring-brand-solid/15">
                            <option value="today" @selected($filters['period'] === 'today')>Bugün</option>
                            <option value="last_7_days" @selected($filters['period'] === 'last_7_days')>Son 7 gün</option>
                            <option value="last_30_days" @selected($filters['period'] === 'last_30_days')>Son 30 gün</option>
                            <option value="this_month" @selected($filters['period'] === 'this_month')>Bu ay</option>
                            <option value="this_year" @selected($filters['period'] === 'this_year')>Bu yıl</option>
                            <option value="custom" @selected($filters['period'] === 'custom')>Özel aralık</option>
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    </div>
                </div>
                <div class="xl:col-span-2">
                    <label class="text-xs font-medium text-slate-600">Başlangıç</label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                        class="mt-1 w-full appearance-none rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm text-slate-700 shadow-sm transition-soft focus:border-brand focus:outline-none  focus:ring-brand-solid/15">
                </div>
                <div class="xl:col-span-2">
                    <label class="text-xs font-medium text-slate-600">Bitiş</label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                        class="mt-1 w-full appearance-none rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm text-slate-700 shadow-sm transition-soft focus:border-brand focus:outline-none  focus:ring-brand-solid/15">
                </div>
                <div class="xl:col-span-2">
                    <label class="text-xs font-medium text-slate-600">Zaman dilimi</label>
                    <div class="relative mt-1">
                        <select name="group_by"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-white py-2.5 pl-3 pr-10 text-sm text-slate-700 shadow-sm transition-soft focus:border-brand focus:outline-none  focus:ring-brand-solid/15">
                            <option value="day" @selected($filters['group_by'] === 'day')>Günlük</option>
                            <option value="week" @selected($filters['group_by'] === 'week')>Haftalık</option>
                            <option value="month" @selected($filters['group_by'] === 'month')>Aylık</option>
                        </select>
                        <i class="ri-arrow-down-s-line pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    </div>
                </div>
                <div class="xl:col-span-3 flex items-end gap-2">
                    <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-solid text-white text-sm font-semibold hover:brightness-95 transition-soft">
                        <i class="ri-filter-3-line text-base"></i>
                        Raporu uygula
                    </button>
                    <a href="{{ route('reports.index') }}"
                        class="inline-flex justify-center items-center px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-soft">
                        <i class="ri-refresh-line text-base"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 p-4">
                <p class="text-xs text-slate-500">Toplam talep</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($kpis['total_leads'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $filters['range_human'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4">
                <p class="text-xs text-slate-500">Kiralama talepleri</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($kpis['rental_requests'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">Okunmayan: {{ number_format($kpis['unread_rental'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4">
                <p class="text-xs text-slate-500">Mesajlar + geri arama</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($kpis['messages'] + $kpis['callback_requests'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">Okunmayan: {{ number_format($kpis['unread_messages'] + $kpis['unread_callbacks'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4">
                <p class="text-xs text-slate-500">Aktif araç</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ number_format($kpis['active_cars'], 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">Anlık filo görünümü</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Zaman bazlı talep dağılımı</h3>
                </div>

                <div class="px-4 py-4 border-b border-slate-100">
                    <div class="h-64 rounded-xl bg-slate-50/80 p-2">
                        <canvas id="reportTimelineChart"
                            data-labels='@json($timelineLabels)'
                            data-total='@json($timelineTotals)'
                            data-rental='@json($timelineRental)'
                            data-message='@json($timelineMessage)'
                            data-callback='@json($timelineCallback)'></canvas>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Grafik; toplam talep, kiralama, mesaj ve geri arama kırılımlarını aynı zaman ekseninde gösterir.</p>
                </div>

                <div class="overflow-x-auto max-h-[24rem]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 font-medium">Dönem</th>
                                <th class="px-4 py-3 font-medium">Toplam</th>
                                <th class="px-4 py-3 font-medium">Kiralama</th>
                                <th class="px-4 py-3 font-medium">Mesaj</th>
                                <th class="px-4 py-3 font-medium">Geri arama</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($timeline as $row)
                                <tr>
                                    <td class="px-4 py-2.5">{{ $row['bucket'] }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-800">{{ number_format($row['total'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5">{{ number_format($row['rental'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5">{{ number_format($row['message'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5">{{ number_format($row['callback'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Şehir dağılımı</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($cityBreakdown as $row)
                        @php
                            $cityPercentRaw = $kpis['total_leads'] > 0 ? (int) round(($row['count'] / $kpis['total_leads']) * 100) : 0;
                            $cityPercent = $cityPercentRaw > 0 ? max(2, min(100, $cityPercentRaw)) : 0;
                        @endphp
                        <div class="px-4 py-3 space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">{{ $row['city'] }}</span>
                                <span class="font-semibold text-slate-800">{{ number_format($row['count'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-brand-solid rounded-full transition-all duration-300"
                                    style="width: {{ $cityPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-sm text-slate-500">Bu aralıkta şehir verisi yok.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Mesaj kategorileri</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($messageCategoryBreakdown as $row)
                        @php
                            $categoryPercent = $kpis['messages'] > 0 ? min(100, (int) round(($row['count'] / $kpis['messages']) * 100)) : 0;
                        @endphp
                        <div class="px-4 py-3 space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600">{{ $row['label'] }}</span>
                                <span class="font-semibold text-slate-800">{{ number_format($row['count'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $categoryPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-sm text-slate-500">Bu aralıkta kategori verisi yok.</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800">Araç talep dağılımı</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($demandBreakdown as $row)
                        @php
                            $demandPercent = $kpis['total_leads'] > 0 ? min(100, (int) round(($row['count'] / $kpis['total_leads']) * 100)) : 0;
                        @endphp
                        <div class="px-4 py-3 space-y-1.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 line-clamp-1">{{ $row['car'] }}</span>
                                <span class="font-semibold text-slate-800">{{ number_format($row['count'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $demandPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-sm text-slate-500">Bu aralıkta araç talebi yok.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Son talepler</h3>
                <span class="text-xs text-slate-500">{{ $filters['range_human'] }}</span>
            </div>
            <div class="overflow-x-auto max-h-[28rem]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tip</th>
                            <th class="px-4 py-3 font-medium">Tarih</th>
                            <th class="px-4 py-3 font-medium">Ad soyad</th>
                            <th class="px-4 py-3 font-medium">İletişim</th>
                            <th class="px-4 py-3 font-medium">Şehir</th>
                            <th class="px-4 py-3 font-medium">Detay</th>
                            <th class="px-4 py-3 font-medium">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($leadRows->take(80) as $row)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-2.5">{{ $row['type'] }}</td>
                                <td class="px-4 py-2.5 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('d.m.Y H:i') }}</td>
                                <td class="px-4 py-2.5">{{ $row['name'] }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="text-slate-700">{{ $row['phone'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $row['email'] }}</div>
                                </td>
                                <td class="px-4 py-2.5">{{ $row['city'] }}</td>
                                <td class="px-4 py-2.5 text-slate-600">{{ $row['detail'] }}</td>
                                <td class="px-4 py-2.5">
                                    @if ($row['read_status'] === 'Yeni')
                                        <span class="inline-flex px-2 py-1 rounded-md text-xs font-medium bg-rose-50 text-rose-600">Yeni</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600">Okundu</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">Bu aralıkta kayıt bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const canvas = document.getElementById('reportTimelineChart');
            if (!canvas) return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const total = JSON.parse(canvas.dataset.total || '[]');
            const rental = JSON.parse(canvas.dataset.rental || '[]');
            const message = JSON.parse(canvas.dataset.message || '[]');
            const callback = JSON.parse(canvas.dataset.callback || '[]');

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Toplam',
                            data: total,
                            borderColor: '#37008a',
                            backgroundColor: 'rgba(55, 0, 138, .12)',
                            borderWidth: 2.5,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 2
                        },
                        {
                            label: 'Kiralama',
                            data: rental,
                            borderColor: '#0f766e',
                            backgroundColor: 'transparent',
                            borderWidth: 1.8,
                            tension: 0.35,
                            pointRadius: 1.5
                        },
                        {
                            label: 'Mesaj',
                            data: message,
                            borderColor: '#0284c7',
                            backgroundColor: 'transparent',
                            borderWidth: 1.8,
                            tension: 0.35,
                            pointRadius: 1.5
                        },
                        {
                            label: 'Geri arama',
                            data: callback,
                            borderColor: '#ea580c',
                            backgroundColor: 'transparent',
                            borderWidth: 1.8,
                            tension: 0.35,
                            pointRadius: 1.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.22)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
