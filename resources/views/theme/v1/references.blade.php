@extends('theme.v1.layout')

@section('meta')
    @include('theme.v1.components.meta', [
        'title' => 'Referanslarımız',
        'description' => 'Bize güvenen kurumsal müşterilerimiz ve birlikte yürüttüğümüz filo kiralama projeleri.',
        'canonical' => route('public.references.index'),
        'ogType' => 'website',
    ])
@endsection

@push('jsonld')
    @php
        $homeUrl = route('home');
        $refUrl = route('public.references.index');
        $bcNode = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => $homeUrl],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Referanslar', 'item' => $refUrl],
            ],
        ];
        $listItems = ($references ?? collect())->values()->map(function ($ref, $idx) {
            return array_filter([
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'name' => $ref->name,
                'url' => $ref->website_url ?? null,
            ], fn ($v) => $v !== null);
        })->all();
        $payload = [
            '@context' => 'https://schema.org',
            '@graph' => [
                $bcNode,
                [
                    '@type' => 'ItemList',
                    '@id' => $refUrl.'#refs',
                    'name' => 'Kurumsal referanslar',
                    'itemListElement' => $listItems,
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="pt-28 pb-12 px-6 bg-gradient-to-b from-[var(--color-surface)] to-white">
        <div class="max-w-6xl mx-auto">
            <nav class="text-xs text-slate-500 mb-5 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <span class="text-slate-700 font-medium">Referanslar</span>
            </nav>

            <div class="rounded-3xl overflow-hidden bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-600)] text-white p-8 sm:p-12 shadow-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-xs font-bold uppercase tracking-widest mb-4">
                    <i class="ri-medal-line"></i> Bize güvenenler
                </span>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">Referanslarımız</h1>
                <p class="text-white/85 text-base sm:text-lg max-w-3xl">
                    Türkiye'nin önde gelen markaları ile uzun yıllara dayanan iş birliği. Filo kiralama ve filo yönetimi alanında binlerce araçlık operasyon.
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 text-center">
                    <div class="bg-white/10 rounded-xl p-4 border border-white/15">
                        <p class="text-2xl sm:text-3xl font-black">{{ $references->count() }}+</p>
                        <p class="text-xs uppercase tracking-widest text-white/80 mt-1">Kurumsal müşteri</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 border border-white/15">
                        <p class="text-2xl sm:text-3xl font-black">10K+</p>
                        <p class="text-xs uppercase tracking-widest text-white/80 mt-1">Aktif araç</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 border border-white/15">
                        <p class="text-2xl sm:text-3xl font-black">81</p>
                        <p class="text-xs uppercase tracking-widest text-white/80 mt-1">İlde hizmet</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4 border border-white/15">
                        <p class="text-2xl sm:text-3xl font-black">15+</p>
                        <p class="text-xs uppercase tracking-widest text-white/80 mt-1">Yıl deneyim</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 pb-16">
        <div class="max-w-6xl mx-auto">
            @if ($references->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center text-slate-500">
                    <i class="ri-building-2-line text-4xl mb-3 block"></i>
                    Henüz eklenmiş referans bulunmuyor.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($references as $ref)
                        <article class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-[var(--color-primary)]/30 transition-all p-6 flex flex-col">
                            <div class="flex items-start gap-4 mb-4">
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-primary)]/5 flex items-center justify-center text-[var(--color-primary)] text-2xl font-black shrink-0 overflow-hidden">
                                    @if (! empty($ref->image))
                                        <img src="{{ asset($ref->image) }}" alt="{{ $ref->name }}"
                                            class="w-full h-full object-contain p-1" loading="lazy">
                                    @else
                                        {{ mb_strtoupper(mb_substr($ref->name ?? '?', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-black text-slate-900 truncate group-hover:text-[var(--color-primary)] transition-colors">
                                        {{ $ref->name }}
                                    </h3>
                                    @if (! empty($ref->title))
                                        <p class="text-xs uppercase tracking-wide text-slate-500 mt-0.5 truncate">{{ $ref->title }}</p>
                                    @endif
                                </div>
                            </div>

                            @if (! empty($ref->detail))
                                <p class="text-sm text-slate-600 leading-relaxed line-clamp-3 mb-4 flex-1">{{ $ref->detail }}</p>
                            @endif

                            @if (! empty($ref->link))
                                <a href="{{ $ref->link }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[var(--color-primary)] hover:underline mt-auto">
                                    Web sitesine git <i class="ri-external-link-line"></i>
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="mt-12 rounded-2xl p-8 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-600)] text-white text-center">
                <h2 class="text-2xl sm:text-3xl font-black mb-2">Siz de aramıza katılın</h2>
                <p class="text-white/85 mb-5 max-w-xl mx-auto">Filonuza özel teklif birkaç dakika içinde hazırlanır.</p>
                <a href="{{ route('we-call-you.create') }}"
                    class="inline-flex items-center gap-2 bg-white text-[var(--color-primary)] hover:bg-slate-100 px-7 py-3.5 rounded-xl font-bold shadow-lg">
                    <i class="ri-phone-line"></i> Beni arayın
                </a>
            </div>
        </div>
    </section>
@endsection
