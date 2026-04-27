@extends('theme.v1.layout')

@section('meta')
    <title>Favorilerim — {{ $site['title'] ?? config('app.name') }}</title>
@endsection

@section('content')
    <section class="pt-28 pb-14 px-6 bg-gradient-to-b from-[var(--color-surface)] to-white min-h-[60vh]">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
                <div>
                    <p class="text-xs uppercase tracking-wide text-[var(--color-primary)] font-bold mb-1">Favoriler</p>
                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900">Favori araclarim</h1>
                </div>
                <a href="{{ route('home') }}#filo"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:border-[var(--color-primary)]/35 hover:text-[var(--color-primary)] transition-all">
                    <i class="ri-arrow-left-line"></i> Filoya don
                </a>
            </div>

            @if ($favorites->isEmpty())
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm p-10 text-center">
                    <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 mb-4">
                        <i class="ri-heart-line text-2xl"></i>
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Henuz favori arac yok</h2>
                    <p class="text-sm text-slate-500 mb-6">Begendiginiz araclari favorilere ekleyerek bu listede saklayabilirsiniz.</p>
                    <a href="{{ route('home') }}#filo"
                        class="inline-flex items-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-5 py-3 rounded-xl font-bold transition-all">
                        <i class="ri-car-line"></i> Araclari incele
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($favorites as $favorite)
                        @php
                            $car = $favorite->car;
                            $priceContext = $car?->displayMonthlyPriceContext();
                        @endphp
                        @if ($car)
                            <div class="flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                                <a href="{{ route('cars.show', $car->slug) }}"
                                    class="relative aspect-[4/3] bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                                    @if ($url = $car->displayImageUrl())
                                        <img src="{{ $url }}" alt="{{ $car->title }}" class="w-full h-full object-cover"
                                            loading="lazy" decoding="async">
                                    @else
                                        <div class="flex h-full items-center justify-center text-slate-300">
                                            <i class="ri-image-2-line text-4xl"></i>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-4 flex-1 flex flex-col">
                                    <h2 class="text-base font-bold text-slate-900 leading-snug mb-2">{{ $car->title }}</h2>
                                    @if ($priceContext)
                                        <p class="text-sm text-slate-500 mb-4">
                                            Aylik
                                            <span class="font-black text-[var(--color-primary)]">{{ $priceContext['price'] }}</span>
                                        </p>
                                    @else
                                        <p class="text-sm text-slate-500 mb-4">Fiyat icin teklif alin</p>
                                    @endif

                                    <div class="mt-auto flex items-center gap-2">
                                        <a href="{{ route('cars.show', $car->slug) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:border-[var(--color-primary)]/35 hover:text-[var(--color-primary)] transition-all">
                                            Detaya git <i class="ri-arrow-right-line"></i>
                                        </a>
                                        <form method="POST" action="{{ route('favorites.destroy', $car->slug) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-11 h-11 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 transition-all"
                                                title="Favorilerden kaldir">
                                                <i class="ri-heart-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
