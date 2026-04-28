@extends('theme.v1.layout')

@php
    $mb = $page->magicbox ?? [];
    $hero = $mb['hero'] ?? null;
    $intro = $mb['intro'] ?? null;
    $ctas = $mb['ctas'] ?? [];
    $sections = $mb['sections'] ?? [];
    $faqs = $mb['faqs'] ?? [];
    $finalCta = $mb['final_cta'] ?? null;
@endphp

@section('meta')
    @php
        $pageSeo = data_get($page->magicbox ?? [], 'seo', []);
        $pageMetaTitle = trim((string) data_get($pageSeo, 'meta_title', ''));
        $pageMetaDesc = trim((string) data_get($pageSeo, 'meta_description', ''));
        $pageHeroImage = data_get($page->magicbox ?? [], 'hero.image_url')
            ?? data_get($page->magicbox ?? [], 'hero.image');
        if ($pageHeroImage && ! str_starts_with($pageHeroImage, 'http')) {
            $pageHeroImage = asset('storage/'.ltrim($pageHeroImage, '/'));
        }
    @endphp
    @include('theme.v1.components.meta', [
        'title' => $pageMetaTitle !== '' ? $pageMetaTitle : $page->title,
        'description' => $pageMetaDesc !== '' ? $pageMetaDesc : Str::limit(strip_tags((string) $page->description), 160),
        'canonical' => route('public.pages.show', $page->slug),
        'image' => $pageHeroImage ?: null,
        'ogType' => 'article',
    ])
@endsection

