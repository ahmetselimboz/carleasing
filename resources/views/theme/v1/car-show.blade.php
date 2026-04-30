@extends('theme.v1.layout')

@section('meta')
    @php
        $carSeo = data_get($car->magicbox ?? [], 'seo', []);
        $carMetaTitle = trim((string) data_get($carSeo, 'meta_title', ''));
        $carMetaDescription = trim((string) data_get($carSeo, 'meta_description', ''));
        $carMonthly = $car->displayMonthlyPriceLabel();
        $carDurationLabel = $car->displayMonthlyPriceDurationLabel();

        $autoTitle = $carMetaTitle !== ''
            ? $carMetaTitle
            : $car->title.' Uzun Dönem Kiralama'.($carMonthly ? ' · '.$carMonthly.'/ay'.($carDurationLabel ? ' ('.$carDurationLabel.')' : '') : '');

        $autoDesc = $carMetaDescription !== ''
            ? $carMetaDescription
            : (\Illuminate\Support\Str::limit(strip_tags((string) $car->description), 155)
                ?: ($car->title.' aracı için aylık ödeme, paket ve sözleşme süresi seçenekleriyle uzun dönem kiralama teklifi.'));
    @endphp
    @include('theme.v1.components.meta', [
        'title' => $autoTitle,
        'description' => $autoDesc,
        'canonical' => route('cars.show', $car->slug),
        'image' => $car->displayImageUrl(),
        'ogType' => 'product',
    ])
@endsection

