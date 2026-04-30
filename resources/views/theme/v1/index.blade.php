@extends('theme.v1.layout')

@section('meta')
    @include('theme.v1.components.meta', [
        'title' => 'Kurumsal Filo ve Uzun Dönem Araç Kiralama',
        'description' => $site['magicbox']['seo']['default_meta_description']
            ?? ($site['description'] ?? 'Kurumsal ve bireysel uzun dönem araç kiralama, filo yönetimi ve operasyonel kiralama çözümleri.'),
        'canonical' => route('home'),
        'ogType' => 'website',
    ])
@endsection

@push('jsonld')
    @php
        $homeUrl = route('home');
        $faqEntries = ($faqs ?? collect())->map(fn ($f) => [
            '@type' => 'Question',
            'name' => (string) $f->question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => strip_tags((string) $f->answer),
            ],
        ])->all();

        $vehicleListItems = ($featuredCars ?? collect())->values()->map(function ($car, $idx) {
            return [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'url' => route('cars.show', $car->slug),
                'name' => $car->title,
            ];
        })->all();

        $homeGraph = [];
        if (! empty($faqEntries)) {
            $homeGraph[] = [
                '@type' => 'FAQPage',
                '@id' => $homeUrl.'#faq',
                'mainEntity' => $faqEntries,
            ];
        }
        if (! empty($vehicleListItems)) {
            $homeGraph[] = [
                '@type' => 'ItemList',
                '@id' => $homeUrl.'#fleet',
                'name' => 'Öne çıkan filo araçları',
                'itemListElement' => $vehicleListItems,
            ];
        }
        $homePayload = [
            '@context' => 'https://schema.org',
            '@graph' => $homeGraph,
        ];
    @endphp
    @if (! empty($homeGraph))
        <script type="application/ld+json">{!! json_encode($homePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endpush

@push('styles')
    <style>
        /* Services section: only page-specific decoration */
        .services-bg {
            background:
                radial-gradient(900px 420px at 10% 0%, rgba(139, 92, 246, 0.25), transparent 60%),
                radial-gradient(800px 380px at 100% 100%, rgba(147, 194, 37, 0.18), transparent 60%),
                #37008a;
        }
        .services-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 42px 42px;
            -webkit-mask-image: radial-gradient(ellipse at center, #000 40%, transparent 80%);
                    mask-image: radial-gradient(ellipse at center, #000 40%, transparent 80%);
            pointer-events: none;
            opacity: .55;
        }
    </style>
@endpush

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="relative min-h-screen w-full overflow-hidden bg-primary">
        <div class="hero-slider h-full min-h-screen w-full !mb-0">
            @forelse ($heroSliders as $slide)
                <div class="slide relative h-full w-full min-h-[100vh]">
                    <div class="absolute inset-0">
                        @if ($slide->image1Url())
                            <img src="{{ $slide->image1Url() }}" alt="{{ $slide->badge ?: 'Hero' }}"
                                class="w-full h-full object-cover scale-105 motion-safe:animate-[heroKenburns_18s_ease-in-out_infinite_alternate]"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                fetchpriority="{{ $loop->first ? 'high' : 'low' }}" decoding="async">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary via-[#2a0068] to-slate-900"></div>
                        @endif
                        <div class="absolute inset-0 bg-black/30"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/30 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    </div>

                    <div class="hero-fade-up absolute bottom-24 left-4 right-4 sm:left-10 sm:right-auto z-10 p-6 lg:p-8 rounded-2xl shadow-2xl max-w-2xl bg-slate-900/55 backdrop-blur-md border border-white/15">
                        <div class="text-white">
                            @if ($slide->badge)
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md text-xs font-bold uppercase tracking-widest mb-5 border border-white/20">
                                    <span class="pulse-dot" aria-hidden="true"></span>
                                    {{ $slide->badge }}
                                </span>
                            @endif
                            <h2 class="text-4xl sm:text-5xl lg:text-7xl font-black leading-[1.05] mb-6 text-white">
                                {{ $slide->title }}
                                @if ($slide->title_highlight)
                                    <span class="block sm:inline sm:ml-2 mt-1 sm:mt-0 text-secondary">{{ $slide->title_highlight }}</span>
                                @endif
                            </h2>
                            @php $sub = $slide->subtitle ?: $slide->description; @endphp
                            @if ($sub)
                                <p class="text-lg sm:text-xl text-white/90 mb-8 font-light leading-relaxed max-w-xl">{{ $sub }}</p>
                            @endif
                            @if ($slide->link)
                                <a href="{{ $slide->link }}"
                                    class="group inline-flex items-center gap-2 rounded-xl bg-white text-primary px-6 py-3 text-sm font-bold shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl">
                                    Devamını gör
                                    <i class="ri-arrow-right-line text-base transition-transform duration-200 group-hover:translate-x-1"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if ($slide->image2Url())
                        <div class="pointer-events-none absolute bottom-32 -right-8 sm:-right-16 lg:-right-24 z-10 w-full max-w-6xl select-none">
                            <img src="{{ $slide->image2Url() }}" alt=""
                                class="w-full h-auto object-contain drop-shadow-2xl translate-y-[6%]"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                        </div>
                    @endif
                </div>
            @empty
                <div class="relative min-h-[100vh] flex items-center justify-center bg-gradient-to-br from-primary to-slate-900">
                    <div class="text-center text-white px-6 max-w-lg">
                        <i class="ri-slideshow-3-line text-5xl opacity-40 mb-4 block"></i>
                        <p class="text-lg font-medium">Henüz hero slaytı eklenmedi.</p>
                        <p class="text-sm text-white/70 mt-2">Yönetim panelinden «Ana sayfa içerik → Hero slayt» bölümünden görsel ve metinleri ekleyebilirsiniz.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($heroSliders->isNotEmpty())
            <div class="hero-slider-dots absolute bottom-0 left-1/2 -translate-x-1/2 z-20 flex gap-3 px-4"></div>
            <span class="scroll-hint absolute bottom-12 left-1/2 -translate-x-1/2 z-[15]" aria-hidden="true">Kaydır</span>
        @endif
    </section>

    {{-- ===================== FLEET ===================== --}}
    <section id="filo" class="relative py-24 px-6 max-w-7xl mx-auto scroll-mt-20">
        <div class="flex flex-col gap-6 sm:flex-row sm:justify-between sm:items-end mb-12 reveal">
            <div>
                <span class="eyebrow">Seçim yapın</span>
                <h2 class="text-3xl sm:text-4xl font-black mt-3 text-slate-900 tracking-tight">
                    Filonuzu <span class="text-primary">genişletin</span>
                </h2>
                <p class="text-slate-500 mt-3 text-sm max-w-xl leading-relaxed">
                    Öne çıkan filo araçlarımız; fiyatlar örnek matris satırına göredir.
                </p>
            </div>
            @if ($featuredCars->isNotEmpty())
                <div class="flex gap-2 shrink-0">
                    <button type="button"
                        class="fleet-slider-prev w-12 h-12 flex items-center justify-center rounded-full border border-primary/25 text-[var(--color-primary)] transition-all duration-200 hover:bg-primary hover:text-white hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/30"
                        aria-label="Önceki">
                        <i class="ri-arrow-left-s-line text-xl" aria-hidden="true"></i>
                    </button>
                    <button type="button"
                        class="fleet-slider-next w-12 h-12 flex items-center justify-center rounded-full border border-primary/25 text-[var(--color-primary)] transition-all duration-200 hover:bg-primary hover:text-white hover:-translate-y-0.5 hover:shadow-lg hover:shadow-primary/30"
                        aria-label="Sonraki">
                        <i class="ri-arrow-right-s-line text-xl" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        </div>

        @if ($featuredCars->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 px-6 py-16 text-center text-slate-500 reveal">
                <i class="ri-car-line text-4xl text-slate-300 mb-3 block"></i>
                <p class="font-medium text-slate-700">Anasayfada gösterilecek araç seçilmedi.</p>
                <p class="text-sm mt-2">Panelde araç düzenleme sayfasından «Anasayfa filo slider’ında göster» seçeneğini açın.</p>
            </div>
        @else
            <div class="fleet-slider pb-8 reveal">
                @foreach ($featuredCars as $car)
                    <div class="px-3">
                        <div class="group flex flex-col h-full p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-primary/15 hover:border-primary/30">
                            <a href="{{ route('cars.show', $car->slug) }}"
                                class="relative h-56 w-full rounded-xl overflow-hidden mb-5 bg-gradient-to-br from-slate-100 to-slate-200 block">
                                @if ($url = $car->displayImageUrl())
                                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.06]"
                                        src="{{ $url }}" alt="{{ $car->title }}" loading="lazy" decoding="async">
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300">
                                        <i class="ri-image-2-line text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                @if ($car->displayMonthlyPriceContext())
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-white text-[var(--color-primary)] text-xs font-bold shadow-sm">
                                        <i class="ri-price-tag-3-line"></i> Fırsat
                                    </span>
                                @endif
                            </a>

                            <div class="flex justify-between items-start gap-3 mb-3">
                                <h3 class="text-lg font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('cars.show', $car->slug) }}" class="hover:text-primary transition-colors">{{ $car->title }}</a>
                                </h3>
                                <div class="text-right shrink-0">
                                    @if ($priceCtx = $car->displayMonthlyPriceContext())
                                        <span class="text-[var(--color-primary)] font-black text-xl whitespace-nowrap leading-none">{{ $priceCtx['price'] }}</span>
                                        <span class="text-xs text-slate-500 block mt-0.5 leading-snug">
                                            aylık
                                            @if (! empty($priceCtx['duration_label']))
                                                <span class="text-slate-400" aria-hidden="true"> · </span>
                                                <span class="text-slate-600">{{ $priceCtx['duration_label'] }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">Teklif alın</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-6">
                                @foreach (array_filter([
                                    $car->transmission_type ? ['ri-settings-3-line', $car->transmission_type] : null,
                                    $car->fuel_type ? ['ri-gas-station-line', $car->fuel_type] : null,
                                    $car->body_type ? ['ri-car-line', $car->body_type] : null,
                                ]) as [$icon, $label])
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary/5 text-slate-600 text-xs font-medium transition-colors group-hover:bg-primary/10">
                                        <i class="{{ $icon }} text-[13px] text-primary"></i>{{ $label }}
                                    </span>
                                @endforeach
                            </div>

                            <a href="{{ route('cars.show', $car->slug) }}"
                                class="group/cta mt-auto w-full inline-flex items-center justify-center gap-1.5 bg-slate-100 text-[var(--color-primary)] py-3 rounded-xl font-bold transition-all duration-200 hover:bg-primary hover:text-white hover:shadow-lg hover:shadow-primary/40">
                                İncele
                                <i class="ri-arrow-right-line transition-transform duration-200 group-hover/cta:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ===================== SERVICES ===================== --}}
    <section class="services-bg relative py-24 px-6 overflow-hidden">
        <div class="relative max-w-7xl mx-auto text-center mb-16 reveal">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white/85 text-xs font-bold uppercase tracking-widest mb-5">
                <span class="pulse-dot" aria-hidden="true"></span> Hizmetler
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 tracking-tight">
                Yardımcı <span class="text-secondary">olalım</span>
            </h2>
            <p class="text-white/75 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                Kurumsal filo ve uzun dönem kiralama için ekibimizle iletişime geçin.
            </p>
        </div>

        <div class="relative max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse ($serviceTiles as $tile)
                @php
                    $cardClasses = 'group relative h-80 rounded-2xl overflow-hidden reveal transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-black/40';
                    $delay = $loop->index + 1;
                @endphp
                @if ($tile->link_url)
                    <a href="{{ $tile->link_url }}" class="{{ $cardClasses }} cursor-pointer block focus:outline-none focus:ring-2 focus:ring-white/40" data-delay="{{ $delay }}">
                @else
                    <div class="{{ $cardClasses }}" data-delay="{{ $delay }}">
                @endif
                        @if ($tile->imageUrl())
                            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.06]"
                                src="{{ $tile->imageUrl() }}" alt="{{ $tile->title }}" loading="lazy" decoding="async">
                        @else
                            <div class="w-full h-full bg-slate-800/60"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-primary/40 to-transparent"></div>

                        @if ($tile->link_url)
                            <span class="absolute top-4 right-4 w-9 h-9 inline-flex items-center justify-center rounded-full bg-white/15 border border-white/25 text-white backdrop-blur-md opacity-0 translate-x-1 -translate-y-1 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0 group-hover:translate-y-0">
                                <i class="ri-arrow-right-up-line text-lg" aria-hidden="true"></i>
                            </span>
                        @endif

                        <div class="absolute bottom-0 p-8 text-left">
                            @if ($tile->icon)
                                <span class="inline-flex items-center justify-center w-[52px] h-[52px] rounded-2xl mb-4 bg-white/10 border border-white/20 backdrop-blur-md text-2xl text-white transition-transform duration-500 group-hover:-rotate-6 group-hover:-translate-y-0.5">
                                    <i class="{{ $tile->icon }}" aria-hidden="true"></i>
                                </span>
                            @endif
                            <h3 class="text-2xl font-bold text-white tracking-tight">{{ $tile->title }}</h3>
                            @if ($tile->description)
                                <p class="text-white/85 text-sm mt-2 leading-relaxed">{{ $tile->description }}</p>
                            @endif
                        </div>
                @if ($tile->link_url)
                    </a>
                @else
                    </div>
                @endif
            @empty
                <div class="md:col-span-3 text-center text-white/70 py-8 text-sm">Hizmet kartı eklenmedi (panel: Ana sayfa içerik → Hizmet kutuları).</div>
            @endforelse
        </div>
    </section>

    {{-- ===================== PARTNERS ===================== --}}
    <section class="py-16 bg-white border-y border-primary/10 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 mb-8 flex items-center gap-4 reveal">
            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-primary/30 to-primary/20"></div>
            <span class="text-xs font-bold uppercase tracking-[0.3em] text-primary/70">İş ortaklarımız</span>
            <div class="h-px flex-1 bg-gradient-to-r from-primary/20 via-primary/30 to-transparent"></div>
        </div>
        @if ($partners->isEmpty())
            <p class="text-center text-slate-500 text-sm px-6">Partner listesi panelden yönetilir.</p>
        @else
            <div class="marquee-mask relative w-full overflow-hidden">
                <div class="marquee flex items-center gap-16 whitespace-nowrap w-max">
                    @foreach ([1, 2] as $_)
                        @foreach ($partners as $partner)
                            <span class="text-2xl sm:text-3xl font-black italic tracking-tight text-slate-500 transition-all duration-200 hover:text-primary hover:-translate-y-0.5">{{ $partner->name }}</span>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ===================== TESTIMONIALS ===================== --}}
    <section class="relative py-24 px-6 bg-background-light overflow-hidden">
        <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-20 w-96 h-96 rounded-full bg-secondary/10 blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto">
            <div class="text-center mb-16 reveal">
                <span class="eyebrow justify-center mx-auto">Deneyimler</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black mt-3 mb-4 text-slate-900 tracking-tight">
                    Müşterilerimiz <span class="text-primary">ne diyor?</span>
                </h2>
                <div class="flex justify-center gap-1 text-secondary" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)<i class="ri-star-fill text-xl"></i>@endfor
                </div>
                <p class="text-slate-500 mt-3 text-sm">Gerçek müşteri geri bildirimleri</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($testimonials as $t)
                    <div class="relative p-8 rounded-2xl bg-white shadow-sm border border-slate-200/80 reveal transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/15"
                        data-delay="{{ $loop->index + 1 }}">
                        <span class="absolute -top-3.5 right-4 w-12 h-12 inline-flex items-center justify-center rounded-full bg-gradient-to-br from-primary to-purple-700 text-white text-xl shadow-lg shadow-primary/40"
                            aria-hidden="true">
                            <i class="ri-double-quotes-r"></i>
                        </span>
                        <div class="flex gap-0.5 mb-4 text-secondary text-sm" aria-label="Puan {{ $t->rating }} üzerinden 5">
                            @for ($i = 0; $i < min(5, max(1, $t->rating)); $i++)<i class="ri-star-fill"></i>@endfor
                        </div>
                        <p class="text-slate-700 italic leading-relaxed text-[15px] mb-6">"{{ $t->quote }}"</p>
                        <div class="flex items-center gap-4 pt-5 border-t border-slate-200/70">
                            @if ($t->avatarUrl())
                                <div class="size-12 rounded-full ring-4 ring-primary/10 overflow-hidden shrink-0">
                                    <img src="{{ $t->avatarUrl() }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                </div>
                            @else
                                <div class="size-12 rounded-full bg-gradient-to-br from-primary to-purple-700 flex items-center justify-center text-white font-black text-base shrink-0 shadow-md shadow-primary/40">
                                    {{ mb_substr($t->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $t->name }}</h4>
                                @if ($t->role)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $t->role }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 text-center text-slate-500 py-8 text-sm">Henüz yorum eklenmedi.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== FAQ ===================== --}}
    <section id="sss" class="py-24 px-6 max-w-4xl mx-auto scroll-mt-20">
        <div class="text-center mb-12 reveal">
            <span class="eyebrow justify-center mx-auto">SSS</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black mt-3 text-slate-900 tracking-tight">
                Sık sorulan <span class="text-primary">sorular</span>
            </h2>
            <p class="text-slate-500 mt-3 text-sm">Merak ettiklerinizi kısaca yanıtladık.</p>
        </div>

        <div class="space-y-3">
            @forelse ($faqs as $faq)
                <details class="accordion-item group bg-white rounded-2xl border border-slate-200/80 transition-colors duration-200 hover:border-primary/35 open:border-primary/35 open:shadow-sm reveal"
                    data-delay="{{ min($loop->index, 4) }}">
                    <summary class="flex cursor-pointer items-center gap-4 px-6 py-5 text-left font-bold text-slate-900">
                        <span class="accordion-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="flex-1">{{ $faq->question }}</span>
                        <i class="accordion-chevron ri-arrow-down-s-line text-primary text-2xl shrink-0"></i>
                    </summary>
                    <div class="accordion-body px-6">
                        <div class="pl-[calc(2.25rem+1rem)] pb-5 pt-1 border-t border-primary/10 text-slate-600 text-sm leading-relaxed">
                            <p class="pt-4">{{ $faq->answerText() }}</p>
                        </div>
                    </div>
                </details>
            @empty
                <p class="text-center text-slate-500 text-sm">SSS maddesi eklenmedi.</p>
            @endforelse
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(function () {
            // Scroll reveal
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
                document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
            } else {
                document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('is-visible'); });
            }

            // Fleet slider
            if ($('.fleet-slider > div').length) {
                $('.fleet-slider').slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    prevArrow: $('.fleet-slider-prev'),
                    nextArrow: $('.fleet-slider-next'),
                    dots: false,
                    infinite: true,
                    responsive: [
                        { breakpoint: 1024, settings: { slidesToShow: 2, slidesToScroll: 1 } },
                        { breakpoint: 640,  settings: { slidesToShow: 1, slidesToScroll: 1 } }
                    ]
                });
            }

            // Hero slider
            var $hero = $('.hero-slider');
            if ($hero.length && $hero.find('.slide').length > 1) {
                $hero.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    fade: true,
                    speed: 700,
                    cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    autoplay: true,
                    autoplaySpeed: 5200,
                    dots: true,
                    appendDots: $('.hero-slider-dots'),
                    dotsClass: 'flex gap-3 !m-0 !p-0 list-none items-center justify-center flex-wrap',
                    arrows: false,
                    infinite: true,
                    pauseOnHover: true,
                    pauseOnFocus: true,
                    adaptiveHeight: false
                });
            }
        });
    </script>
@endsection
