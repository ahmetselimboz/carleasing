@extends('admin.layout')

@php
    use Illuminate\Support\Carbon;

    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 6 => 'İyi geceler',
        $hour < 12 => 'Günaydın',
        $hour < 18 => 'İyi günler',
        default => 'İyi akşamlar',
    };

    $userName = trim((string) ($currentUser->name ?? ''));
    $userFirst = $userName !== '' ? explode(' ', $userName)[0] : 'Yönetici';

    $sparkValues = $sparkline->pluck('total')->all();
    $sparkMax = max(1, max($sparkValues ?: [1]));
    $sparkSum = array_sum($sparkValues);
    $sparkPeak = max($sparkValues ?: [0]);

    $kpis = [
        [
            'label' => 'Bekleyen talepler',
            'value' => $totalPending,
            'sub' => 'Okunmamış kuyruğu',
            'icon' => 'ri-inbox-unarchive-line',
            'tone' => 'from-rose-500 to-pink-500',
            'badgeText' => $totalPending > 0 ? 'Aksiyon bekliyor' : 'Sıfır kuyruk',
            'badgeTone' => $totalPending > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600',
        ],
        [
            'label' => 'Son 30 gün lead',
            'value' => $totalLeadsLast30,
            'sub' => 'Talep + mesaj + geri arama',
            'icon' => 'ri-bar-chart-grouped-line',
            'tone' => 'from-indigo-500 to-violet-600',
            'badgeText' => ($leadsDelta >= 0 ? '+' : '').$leadsDelta.'%',
            'badgeTone' => $leadsDelta >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600',
        ],
        [
            'label' => 'Aktif filo aracı',
            'value' => $fleetCounts['active'],
            'sub' => $fleetCounts['featured'].' tanesi anasayfada',
            'icon' => 'ri-roadster-line',
            'tone' => 'from-emerald-500 to-teal-500',
            'badgeText' => $fleetCounts['total'].' toplam',
            'badgeTone' => 'bg-slate-100 text-slate-700',
        ],
        [
            'label' => 'Bugün gelen',
            'value' => $rentalCounts['today'] + $messageCounts['today'] + $callbackCounts['today'],
            'sub' => 'Son 24 saat içinde',
            'icon' => 'ri-flashlight-line',
            'tone' => 'from-amber-500 to-orange-500',
            'badgeText' => 'Bugün',
            'badgeTone' => 'bg-amber-50 text-amber-700',
        ],
    ];
@endphp

