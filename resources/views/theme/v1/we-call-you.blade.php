@extends('theme.v1.layout')

@section('meta')
    @include('theme.v1.components.meta', [
        'title' => 'Biz Sizi Arayalım',
        'description' => 'Numaranızı bırakın, kiralama uzmanlarımız size en kısa sürede dönsün.',
        'canonical' => route('we-call-you.create'),
        'noindex' => true,
    ])
@endsection

@section('content')
    <section class="pt-28 pb-16 px-6 bg-gradient-to-b from-[var(--color-surface)] to-white min-h-screen">
        <div class="max-w-3xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="text-xs text-slate-500 mb-6 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                @if ($car)
                    <a href="{{ route('cars.show', $car->slug) }}" class="hover:text-[var(--color-primary)] truncate max-w-xs">{{ $car->title }}</a>
                    <i class="ri-arrow-right-s-line text-slate-400"></i>
                @endif
                <span class="text-slate-700 font-medium">Biz sizi arayalım</span>
            </nav>

            @if (session('toast'))
                <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800 flex items-start gap-3 shadow-sm">
                    <i class="ri-checkbox-circle-fill text-2xl shrink-0"></i>
                    <div>
                        <p class="font-bold">{{ session('toast.title') }}</p>
                        <p class="text-sm">{{ session('toast.message') }}</p>
                    </div>
                </div>
            @endif

            <div class="rounded-3xl bg-white border border-slate-200 shadow-xl overflow-hidden">
                {{-- Header --}}
                <div class="p-8 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-primary-600)] text-white">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-xs font-bold uppercase tracking-widest mb-3">
                        <i class="ri-phone-line"></i> Geri arama talebi
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Vaktiniz yok mu? Sizi arayalım</h1>
                    <p class="text-white/85 text-sm mt-2">İletişim bilgilerinizi bırakın, ekibimiz en kısa sürede sizinle iletişime geçsin.</p>
                </div>

                {{-- Selected car --}}
                @if ($car)
                    <div class="px-8 py-5 bg-slate-50 border-b border-slate-100 flex items-center gap-4">
                        <div class="w-20 h-14 rounded-lg overflow-hidden bg-slate-200 shrink-0">
                            @if ($url = $car->displayImageUrl())
                                <img src="{{ $url }}" alt="{{ $car->title }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line"></i></div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] uppercase tracking-wide text-slate-500">İlgilendiğiniz araç</p>
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $car->title }}</p>
                        </div>
                        <a href="{{ route('cars.show', $car->slug) }}" class="text-xs font-semibold text-[var(--color-primary)] hover:underline shrink-0">
                            Detaya dön
                        </a>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('we-call-you.store') }}" class="p-8 space-y-5">
                    @csrf
                    @if ($car)
                        <input type="hidden" name="car_slug" value="{{ $car->slug }}">
                    @endif
                    @foreach (['package_id', 'duration_id', 'kilometer_id', 'down_payment_id'] as $cfgKey)
                        @if (! empty($config[$cfgKey]))
                            <input type="hidden" name="{{ $cfgKey }}" value="{{ $config[$cfgKey] }}">
                        @endif
                    @endforeach

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Ad *</label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Soyad</label>
                            <input type="text" name="surname" value="{{ old('surname') }}"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Telefon *</label>
                            <input type="tel" name="phone_number" required value="{{ old('phone_number') }}" placeholder="0 (5__) ___ __ __"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm">
                            @error('phone_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">E-posta</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm">
                            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Şehir</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">
                                Aranmak istediğiniz zaman
                            </label>
                            <select name="preferred_time"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm bg-white">
                                <option value="">Fark etmez</option>
                                @foreach ([
                                    'Sabah (09:00 - 12:00)',
                                    'Öğle (12:00 - 14:00)',
                                    'Öğleden sonra (14:00 - 18:00)',
                                    'Akşam (18:00 - 20:00)',
                                ] as $opt)
                                    <option value="{{ $opt }}" @selected(old('preferred_time') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Notunuz</label>
                            <textarea name="note" rows="3" placeholder="İletmek istediğiniz mesaj (opsiyonel)"
                                class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-4 py-3 text-sm resize-y">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <label class="flex items-start gap-2.5 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="kvkk" value="1" required class="mt-0.5 w-4 h-4 accent-[var(--color-primary)]">
                        <span>
                            <a href="#" class="text-[var(--color-primary)] hover:underline">KVKK aydınlatma metnini</a>
                            okudum, iletişim bilgilerimin geri arama amacıyla işlenmesini kabul ediyorum.
                        </span>
                    </label>
                    @error('kvkk') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-100">
                        <p class="text-xs text-slate-500">* zorunlu alanlar</p>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25 hover:shadow-xl hover:shadow-[var(--color-primary)]/40">
                            <i class="ri-phone-fill"></i> Beni arayın
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