@push('jsonld')
    @php
        $homeUrl = route('home');
        $carUrl = route('cars.show', $car->slug);
        $carImage = $car->displayImageUrl();

        $priceCandidates = $car->priceMatrices->pluck('monthly_price')
            ->map(function ($p) {
                $digits = preg_replace('/[^0-9]/', '', (string) $p);
                return $digits !== '' ? (int) $digits : null;
            })
            ->filter()
            ->values();
        $minPrice = $priceCandidates->min();
        $maxPrice = $priceCandidates->max();

        $vehicleNode = array_filter([
            '@type' => 'Vehicle',
            '@id' => $carUrl.'#vehicle',
            'name' => $car->title,
            'description' => strip_tags((string) $car->description),
            'url' => $carUrl,
            'image' => $carImage ?: null,
            'brand' => $car->brand ? ['@type' => 'Brand', 'name' => $car->brand] : null,
            'model' => $car->model ?: null,
            'vehicleTransmission' => $car->transmission_type ?: null,
            'fuelType' => $car->fuel_type ?: null,
            'bodyType' => $car->body_type ?: null,
        ], fn ($v) => $v !== null && $v !== '');

        $offerNode = null;
        if ($minPrice !== null) {
            $offerNode = array_filter([
                '@type' => $minPrice !== $maxPrice ? 'AggregateOffer' : 'Offer',
                'priceCurrency' => 'TRY',
                'lowPrice' => $minPrice !== $maxPrice ? $minPrice : null,
                'highPrice' => $minPrice !== $maxPrice ? $maxPrice : null,
                'price' => $minPrice === $maxPrice ? $minPrice : null,
                'offerCount' => $priceCandidates->count(),
                'availability' => 'https://schema.org/InStock',
                'priceValidUntil' => now()->addMonths(3)->toDateString(),
                'seller' => ['@id' => $homeUrl.'#organization'],
                'url' => $carUrl,
            ], fn ($v) => $v !== null);
        }

        $productNode = array_filter([
            '@type' => 'Product',
            '@id' => $carUrl.'#product',
            'name' => $car->title,
            'description' => strip_tags((string) $car->description),
            'image' => $carImage ?: null,
            'brand' => $car->brand ? ['@type' => 'Brand', 'name' => $car->brand] : null,
            'category' => 'Uzun Dönem Araç Kiralama',
            'offers' => $offerNode,
        ], fn ($v) => $v !== null);

        $breadcrumbNode = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => $homeUrl],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Uzun dönem kiralama', 'item' => $homeUrl.'#filo'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $car->title, 'item' => $carUrl],
            ],
        ];

        $faqEntries = ($faqs ?? collect())->map(fn ($f) => [
            '@type' => 'Question',
            'name' => (string) $f->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => strip_tags((string) $f->answer),
            ],
        ])->all();

        $carGraph = [$vehicleNode, $productNode, $breadcrumbNode];
        if (! empty($faqEntries)) {
            $carGraph[] = [
                '@type' => 'FAQPage',
                '@id' => $carUrl.'#faq',
                'mainEntity' => $faqEntries,
            ];
        }
        $carPayload = ['@context' => 'https://schema.org', '@graph' => $carGraph];
    @endphp
    <script type="application/ld+json">{!! json_encode($carPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    @php
        $defaultMatrix = $car->priceMatrices->first(function ($matrixItem) {
            return filled($matrixItem->monthly_price);
        }) ?? $car->priceMatrices->first();

        $defaultPrice = $defaultMatrix?->monthly_price;

        $defaultDuration = $defaultMatrix?->duration?->months;
        $contactPhone = data_get($site, 'magicbox.contact.phone');
    @endphp

    {{-- ======================= HERO ======================= --}}
    <section class="pt-28 pb-10 px-6 bg-gradient-to-b from-[var(--color-surface)] to-white">
        <div class="max-w-7xl mx-auto">
            {{-- breadcrumb --}}
            <nav class="text-xs text-slate-500 mb-6 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <a href="{{ route('home') }}#filo" class="hover:text-[var(--color-primary)]">Uzun dönem kiralama</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <span class="text-slate-700 font-medium truncate">{{ $car->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- IMAGE --}}
                <div class="lg:col-span-7">
                    <div
                        class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 aspect-[16/10] shadow-xl">
                        @if ($url = $car->displayImageUrl())
                            <img src="{{ $url }}" alt="{{ $car->title }}"
                                class="w-full h-full object-cover" loading="eager" fetchpriority="high"
                                decoding="async">
                        @else
                            <div class="flex h-full items-center justify-center text-slate-300">
                                <i class="ri-image-2-line text-6xl"></i>
                            </div>
                        @endif

                        @if ($car->brand)
                            <span
                                class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/95 backdrop-blur-md border border-white text-[var(--color-primary)] text-xs font-bold shadow-sm">
                                <i class="ri-award-line"></i> {{ $car->brand }}
                            </span>
                        @endif
                    </div>

                    {{-- Quick badges --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4">
                        @foreach (array_filter([
                            $car->fuel_type ? ['ri-gas-station-line', 'Yakıt Tipi', $car->fuel_type] : null,
                            $car->transmission_type ? ['ri-settings-3-line', 'Vites Tipi', $car->transmission_type] : null,
                            $car->body_type ? ['ri-car-line', 'Kasa Tipi', $car->body_type] : null,
                        ]) as [$icon, $label, $value])
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 hover:border-[var(--color-primary)]/30 transition-colors">
                                <span
                                    class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-lg shrink-0">
                                    <i class="{{ $icon }}"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">{{ $label }}</p>
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $value }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- INFO / PRICE CARD --}}
                <aside class="lg:col-span-5">
                    <div
                        class="sticky top-28 rounded-3xl bg-white border border-slate-200 shadow-xl overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            @if ($car->brand || $car->model)
                                <p class="text-xs uppercase tracking-widest text-[var(--color-primary)] font-bold mb-1.5">
                                    {{ trim(($car->brand ?? '').' '.($car->model ?? '')) }}
                                </p>
                            @endif
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                                {{ $car->title }}
                            </h1>
                        </div>

                        <div class="p-6 bg-gradient-to-br from-[var(--color-primary)]/5 via-transparent to-transparent">
                            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Aylık kira (KDV hariç)</p>
                            <div class="flex items-baseline gap-2">
                                <span id="price-display"
                                    class="text-4xl font-black text-[var(--color-primary)] leading-none">
                                    {{ $defaultPrice }}
                                </span>
                                @if ($defaultPrice)
                                    <span class="text-sm font-semibold text-slate-500">TL / ay</span>
                                @endif
                            </div>
                            <p id="price-meta" class="text-xs text-slate-500 mt-2">
                                @if ($defaultDuration)
                                    {{ $defaultDuration }} ay vade · seçeneklere göre güncellenir
                                @else
                                    Konfigürasyon seçimine göre güncellenir
                                @endif
                            </p>
                        </div>

                        <div class="p-6 space-y-3">
                            <button type="button" data-favorite-toggle data-car-slug="{{ $car->slug }}"
                                class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-semibold transition-all border border-slate-200">
                                <i class="ri-heart-line text-base" data-favorite-icon></i>
                                <span data-favorite-label>Listeye ekle</span>
                            </button>
                            <a id="hero-quote-link" href="{{ route('favorites.index') }}"
                                class="w-full inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25 hover:shadow-xl hover:shadow-[var(--color-primary)]/40">
                                <i class="ri-send-plane-fill text-lg"></i>
                                Listeye git
                            </a>
                            <a id="hero-callme-link" href="{{ route('we-call-you.create', ['car' => $car->slug]) }}"
                                class="w-full inline-flex items-center justify-center gap-2 bg-white border-2 border-[var(--color-primary)]/20 hover:border-[var(--color-primary)] text-[var(--color-primary)] py-3 rounded-xl font-semibold transition-all">
                                <i class="ri-phone-fill text-base"></i>
                                Biz sizi arayalım
                            </a>
                            @if (filled($contactPhone))
                                <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-semibold transition-all">
                                    <i class="ri-phone-line text-lg"></i>
                                    {{ $contactPhone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ======================= CONFIGURATOR ======================= --}}
    @if ($car->priceMatrices->isNotEmpty())
        <section class="py-12 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <header class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                        <span
                            class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                            <i class="ri-equalizer-line text-lg"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Kiralama koşulları</h2>
                            <p class="text-xs text-slate-500">Paket, vade, yıllık kilometre ve depozito seçimine göre fiyat anlık güncellenir.</p>
                        </div>
                    </header>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                        {{-- Paket --}}
                        @if ($packages->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                    <i class="ri-shield-check-line mr-1 text-[var(--color-primary)]"></i> Paket
                                </label>
                                <div class="space-y-2" data-config-group="package">
                                    @foreach ($packages as $pkg)
                                        <label
                                            class="block cursor-pointer rounded-xl border-2 border-slate-200 hover:border-[var(--color-primary)]/40 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 px-3 py-2.5 transition-all">
                                            <input type="radio" name="cfg_package" value="{{ $pkg->id }}"
                                                class="sr-only" @checked($loop->first)>
                                            <span class="flex items-center justify-between gap-2">
                                                <span class="text-sm font-semibold text-slate-800">{{ $pkg->name }}</span>
                                                <i class="ri-check-line text-[var(--color-primary)] opacity-0 has-[:checked]:opacity-100"></i>
                                            </span>
                                            @if ($pkg->description)
                                                <p class="text-[11px] text-slate-500 mt-1 leading-snug line-clamp-2">{{ $pkg->description }}</p>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Vade --}}
                        @if ($durations->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                    <i class="ri-calendar-line mr-1 text-[var(--color-primary)]"></i> Vade
                                </label>
                                <div class="grid grid-cols-3 gap-2" data-config-group="duration">
                                    @foreach ($durations as $dur)
                                        <label
                                            class="cursor-pointer text-center rounded-xl border-2 border-slate-200 hover:border-[var(--color-primary)]/40 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 px-2 py-3 transition-all">
                                            <input type="radio" name="cfg_duration" value="{{ $dur->id }}"
                                                class="sr-only" @checked($loop->first)>
                                            <span class="block text-lg font-black text-slate-800">{{ $dur->months }}</span>
                                            <span class="block text-[10px] uppercase tracking-wide text-slate-500">ay</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Kilometre --}}
                        @if ($kilometers->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                    <i class="ri-road-map-line mr-1 text-[var(--color-primary)]"></i> Yıllık km
                                </label>
                                <select name="cfg_kilometer"
                                    class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-3 py-3 text-sm font-semibold text-slate-800 bg-white">
                                    @foreach ($kilometers as $km)
                                        <option value="{{ $km->id }}">{{ number_format((float) $km->kilometer, 0, ',', '.') }} km</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                      
                        {{-- Depozito --}}
                        @if ($downPayments->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                                    <i class="ri-bank-card-line mr-1 text-[var(--color-primary)]"></i> Depozito
                                </label>
                                <select name="cfg_down_payment"
                                    class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-3 py-3 text-sm font-semibold text-slate-800 bg-white">
                                    @foreach ($downPayments as $dp)
                                        <option value="{{ $dp->id }}">{{ $dp->amount }} </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="px-6 pb-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">
                                <i class="ri-file-list-3-line mr-1 text-[var(--color-primary)]"></i> Seçili teklif özeti
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
                                <div class="rounded-xl bg-white border border-slate-200 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Paket</p>
                                    <p id="summary-package" class="font-bold text-slate-800">-</p>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Vade</p>
                                    <p id="summary-duration" class="font-bold text-slate-800">-</p>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Yıllık km</p>
                                    <p id="summary-kilometer" class="font-bold text-slate-800">-</p>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 px-3 py-2">
                                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Depozito</p>
                                    <p id="summary-down-payment" class="font-bold text-slate-800">-</p>
                                </div>
                            </div>
                            <div id="summary-extras-row" class="hidden mt-3 text-xs text-slate-600">
                                <span class="font-semibold">Ek hizmetler:</span>
                                <span id="summary-extras">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Extra services --}}
                    @if ($extraServices->isNotEmpty())
                        <div class="px-6 pb-6">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3">
                                <i class="ri-add-circle-line mr-1 text-[var(--color-primary)]"></i> Ek hizmetler
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($extraServices as $svc)
                                    <label
                                        class="flex items-start gap-3 rounded-xl border-2 border-slate-200 hover:border-[var(--color-primary)]/40 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 p-4 cursor-pointer transition-all">
                                        <input type="checkbox" name="extras[]" value="{{ $svc->id }}"
                                            class="mt-0.5 w-4 h-4 accent-[var(--color-primary)]"
                                            data-extra-name="{{ $svc->name }}"
                                            data-extra-price="{{ (float) ($svc->price ?? 0) }}">
                                        <span class="flex-1 min-w-0">
                                            <span class="block text-sm font-semibold text-slate-800">{{ $svc->name }}</span>
                                            @if ($svc->description)
                                                <span class="block text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $svc->description }}</span>
                                            @endif
                                        </span>
                                        @if ($svc->price)
                                            <span class="shrink-0 text-right">
                                                <span class="block text-sm font-black text-[var(--color-primary)] whitespace-nowrap">
                                                    +{{ number_format((float) $svc->price, 0, ',', '.') }} TL
                                                </span>
                                                <span class="block text-[10px] text-slate-500">aylık</span>
                                            </span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ======================= SPECS ======================= --}}
    @if (! empty($specGroups))
        <section class="py-12 px-6 bg-[var(--color-surface)]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-10">
                    <span class="eyebrow justify-center mx-auto">Donanım</span>
                    <h2 class="text-3xl sm:text-4xl font-black mt-3 text-slate-900 tracking-tight">
                        Teknik <span class="text-[var(--color-primary)]">özellikler</span>
                    </h2>
                </div>

                {{-- Tabs (desktop) --}}
                <div class="hidden md:block rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="grid grid-cols-12 min-h-[420px]">
                        <aside class="col-span-4 border-r border-slate-100 bg-slate-50/50">
                            <ul class="py-4" id="spec-tabs">
                                @foreach ($specGroups as $idx => $group)
                                    <li>
                                        <button type="button" data-spec-tab="{{ $idx }}"
                                            class="spec-tab w-full text-left px-6 py-3 text-sm font-semibold transition-all border-l-4 {{ $idx === 0 ? 'border-[var(--color-primary)] bg-white text-[var(--color-primary)]' : 'border-transparent text-slate-600 hover:bg-white hover:text-slate-900' }}">
                                            <span class="flex items-center justify-between gap-2">
                                                {{ $group['name'] }}
                                                <i class="ri-arrow-right-s-line text-base"></i>
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                        <div class="col-span-8 p-8">
                            @foreach ($specGroups as $idx => $group)
                                <div data-spec-panel="{{ $idx }}" class="{{ $idx === 0 ? '' : 'hidden' }}">
                                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                                        <i class="ri-checkbox-circle-line text-[var(--color-primary)]"></i>
                                        {{ $group['name'] }}
                                    </h3>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                                        @foreach ($group['rows'] as $row)
                                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                                <dt class="text-sm text-slate-500">{{ $row['label'] }}</dt>
                                                <dd class="text-sm font-semibold text-slate-800 text-right">{{ $row['value'] ?: '—' }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Accordion (mobile) --}}
                <div class="md:hidden space-y-3">
                    @foreach ($specGroups as $idx => $group)
                        <details class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden group">
                            <summary
                                class="list-none flex items-center justify-between gap-3 px-5 py-4 cursor-pointer">
                                <span class="font-semibold text-slate-800 flex items-center gap-2">
                                    <i class="ri-checkbox-circle-line text-[var(--color-primary)]"></i>
                                    {{ $group['name'] }}
                                </span>
                                <i class="ri-arrow-down-s-line text-slate-400 group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <div class="px-5 pb-5 pt-1 space-y-3 border-t border-slate-100">
                                @foreach ($group['rows'] as $row)
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-sm text-slate-500">{{ $row['label'] }}</span>
                                        <span class="text-sm font-semibold text-slate-800 text-right">{{ $row['value'] ?: '—' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ======================= DESCRIPTION ======================= --}}
    @if (filled($car->description))
        <section class="py-12 px-6">
            <div class="max-w-4xl mx-auto">
                <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8">
                    <h2 class="text-2xl font-black text-slate-900 mb-4 flex items-center gap-2">
                        <i class="ri-information-line text-[var(--color-primary)]"></i>
                        Araç hakkında
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                        {!! $car->description !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ======================= CTA: BIZ SIZI ARAYALIM ======================= --}}
    <section id="teklif" class="py-16 px-6 scroll-mt-24">
        <div class="max-w-5xl mx-auto">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-primary-600)] to-[var(--color-primary)] shadow-2xl">
                <div class="absolute inset-0 opacity-30 pointer-events-none"
                    style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.25), transparent 50%), radial-gradient(circle at 80% 80%, rgba(255,255,255,0.18), transparent 45%);"></div>

                <div class="relative p-8 sm:p-12 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 text-white">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-xs font-bold uppercase tracking-widest mb-4">
                            <i class="ri-phone-line"></i> Geri arama
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
                            Vaktiniz yok mu? <br class="hidden sm:block">
                            <span class="text-white/85">Biz sizi arayalım.</span>
                        </h2>
                        <p class="text-white/80 text-sm sm:text-base mt-3 leading-relaxed max-w-md">
                            Aşağıdaki seçili konfigürasyon ve aracın bilgileri hazır gelir; siz sadece iletişim bilgilerinizi paylaşın.
                        </p>

                        <ul class="mt-5 space-y-2 text-sm text-white/85">
                            <li class="flex items-center gap-2"><i class="ri-checkbox-circle-line text-emerald-300"></i> Aynı gün içinde geri dönüş</li>
                            <li class="flex items-center gap-2"><i class="ri-checkbox-circle-line text-emerald-300"></i> Kişiye özel teklif</li>
                            <li class="flex items-center gap-2"><i class="ri-checkbox-circle-line text-emerald-300"></i> Bilgileriniz KVKK ile korunur</li>
                        </ul>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-2xl bg-white p-6 shadow-xl">
                            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Seçili konfigürasyona göre aylık</p>
                            <div class="flex items-baseline gap-2 mb-1">
                                <span id="cta-price-display" class="text-3xl sm:text-4xl font-black text-[var(--color-primary)] leading-none">
                                    {{ $defaultPrice }}
                                </span>
                                @if ($defaultPrice)
                                    <span class="text-sm font-semibold text-slate-500">TL / ay</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 mb-4">+ KDV · konfigürasyon değiştikçe güncellenir</p>

                            <a id="cta-quote-link" href="{{ route('favorites.index') }}"
                                class="w-full inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/30 hover:shadow-xl hover:shadow-[var(--color-primary)]/45">
                                <i class="ri-send-plane-fill"></i> Listeye git
                            </a>
                            <a id="callme-link" href="{{ route('we-call-you.create', ['car' => $car->slug]) }}"
                                class="mt-2 w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-semibold transition-all text-sm">
                                <i class="ri-phone-fill"></i> Biz sizi arayalım
                            </a>
                            @if (filled($contactPhone))
                                <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}"
                                    class="mt-2 w-full inline-flex items-center justify-center gap-2 text-slate-600 hover:text-[var(--color-primary)] py-2 rounded-xl font-medium transition-all text-sm">
                                    <i class="ri-phone-line"></i> {{ $contactPhone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= FAQ ======================= --}}
    @if ($faqs->isNotEmpty())
        <section class="py-16 px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <span class="eyebrow justify-center mx-auto">Sıkça sorulan sorular</span>
                    <h2 class="text-3xl sm:text-4xl font-black mt-3 text-slate-900 tracking-tight">
                        Aklınızda <span class="text-[var(--color-primary)]">soru kalmasın</span>
                    </h2>
                </div>
                <div class="space-y-3">
                    @foreach ($faqs as $faq)
                        <details class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden group">
                            <summary
                                class="list-none flex items-center justify-between gap-3 px-6 py-5 cursor-pointer hover:bg-slate-50 transition-colors">
                                <span class="font-semibold text-slate-800">{{ $faq->question }}</span>
                                <span
                                    class="w-8 h-8 inline-flex items-center justify-center rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] group-open:rotate-45 transition-transform shrink-0">
                                    <i class="ri-add-line text-lg"></i>
                                </span>
                            </summary>
                            <div class="px-6 pb-6 pt-1 text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none">
                                {!! $faq->answerText() !!}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ======================= RELATED CARS ======================= --}}
    @if ($relatedCars->isNotEmpty())
        <section class="py-16 px-6 bg-[var(--color-surface)]">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <span class="eyebrow">Benzer araçlar</span>
                        <h2 class="text-2xl sm:text-3xl font-black mt-3 text-slate-900 tracking-tight">
                            Bunlar da <span class="text-[var(--color-primary)]">ilgini çekebilir</span>
                        </h2>
                    </div>
                    <a href="{{ route('home') }}#filo"
                        class="hidden sm:inline-flex items-center gap-1.5 text-sm font-bold text-[var(--color-primary)] hover:underline">
                        Tüm filo <i class="ri-arrow-right-line"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($relatedCars as $rc)
                        @php $rcCtx = $rc->displayMonthlyPriceContext(); @endphp
                        <a href="{{ route('cars.show', $rc->slug) }}"
                            class="group flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-[var(--color-primary)]/30 transition-all overflow-hidden">
                            <div class="relative aspect-[4/3] bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                                @if ($rcUrl = $rc->displayImageUrl())
                                    <img src="{{ $rcUrl }}" alt="{{ $rc->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        loading="lazy" decoding="async">
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300">
                                        <i class="ri-image-2-line text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="text-sm font-bold text-slate-900 leading-snug mb-2 line-clamp-2">{{ $rc->title }}</h3>
                                <div class="mt-auto flex items-end justify-between gap-2">
                                    @if ($rcCtx)
                                        <div>
                                            <span class="block text-[10px] uppercase tracking-wide text-slate-500">aylık</span>
                                            <span class="text-base font-black text-[var(--color-primary)]">{{ $rcCtx['price'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">Teklif alın</span>
                                    @endif
                                    <span class="w-8 h-8 inline-flex items-center justify-center rounded-full bg-slate-100 text-[var(--color-primary)] group-hover:bg-[var(--color-primary)] group-hover:text-white transition-colors">
                                        <i class="ri-arrow-right-line"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ======================= STICKY MOBILE BAR ======================= --}}
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200 shadow-2xl px-4 py-3">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <span class="block text-[10px] uppercase tracking-wide text-slate-500">Aylık</span>
                <span id="price-display-mobile" class="text-lg font-black text-[var(--color-primary)] block leading-none">
                    {{ $defaultPrice ?: 'Teklif alın' }}
                </span>
            </div>
            <a id="mobile-callme-link" href="{{ route('we-call-you.create', ['car' => $car->slug]) }}"
                class="inline-flex items-center gap-1.5 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-5 py-3 rounded-xl font-bold text-sm whitespace-nowrap">
                <i class="ri-phone-fill"></i> Beni ara
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const matrix = @json($matrixRows);

            const $priceMain = document.getElementById('price-display');
            const $priceMeta = document.getElementById('price-meta');
            const $priceMobile = document.getElementById('price-display-mobile');
            const $priceCta = document.getElementById('cta-price-display');
            const $summaryPackage = document.getElementById('summary-package');
            const $summaryDuration = document.getElementById('summary-duration');
            const $summaryKilometer = document.getElementById('summary-kilometer');
            const $summaryDownPayment = document.getElementById('summary-down-payment');
            const $summaryExtrasRow = document.getElementById('summary-extras-row');
            const $summaryExtras = document.getElementById('summary-extras');
            const callmeBaseUrl = @json(route('we-call-you.create'));
            const carSlug = @json($car->slug);
            const FAVORITES_KEY = 'carleasing:favorites';
            const callmeLinks = ['callme-link', 'hero-callme-link', 'mobile-callme-link']
                .map(id => document.getElementById(id))
                .filter(Boolean);
            const fmtTL = new Intl.NumberFormat('tr-TR');

            const toNumber = (val) => {
                if (typeof val === 'number' && Number.isFinite(val)) return val;
                if (val == null) return null;
                const normalized = String(val).replace(/\./g, '').replace(',', '.').replace(/[^\d.-]/g, '');
                const parsed = Number(normalized);
                return Number.isFinite(parsed) ? parsed : null;
            };

            const formatPrice = (amount) => {
                if (amount == null) return 'Teklif alın';
                return fmtTL.format(Math.round(amount));
            };
            const getFavorites = () => {
                try {
                    const parsed = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
                    return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
                } catch (err) {
                    return [];
                }
            };
            const setFavorites = (favorites) => {
                localStorage.setItem(FAVORITES_KEY, JSON.stringify(Array.from(new Set(favorites))));
                window.dispatchEvent(new CustomEvent('favorites:changed'));
            };
            const setFavoriteButtonState = () => {
                const button = document.querySelector('[data-favorite-toggle]');
                if (!button) return;
                const icon = button.querySelector('[data-favorite-icon]');
                const label = button.querySelector('[data-favorite-label]');
                const isFavorite = getFavorites().includes(carSlug);
                button.classList.toggle('bg-rose-50', isFavorite);
                button.classList.toggle('hover:bg-rose-100', isFavorite);
                button.classList.toggle('text-rose-700', isFavorite);
                button.classList.toggle('border-rose-200', isFavorite);
                button.classList.toggle('bg-slate-100', !isFavorite);
                button.classList.toggle('hover:bg-slate-200', !isFavorite);
                button.classList.toggle('text-slate-800', !isFavorite);
                button.classList.toggle('border-slate-200', !isFavorite);
                if (icon) {
                    icon.classList.toggle('ri-heart-fill', isFavorite);
                    icon.classList.toggle('ri-heart-line', !isFavorite);
                }
                if (label) {
                    label.textContent = isFavorite ? 'Listeden çıkar' : 'Listeye ekle';
                }
            };

            const getLabel = (name) => {
                const checked = document.querySelector(`[name="${name}"]:checked`);
                if (checked && checked.closest('label')) {
                    const txt = checked.closest('label').innerText || '';
                    return txt.split('\n').map(s => s.trim()).filter(Boolean)[0] || '-';
                }
                const input = document.querySelector(`[name="${name}"]`);
                if (!input) return '-';
                if (input.tagName === 'SELECT') {
                    return input.options[input.selectedIndex]?.text || '-';
                }
                return '-';
            };

            const getSelectedExtras = () => {
                return Array.from(document.querySelectorAll('[name="extras[]"]:checked')).map(el => ({
                    id: Number(el.value),
                    name: el.dataset.extraName || 'Ek hizmet',
                    price: toNumber(el.dataset.extraPrice) || 0,
                }));
            };

            const getCurrent = () => {
                const get = (n) => {
                    const el = document.querySelector(`[name="${n}"]:checked`) || document.querySelector(`[name="${n}"]`);
                    if (!el) return null;
                    const v = el.value;
                    return v === '' ? null : Number(v);
                };
                return {
                    package_id: get('cfg_package'),
                    duration_id: get('cfg_duration'),
                    kilometer_id: get('cfg_kilometer'),
                    down_payment_id: get('cfg_down_payment'),
                };
            };

            const findRow = (sel) => {
                return matrix.find(r =>
                    (sel.package_id == null || r.package_id == sel.package_id) &&
                    (sel.duration_id == null || r.duration_id == sel.duration_id) &&
                    (sel.kilometer_id == null || r.kilometer_id == sel.kilometer_id) &&
                    (sel.down_payment_id == null || r.down_payment_id == sel.down_payment_id)
                );
            };

            const buildCallmeUrl = (sel) => {
                const params = new URLSearchParams();
                if (carSlug) params.set('car', carSlug);
                if (sel.package_id != null) params.set('package', sel.package_id);
                if (sel.duration_id != null) params.set('duration', sel.duration_id);
                if (sel.kilometer_id != null) params.set('kilometer', sel.kilometer_id);
                if (sel.down_payment_id != null) params.set('down_payment', sel.down_payment_id);
                const qs = params.toString();
                return callmeBaseUrl + (qs ? '?' + qs : '');
            };

            const update = () => {
                const sel = getCurrent();
                const row = findRow(sel);
                const fallbackRow = matrix.find(item => toNumber(item.monthly_price) != null) || null;
                const effectiveRow = row || fallbackRow;
                const basePrice = effectiveRow ? toNumber(effectiveRow.monthly_price) : null;
                const extras = getSelectedExtras();
                const extrasTotal = extras.reduce((sum, item) => sum + item.price, 0);
                const totalPrice = basePrice == null ? null : basePrice + extrasTotal;
                const priceText = formatPrice(totalPrice);

                if ($priceMain) $priceMain.textContent = priceText;
                if ($priceMobile) $priceMobile.textContent = priceText;
                if ($priceCta) $priceCta.textContent = priceText;

                if ($priceMeta) {
                    $priceMeta.textContent = effectiveRow
                        ? (extrasTotal > 0
                            ? `Bu konfigürasyona göre aylık fiyat (ek hizmetler dahil +${formatPrice(extrasTotal)} TL)`
                            : 'Bu konfigürasyona göre aylık fiyat')
                        : 'Bu kombinasyon için fiyat girilmemiş — lütfen teklif isteyin';
                }

                if ($summaryPackage) $summaryPackage.textContent = getLabel('cfg_package');
                if ($summaryDuration) $summaryDuration.textContent = getLabel('cfg_duration');
                if ($summaryKilometer) $summaryKilometer.textContent = getLabel('cfg_kilometer');
                if ($summaryDownPayment) $summaryDownPayment.textContent = getLabel('cfg_down_payment');
                if ($summaryExtrasRow && $summaryExtras) {
                    if (extras.length > 0) {
                        $summaryExtrasRow.classList.remove('hidden');
                        $summaryExtras.textContent = extras.map(item => item.name).join(', ');
                    } else {
                        $summaryExtrasRow.classList.add('hidden');
                        $summaryExtras.textContent = '-';
                    }
                }
                const url = buildCallmeUrl(sel);
                callmeLinks.forEach(a => { a.href = url; });
            };

            document.querySelectorAll('[name="cfg_package"], [name="cfg_duration"], [name="cfg_kilometer"], [name="cfg_down_payment"], [name="extras[]"]').forEach(el => {
                el.addEventListener('change', update);
            });
            document.querySelector('[data-favorite-toggle]')?.addEventListener('click', () => {
                const favorites = getFavorites();
                const next = favorites.includes(carSlug)
                    ? favorites.filter(item => item !== carSlug)
                    : [...favorites, carSlug];
                setFavorites(next);
                setFavoriteButtonState();
            });
            window.addEventListener('storage', setFavoriteButtonState);
            window.addEventListener('favorites:changed', setFavoriteButtonState);

            // Spec tabs
            document.querySelectorAll('[data-spec-tab]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const idx = btn.dataset.specTab;
                    document.querySelectorAll('.spec-tab').forEach(b => {
                        b.classList.remove('border-[var(--color-primary)]', 'bg-white', 'text-[var(--color-primary)]');
                        b.classList.add('border-transparent', 'text-slate-600');
                    });
                    btn.classList.add('border-[var(--color-primary)]', 'bg-white', 'text-[var(--color-primary)]');
                    btn.classList.remove('border-transparent', 'text-slate-600');
                    document.querySelectorAll('[data-spec-panel]').forEach(p => p.classList.add('hidden'));
                    const panel = document.querySelector('[data-spec-panel="' + idx + '"]');
                    if (panel) panel.classList.remove('hidden');
                });
            });

            update();
            setFavoriteButtonState();
        })();
    </script>
@endpush