@section('content')
    {{-- Greeting hero --}}
    <div class="fade-in relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-[var(--color-primary,#370089)] via-[#4a0bb0] to-[#270063] text-white shadow-sm">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
             style="background-image: radial-gradient(circle at 12% 20%, rgba(255,255,255,0.25), transparent 45%), radial-gradient(circle at 88% 80%, rgba(255,255,255,0.18), transparent 40%);"></div>
        <div class="relative p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-white/70 text-xs uppercase tracking-[0.2em] mb-2">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                <h2 class="text-2xl lg:text-3xl font-black tracking-tight">{{ $greeting }}, {{ $userFirst }} <span class="inline-block">👋</span></h2>
                <p class="text-white/80 text-sm mt-2 max-w-xl">
                    Sitenizin son 30 günde topladığı leadler, içerik durumu ve günlük trafik özeti aşağıda.
                    @if ($totalPending > 0)
                        <span class="font-semibold text-amber-200">{{ $totalPending }} talebiniz okunmayı bekliyor.</span>
                    @else
                        <span class="font-semibold text-emerald-200">Tüm talepler okundu, harika!</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                <a href="{{ route('cars.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-brand-solid text-white px-4 py-2.5 text-sm font-semibold shadow-sm hover:bg-brand-solid-hover transition">
                    <i class="ri-add-line text-lg"></i> Yeni araç
                </a>
                <a href="{{ route('rental-requests.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white/15 border border-white/25 text-white px-4 py-2.5 text-sm font-semibold hover:bg-white/25 transition backdrop-blur">
                    <i class="ri-inbox-line text-lg"></i> Talepleri görüntüle
                </a>
                <form action="{{ route('cache.clear') }}" method="post" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/10 border border-white/20 text-white px-4 py-2.5 text-sm font-semibold hover:bg-white/20 transition">
                        <i class="ri-refresh-line text-lg"></i> Önbelleği temizle
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($kpis as $kpi)
            <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-5 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">
                <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-gradient-to-br {{ $kpi['tone'] }} opacity-10"></div>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $kpi['label'] }}</p>
                        <p class="text-3xl font-black text-slate-900 mt-2 leading-none">{{ number_format($kpi['value'], 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500 mt-2">{{ $kpi['sub'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $kpi['tone'] }} text-white flex items-center justify-center text-xl shadow-sm shrink-0">
                        <i class="{{ $kpi['icon'] }}"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $kpi['badgeTone'] }}">
                        {{ $kpi['badgeText'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 14-day trend + lead breakdown --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Sparkline trend --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="ri-line-chart-line text-[var(--color-primary,#370089)]"></i>
                        Son 14 gün lead trendi
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Kiralama talepleri, mesajlar ve geri arama talepleri toplamı</p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-right">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400">Toplam</p>
                        <p class="text-base font-black text-slate-800">{{ $sparkSum }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400">Tepe</p>
                        <p class="text-base font-black text-indigo-600">{{ $sparkPeak }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400">Ort/gün</p>
                        <p class="text-base font-black text-emerald-600">{{ $sparkSum > 0 ? round($sparkSum / max(1, $sparkline->count()), 1) : 0 }}</p>
                    </div>
                </div>
            </div>

            @php
                // SVG line chart koordinatları
                $svgW = 700;
                $svgH = 180;
                $padX = 18;
                $padY = 22;
                $count = max(1, $sparkline->count());
                $stepX = $count > 1 ? ($svgW - 2 * $padX) / ($count - 1) : 0;
                $usableH = $svgH - 2 * $padY;
                $todayStr = now()->toDateString();

                $points = [];
                $rentalPoints = [];
                $msgPoints = [];
                $cbPoints = [];
                $totalMaxForScale = max($sparkMax, 1);
                $maxOfStreams = max(1, $sparkline->max('rental') ?? 0, $sparkline->max('message') ?? 0, $sparkline->max('callback') ?? 0);

                foreach ($sparkline->values() as $idx => $p) {
                    $x = $padX + $stepX * $idx;
                    $totalY = $padY + $usableH * (1 - ($p['total'] / $totalMaxForScale));
                    $rentalY = $padY + $usableH * (1 - ($p['rental'] / $maxOfStreams));
                    $msgY = $padY + $usableH * (1 - ($p['message'] / $maxOfStreams));
                    $cbY = $padY + $usableH * (1 - ($p['callback'] / $maxOfStreams));
                    $points[] = ['x' => $x, 'y' => $totalY, 'p' => $p, 'is_today' => $p['date'] === $todayStr];
                    $rentalPoints[] = $x.','.round($rentalY, 2);
                    $msgPoints[] = $x.','.round($msgY, 2);
                    $cbPoints[] = $x.','.round($cbY, 2);
                }

                $linePath = '';
                foreach ($points as $i => $pt) {
                    $linePath .= ($i === 0 ? 'M' : 'L').round($pt['x'], 2).','.round($pt['y'], 2).' ';
                }
                $areaPath = $linePath
                    .'L'.round(end($points)['x'], 2).','.($svgH - $padY)
                    .' L'.round($points[0]['x'], 2).','.($svgH - $padY).' Z';
            @endphp

            <div class="dash-spark relative w-full">
                <svg viewBox="0 0 {{ $svgW }} {{ $svgH }}" preserveAspectRatio="none"
                     class="w-full h-48 overflow-visible block" role="img" aria-label="Son 14 gün lead trendi">
                    <defs>
                        <linearGradient id="dashSparkArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#7c3aed" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#7c3aed" stop-opacity="0"/>
                        </linearGradient>
                        <linearGradient id="dashSparkLine" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#370089"/>
                            <stop offset="100%" stop-color="#7c3aed"/>
                        </linearGradient>
                    </defs>

                    {{-- yatay grid çizgileri --}}
                    @for ($g = 0; $g <= 3; $g++)
                        @php $gy = $padY + $usableH * $g / 3; @endphp
                        <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $svgW - $padX }}" y2="{{ $gy }}"
                              class="dash-spark-grid" stroke-dasharray="3 4" stroke-width="1"/>
                    @endfor

                    {{-- 3 alt-stream ince çizgiler --}}
                    <polyline points="{{ implode(' ', $rentalPoints) }}" fill="none"
                              stroke="#fb7185" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" opacity="0.55"/>
                    <polyline points="{{ implode(' ', $msgPoints) }}" fill="none"
                              stroke="#f59e0b" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" opacity="0.55"/>
                    <polyline points="{{ implode(' ', $cbPoints) }}" fill="none"
                              stroke="#10b981" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" opacity="0.55"/>

                    {{-- toplam alan + çizgi --}}
                    <path d="{{ $areaPath }}" fill="url(#dashSparkArea)"/>
                    <path d="{{ $linePath }}" fill="none" stroke="url(#dashSparkLine)" stroke-width="2.5"
                          stroke-linecap="round" stroke-linejoin="round"/>

                    {{-- noktalar --}}
                    @foreach ($points as $pt)
                        <g class="dash-spark-point" tabindex="0">
                            <circle cx="{{ round($pt['x'], 2) }}" cy="{{ round($pt['y'], 2) }}" r="{{ $pt['is_today'] ? 5 : 3.5 }}"
                                    fill="#fff" stroke="#370089" stroke-width="{{ $pt['is_today'] ? 3 : 2 }}"/>
                            <title>{{ $pt['p']['label'] }} · {{ $pt['p']['total'] }} lead (Kiralama {{ $pt['p']['rental'] }}, Mesaj {{ $pt['p']['message'] }}, Geri arama {{ $pt['p']['callback'] }})</title>
                        </g>
                    @endforeach
                </svg>

                {{-- x ekseni etiketleri --}}
                <div class="grid mt-1 px-[18px]" style="grid-template-columns: repeat({{ $sparkline->count() }}, minmax(0, 1fr));">
                    @foreach ($sparkline as $p)
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 text-center truncate {{ $p['date'] === $todayStr ? 'font-bold text-[var(--color-primary,#370089)]' : '' }}">
                            {{ \Illuminate\Support\Str::substr($p['label'], 0, 6) }}
                        </span>
                    @endforeach
                </div>

                {{-- legend --}}
                <div class="flex flex-wrap items-center gap-4 mt-3 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                        <span class="w-3 h-1 rounded-sm bg-gradient-to-r from-[#370089] to-[#7c3aed]"></span> Toplam
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-slate-500">
                        <span class="w-3 h-0.5 rounded-sm bg-rose-400"></span> Kiralama
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-slate-500">
                        <span class="w-3 h-0.5 rounded-sm bg-amber-400"></span> Mesaj
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-slate-500">
                        <span class="w-3 h-0.5 rounded-sm bg-emerald-400"></span> Geri arama
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-3 text-center">
                <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                    <p class="text-[10px] uppercase tracking-wider text-rose-600 font-semibold">Kiralama</p>
                    <p class="text-xl font-black text-rose-700 mt-1">{{ $rentalCounts['last_30'] }}</p>
                    <p class="text-[10px] text-rose-500 mt-0.5">{{ $rentalCounts['pending'] }} beklemede</p>
                </div>
                <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                    <p class="text-[10px] uppercase tracking-wider text-amber-600 font-semibold">Mesajlar</p>
                    <p class="text-xl font-black text-amber-700 mt-1">{{ $messageCounts['last_30'] }}</p>
                    <p class="text-[10px] text-amber-600 mt-0.5">{{ $messageCounts['pending'] }} beklemede</p>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                    <p class="text-[10px] uppercase tracking-wider text-emerald-600 font-semibold">Geri arama</p>
                    <p class="text-xl font-black text-emerald-700 mt-1">{{ $callbackCounts['last_30'] }}</p>
                    <p class="text-[10px] text-emerald-600 mt-0.5">{{ $callbackCounts['pending'] }} beklemede</p>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4">
                <i class="ri-flashlight-fill text-amber-500"></i> Hızlı eylemler
            </h3>
            <div class="grid grid-cols-2 gap-2.5">
                @php
                    $actions = [
                        ['Yeni araç', 'ri-roadster-line', route('cars.create'), 'from-indigo-500 to-violet-600'],
                        ['Yeni sayfa', 'ri-file-add-line', route('pages.create'), 'from-emerald-500 to-teal-500'],
                        ['Yeni slider', 'ri-image-line', route('sliders.create'), 'from-pink-500 to-rose-500'],
                        ['Yeni SSS', 'ri-question-answer-line', route('faqs.create'), 'from-cyan-500 to-blue-500'],
                        ['Referans', 'ri-medal-line', route('references.create'), 'from-amber-500 to-orange-500'],
                        ['Menü düzenle', 'ri-list-settings-line', route('menus.index'), 'from-fuchsia-500 to-purple-500'],
                        ['Raporlar', 'ri-bar-chart-2-line', route('reports.index'), 'from-slate-700 to-slate-900'],
                        ['Ayarlar', 'ri-settings-3-line', route('settings'), 'from-violet-500 to-fuchsia-500'],
                    ];
                @endphp
                @foreach ($actions as [$label, $icon, $url, $tone])
                    <a href="{{ $url }}"
                       class="group flex flex-col items-start gap-2 p-3 rounded-xl border border-slate-100 hover:border-transparent hover:bg-gradient-to-br hover:{{ $tone }} hover:text-white transition-all">
                        <span class="w-9 h-9 rounded-lg bg-gradient-to-br {{ $tone }} text-white flex items-center justify-center text-lg group-hover:bg-white/20 group-hover:bg-none">
                            <i class="{{ $icon }}"></i>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 group-hover:text-white">{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            @if ($contentHealth['no_image'] + $contentHealth['no_description'] + $contentHealth['inactive'] > 0)
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ri-alert-line text-amber-500"></i> İçerik sağlığı
                    </p>
                    <ul class="space-y-1.5 text-xs">
                        @if ($contentHealth['no_image'] > 0)
                            <li class="flex items-center justify-between text-slate-600">
                                <span><i class="ri-image-line text-rose-500"></i> Görseli olmayan araç</span>
                                <span class="font-bold text-rose-600">{{ $contentHealth['no_image'] }}</span>
                            </li>
                        @endif
                        @if ($contentHealth['no_description'] > 0)
                            <li class="flex items-center justify-between text-slate-600">
                                <span><i class="ri-file-text-line text-amber-500"></i> Açıklaması eksik araç</span>
                                <span class="font-bold text-amber-600">{{ $contentHealth['no_description'] }}</span>
                            </li>
                        @endif
                        @if ($contentHealth['inactive'] > 0)
                            <li class="flex items-center justify-between text-slate-600">
                                <span><i class="ri-eye-off-line text-slate-500"></i> Pasif araç</span>
                                <span class="font-bold text-slate-700">{{ $contentHealth['inactive'] }}</span>
                            </li>
                        @endif
                        @if ($contentHealth['pages_inactive'] > 0)
                            <li class="flex items-center justify-between text-slate-600">
                                <span><i class="ri-file-forbid-line text-slate-500"></i> Pasif sayfa</span>
                                <span class="font-bold text-slate-700">{{ $contentHealth['pages_inactive'] }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Lead queues --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Rental requests --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-rose-50 to-transparent">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-rose-500 to-pink-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="ri-shopping-cart-2-line"></i>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-800">Kiralama talepleri</h3>
                        <p class="text-[11px] text-slate-500">Son 5 başvuru</p>
                    </div>
                </div>
                @if ($rentalCounts['pending'] > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        {{ $rentalCounts['pending'] }} yeni
                    </span>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($latestRentals as $req)
                    @php $unread = is_null($req->read_at); @endphp
                    <a href="{{ route('rental-requests.show', $req) }}"
                       class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50 transition-colors {{ $unread ? 'bg-rose-50/40' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($req->name ?? '?', 0, 1).\Illuminate\Support\Str::substr($req->surname ?? '', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ trim(($req->name ?? '').' '.($req->surname ?? '')) ?: 'İsimsiz' }}</p>
                                @if ($unread)
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate">{{ $req->email ?: $req->phone_number }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $req->requested_car_count ? $req->requested_car_count.' araç · ' : '' }}{{ optional($req->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center">
                        <i class="ri-inbox-line text-3xl text-slate-300"></i>
                        <p class="text-xs text-slate-400 mt-2">Henüz talep yok</p>
                    </div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-slate-100">
                <a href="{{ route('rental-requests.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1">
                    Tümünü görüntüle <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>

        {{-- Messages --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-transparent">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="ri-message-3-line"></i>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-800">İletişim mesajları</h3>
                        <p class="text-[11px] text-slate-500">Son 5 mesaj</p>
                    </div>
                </div>
                @if ($messageCounts['pending'] > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        {{ $messageCounts['pending'] }} yeni
                    </span>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($latestMessages as $msg)
                    @php $unread = is_null($msg->read_at); @endphp
                    <a href="{{ route('messages.show', $msg) }}"
                       class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50 transition-colors {{ $unread ? 'bg-amber-50/40' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($msg->name ?? '?', 0, 1).\Illuminate\Support\Str::substr($msg->surname ?? '', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ trim(($msg->name ?? '').' '.($msg->surname ?? '')) ?: 'İsimsiz' }}</p>
                                @if ($unread)
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate">{{ $msg->email }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if ($msg->category)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">
                                        {{ $msg->categoryLabel() }}
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400">{{ optional($msg->created_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center">
                        <i class="ri-mail-line text-3xl text-slate-300"></i>
                        <p class="text-xs text-slate-400 mt-2">Henüz mesaj yok</p>
                    </div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-slate-100">
                <a href="{{ route('messages.index') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1">
                    Tümünü görüntüle <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>

        {{-- We call you --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-transparent">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="ri-phone-line"></i>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-800">Geri arama talepleri</h3>
                        <p class="text-[11px] text-slate-500">Son 5 talep</p>
                    </div>
                </div>
                @if ($callbackCounts['pending'] > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $callbackCounts['pending'] }} yeni
                    </span>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($latestCallbacks as $cb)
                    @php $unread = is_null($cb->read_at); @endphp
                    <a href="{{ route('we-call-you.show', $cb) }}"
                       class="flex items-start gap-3 px-5 py-3 hover:bg-slate-50 transition-colors {{ $unread ? 'bg-emerald-50/40' : '' }}">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($cb->name ?? '?', 0, 1).\Illuminate\Support\Str::substr($cb->surname ?? '', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ trim(($cb->name ?? '').' '.($cb->surname ?? '')) ?: 'İsimsiz' }}</p>
                                @if ($unread)
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate">{{ $cb->phone_number }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $cb->preferred_time ? \Illuminate\Support\Str::limit($cb->preferred_time, 24).' · ' : '' }}{{ optional($cb->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center">
                        <i class="ri-phone-line text-3xl text-slate-300"></i>
                        <p class="text-xs text-slate-400 mt-2">Henüz geri arama talebi yok</p>
                    </div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-slate-100">
                <a href="{{ route('we-call-you.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    Tümünü görüntüle <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Featured fleet + content overview --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Featured cars --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="ri-star-line text-amber-500"></i> Anasayfada öne çıkan filo
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $fleetCounts['featured'] }} araç anasayfada görünüyor · Toplam {{ $fleetCounts['active'] }} aktif araç</p>
                </div>
                <a href="{{ route('cars.index') }}" class="text-xs font-semibold text-[var(--color-primary,#370089)] hover:underline shrink-0 inline-flex items-center gap-1">
                    Tüm filo <i class="ri-arrow-right-line"></i>
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($topFeaturedCars as $car)
                    <div class="flex items-center gap-4 px-5 py-3 hover:bg-slate-50 transition-colors">
                        <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 shrink-0 ring-1 ring-slate-200">
                            @if ($url = $car->displayImageUrl())
                                <img src="{{ $url }}" alt="{{ $car->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="ri-image-line"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $car->title }}</p>
                            <p class="text-[11px] text-slate-500 truncate">
                                {{ trim(($car->brand ?? '').' '.($car->model ?? '')) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('cars.show', $car->slug) }}" target="_blank" rel="noopener"
                               class="text-xs px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition inline-flex items-center gap-1">
                                <i class="ri-external-link-line"></i> Önizle
                            </a>
                            <a href="{{ route('cars.edit', $car) }}"
                               class="text-xs px-2.5 py-1.5 rounded-lg bg-[var(--color-primary,#370089)] hover:opacity-90 text-white transition inline-flex items-center gap-1">
                                <i class="ri-edit-2-line"></i> Düzenle
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <i class="ri-roadster-line text-4xl text-slate-300"></i>
                        <p class="text-sm text-slate-500 mt-3">Henüz öne çıkan araç yok</p>
                        <a href="{{ route('cars.create') }}" class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-[var(--color-primary,#370089)]">
                            <i class="ri-add-line"></i> İlk aracını ekle
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Content overview --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-4">
                <i class="ri-stack-line text-[var(--color-primary,#370089)]"></i> Site içeriği
            </h3>
            <div class="space-y-2.5">
                @php
                    $contentRows = [
                        ['Sayfalar', $contentCounts['pages'], 'ri-file-text-line', 'text-emerald-600 bg-emerald-50', route('pages.index')],
                        ['Sayfa kategorileri', $contentCounts['page_categories'], 'ri-folder-3-line', 'text-teal-600 bg-teal-50', route('page-categories.index')],
                        ['Sliderlar', $contentCounts['sliders'], 'ri-slideshow-line', 'text-pink-600 bg-pink-50', route('sliders.index')],
                        ['SSS', $contentCounts['faqs'], 'ri-question-answer-line', 'text-cyan-600 bg-cyan-50', route('faqs.index')],
                        ['Referanslar', $contentCounts['references'], 'ri-medal-line', 'text-amber-600 bg-amber-50', route('references.index')],
                        ['Hizmet kartları', $contentCounts['service_tiles'], 'ri-apps-line', 'text-violet-600 bg-violet-50', route('home-service-tiles.index')],
                        ['Partnerler', $contentCounts['partners'], 'ri-building-line', 'text-blue-600 bg-blue-50', route('home-partners.index')],
                        ['Yorumlar', $contentCounts['testimonials'], 'ri-double-quotes-l', 'text-fuchsia-600 bg-fuchsia-50', route('home-testimonials.index')],
                        ['Yöneticiler', $contentCounts['admins'], 'ri-shield-user-line', 'text-slate-700 bg-slate-100', route('users.index')],
                    ];
                @endphp
                @foreach ($contentRows as [$label, $count, $icon, $tone, $url])
                    <a href="{{ $url }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition group">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center {{ $tone }}">
                            <i class="{{ $icon }}"></i>
                        </span>
                        <span class="flex-1 text-sm text-slate-700 group-hover:text-slate-900 font-medium">{{ $label }}</span>
                        <span class="text-sm font-black text-slate-800">{{ $count }}</span>
                        <i class="ri-arrow-right-s-line text-slate-400 group-hover:text-slate-600"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Google Analytics istatistikleri --}}
    @include('admin.index_stats')
@endsection