@push('jsonld')
    @php
        $homeUrl = route('home');
        $pageUrl = route('public.pages.show', $page->slug);
        $bcItems = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => $homeUrl],
        ];
        $pos = 2;
        if ($page->category) {
            $bcItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $page->category->name, 'item' => $pageUrl];
        }
        $bcItems[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => $page->title, 'item' => $pageUrl];

        $faqEntries = collect(data_get($page->magicbox ?? [], 'faqs', []))
            ->filter(fn ($f) => filled(data_get($f, 'question')) && filled(data_get($f, 'answer')))
            ->map(fn ($f) => [
                '@type' => 'Question',
                'name' => (string) data_get($f, 'question'),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags((string) data_get($f, 'answer')),
                ],
            ])->values()->all();

        $articleNode = [
            '@type' => 'WebPage',
            '@id' => $pageUrl.'#webpage',
            'url' => $pageUrl,
            'name' => $page->title,
            'inLanguage' => 'tr-TR',
            'isPartOf' => ['@id' => $homeUrl.'#website'],
            'description' => Str::limit(strip_tags((string) $page->description), 200),
            'datePublished' => optional($page->created_at)->toIso8601String(),
            'dateModified' => optional($page->updated_at)->toIso8601String(),
        ];

        $pageGraph = [
            $articleNode,
            ['@type' => 'BreadcrumbList', 'itemListElement' => $bcItems],
        ];
        if (! empty($faqEntries)) {
            $pageGraph[] = ['@type' => 'FAQPage', '@id' => $pageUrl.'#faq', 'mainEntity' => $faqEntries];
        }
        $payload = ['@context' => 'https://schema.org', '@graph' => $pageGraph];
    @endphp
    <script type="application/ld+json">{!! json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    {{-- HERO --}}
    <section class="pt-28 pb-12 px-6 bg-gradient-to-b from-[var(--color-surface)] to-white">
        <div class="max-w-6xl mx-auto">
            <nav class="text-xs text-slate-500 mb-5 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                @if ($page->category)
                    <span class="text-slate-600">{{ $page->category->name }}</span>
                    <i class="ri-arrow-right-s-line text-slate-400"></i>
                @endif
                <span class="text-slate-700 font-medium">{{ $page->title }}</span>
            </nav>

            <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-600)] text-white p-8 sm:p-12 shadow-xl">
                @if ($hero && ! empty($hero['badge']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-xs font-bold uppercase tracking-widest mb-4">
                        <i class="ri-sparkling-2-line"></i> {{ $hero['badge'] }}
                    </span>
                @endif
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">{{ $hero['title'] ?? $page->title }}</h1>
                @if (! empty($hero['subtitle']))
                    <p class="text-white/85 text-base sm:text-lg max-w-3xl">{{ $hero['subtitle'] }}</p>
                @endif

                @if (count($ctas) > 0)
                    <div class="flex flex-wrap gap-3 mt-7">
                        @foreach ($ctas as $cta)
                            <a href="{{ $cta['href'] ?? '#' }}"
                                class="inline-flex items-center gap-2 bg-white text-[var(--color-primary)] hover:bg-slate-100 px-6 py-3 rounded-xl font-bold text-sm shadow-lg transition-all">
                                @if (! empty($cta['icon']))
                                    <i class="{{ $cta['icon'] }}"></i>
                                @endif
                                {{ $cta['label'] ?? 'Detay' }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 pb-16">
        <article class="lg:col-span-8 space-y-10">
            @if ($intro)
                <section class="prose max-w-none text-slate-700 text-base leading-relaxed">
                    <p>{{ $intro }}</p>
                </section>
            @endif

            @if (! empty($page->description) && trim($page->description) !== '')
                <section class="text-slate-700 text-base leading-relaxed whitespace-pre-line">
                    {{ $page->description }}
                </section>
            @endif

            @foreach ($sections as $section)
                @php $type = $section['type'] ?? 'text'; @endphp

                @if ($type === 'text')
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7">
                        @if (! empty($section['title']))
                            <h2 class="text-2xl font-black text-slate-900 mb-3">{{ $section['title'] }}</h2>
                        @endif
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $section['body'] ?? '' }}</p>
                    </section>

                @elseif ($type === 'features')
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7">
                        @if (! empty($section['title']))
                            <h2 class="text-2xl font-black text-slate-900 mb-5">{{ $section['title'] }}</h2>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($section['items'] ?? [] as $item)
                                <div class="flex gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-11 h-11 rounded-xl bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center text-xl shrink-0">
                                        <i class="{{ $item['icon'] ?? 'ri-check-line' }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-900 mb-1">{{ $item['title'] ?? '' }}</h3>
                                        <p class="text-sm text-slate-600 leading-relaxed">{{ $item['body'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                @elseif ($type === 'comparison')
                    <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7">
                        @if (! empty($section['title']))
                            <h2 class="text-2xl font-black text-slate-900 mb-5">{{ $section['title'] }}</h2>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($section['columns'] ?? [] as $col)
                                @php $primary = ($col['tone'] ?? 'muted') === 'primary'; @endphp
                                <div class="rounded-xl p-5 border-2 {{ $primary ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-slate-200 bg-slate-50' }}">
                                    <h3 class="font-bold text-lg mb-3 {{ $primary ? 'text-[var(--color-primary)]' : 'text-slate-700' }}">
                                        {{ $col['title'] ?? '' }}
                                    </h3>
                                    <ul class="space-y-2.5">
                                        @foreach ($col['items'] ?? [] as $li)
                                            <li class="flex gap-2 text-sm text-slate-700">
                                                <i class="{{ $primary ? 'ri-check-line text-[var(--color-secondary)]' : 'ri-close-line text-slate-400' }} shrink-0 mt-0.5"></i>
                                                <span>{{ $li }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach

            @if (count($faqs) > 0)
                <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7">
                    <h2 class="text-2xl font-black text-slate-900 mb-5">Sıkça Sorulan Sorular</h2>
                    <div class="divide-y divide-slate-100">
                        @foreach ($faqs as $i => $faq)
                            <details class="py-4 group" @if ($i === 0) open @endif>
                                <summary class="cursor-pointer flex items-start justify-between gap-4 list-none">
                                    <h3 class="font-semibold text-slate-900 group-open:text-[var(--color-primary)]">{{ $faq['q'] ?? '' }}</h3>
                                    <i class="ri-arrow-down-s-line text-xl text-slate-400 group-open:rotate-180 transition-transform shrink-0"></i>
                                </summary>
                                <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($finalCta)
                <section class="rounded-2xl p-8 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-600)] text-white text-center">
                    <h2 class="text-2xl sm:text-3xl font-black mb-2">{{ $finalCta['title'] ?? '' }}</h2>
                    @if (! empty($finalCta['subtitle']))
                        <p class="text-white/85 mb-5 max-w-xl mx-auto">{{ $finalCta['subtitle'] }}</p>
                    @endif
                    @if (! empty($finalCta['button']))
                        <a href="{{ $finalCta['button']['href'] ?? '#' }}"
                            class="inline-flex items-center gap-2 bg-white text-[var(--color-primary)] hover:bg-slate-100 px-7 py-3.5 rounded-xl font-bold shadow-lg">
                            <i class="ri-phone-line"></i> {{ $finalCta['button']['label'] ?? 'Teklif al' }}
                        </a>
                    @endif
                </section>
            @endif
        </article>

        {{-- SIDEBAR --}}
        <aside class="lg:col-span-4 space-y-6">
            @if ($categories->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Kategoriler</h3>
                    <ul class="space-y-1">
                        @foreach ($categories as $cat)
                            <li class="flex items-center justify-between text-sm py-1.5">
                                <span class="text-slate-700 font-medium">{{ $cat->name }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $cat->pages_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($relatedPages->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">İlgili Sayfalar</h3>
                    <ul class="space-y-1">
                        @foreach ($relatedPages as $rp)
                            <li>
                                <a href="{{ route('public.pages.show', $rp->slug) }}"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-50 hover:text-[var(--color-primary)] transition-colors">
                                    <i class="ri-arrow-right-s-line text-slate-400"></i>
                                    <span class="truncate">{{ $rp->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl bg-[var(--color-primary)]/5 border border-[var(--color-primary)]/15 p-5">
                <h3 class="text-base font-black text-slate-900 mb-1">Vaktiniz yok mu?</h3>
                <p class="text-sm text-slate-600 mb-4">İletişim bilgilerinizi bırakın, sizi biz arayalım.</p>
                <a href="{{ route('we-call-you.create') }}"
                    class="inline-flex items-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-4 py-2.5 rounded-lg text-sm font-bold w-full justify-center">
                    <i class="ri-phone-line"></i> Beni arayın
                </a>
            </div>
        </aside>
    </div>
@endsection
