@extends('theme.v1.layout')

@section('meta')
    @include('theme.v1.components.meta', [
        'title' => $car->title.' — Teklif Gönder',
        'description' => $car->title.' için kiralama teklifi oluşturun.',
        'canonical' => route('cars.quote.create', $car->slug),
        'noindex' => true,
    ])
@endsection

@section('content')
    @php
        $extrasMonthly = $selectedExtras->sum(fn ($e) => (float) ($e->price ?? 0));
        $monthlyNumeric = is_numeric(preg_replace('/[^0-9.]/', '', (string) $monthlyPrice))
            ? (float) preg_replace('/[^0-9.]/', '', (string) $monthlyPrice)
            : null;
        $totalMonthly = $monthlyNumeric !== null ? ($monthlyNumeric + $extrasMonthly) : null;

        $errorStep = 1;
        if ($errors->any()) {
            $personalKeys = ['name', 'surname', 'email', 'phone_number', 'city', 'district'];
            $companyKeys = ['requested_car_count', 'company_total_car_count', 'tax_office', 'tax_number_or_tckn', 'kvkk'];
            foreach ($errors->keys() as $k) {
                if (in_array($k, $companyKeys, true)) { $errorStep = 3; break; }
                if (in_array($k, $personalKeys, true)) { $errorStep = max($errorStep, 2); }
            }
        }

        $steps = [
            1 => ['Yapılandırma', 'ri-car-line', 'Aracı ve seçimleri kontrol edin'],
            2 => ['İletişim', 'ri-user-3-line', 'Sizinle iletişime geçelim'],
            3 => ['Onay & gönder', 'ri-checkbox-circle-line', 'Talep firma bilgileri'],
        ];
    @endphp

    {{-- ======================= HERO STRIP ======================= --}}
    <section class="pt-28 pb-8 px-6 bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-primary-600)] to-[var(--color-primary)] relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
            style="background-image: radial-gradient(circle at 15% 30%, rgba(255,255,255,0.25), transparent 45%), radial-gradient(circle at 85% 80%, rgba(255,255,255,0.2), transparent 40%);"></div>

        <div class="relative max-w-6xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="text-xs text-white/70 mb-6 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-white">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-white/50"></i>
                <a href="{{ route('cars.show', $car->slug) }}" class="hover:text-white truncate max-w-xs">{{ $car->title }}</a>
                <i class="ri-arrow-right-s-line text-white/50"></i>
                <span class="text-white font-semibold">Teklif gönder</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 text-white">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-xs font-bold uppercase tracking-widest mb-3">
                        <i class="ri-mail-send-line"></i> Teklif talebi
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight">3 adımda talebinizi tamamlayın</h1>
                    <p class="text-white/80 text-sm sm:text-base mt-2 max-w-xl">Önce aracı ve yapılandırmayı kontrol edin, sonra iletişim bilgilerinizi paylaşın.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-white/80">
                    <i class="ri-shield-check-line text-emerald-300 text-base"></i>
                    KVKK kapsamında güvenli işlem
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= STEPPER ======================= --}}
    <div class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-5">
            <ol id="stepper" class="flex items-center justify-between gap-2 sm:gap-4 relative" data-current="{{ $errorStep }}">
                <div class="absolute top-5 left-[12%] right-[12%] h-0.5 bg-slate-200 rounded-full -z-0"></div>
                <div id="stepper-progress" class="absolute top-5 left-[12%] h-0.5 bg-[var(--color-primary)] rounded-full -z-0 transition-all duration-500"
                    style="width: {{ ($errorStep - 1) * 38 }}%"></div>

                @foreach ($steps as $idx => [$label, $icon, $desc])
                    <li data-step-indicator="{{ $idx }}"
                        class="step-indicator relative z-10 flex flex-col items-center gap-2 flex-1 cursor-pointer group" data-go-step="{{ $idx }}">
                        <span class="step-circle w-10 h-10 inline-flex items-center justify-center rounded-full font-black text-sm shrink-0 transition-all border-2
                            {{ $idx === $errorStep
                                ? 'bg-[var(--color-primary)] text-white border-[var(--color-primary)] shadow-lg shadow-[var(--color-primary)]/30 scale-110'
                                : ($idx < $errorStep
                                    ? 'bg-emerald-500 text-white border-emerald-500'
                                    : 'bg-white text-slate-400 border-slate-300') }}">
                            <span class="step-num {{ $idx < $errorStep ? 'hidden' : '' }}">{{ $idx }}</span>
                            <i class="ri-check-line step-check {{ $idx < $errorStep ? '' : 'hidden' }}"></i>
                        </span>
                        <div class="text-center min-w-0 hidden sm:block">
                            <p class="text-xs font-bold {{ $idx === $errorStep ? 'text-[var(--color-primary)]' : 'text-slate-700' }} truncate">{{ $label }}</p>
                            <p class="text-[10px] text-slate-500 hidden md:block truncate">{{ $desc }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            {{-- Mobile current step label --}}
            <div class="sm:hidden text-center mt-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Adım <span id="mobile-step-num">{{ $errorStep }}</span> / 3</p>
                <p class="text-sm font-bold text-[var(--color-primary)]" id="mobile-step-label">{{ $steps[$errorStep][0] }}</p>
            </div>
        </div>
    </div>

    {{-- ======================= MAIN ======================= --}}
    <section class="px-6 py-10 bg-[var(--color-surface)] min-h-screen">
        <div class="max-w-6xl mx-auto">
            <form method="POST" action="{{ route('cars.quote.store', $car->slug) }}" id="quote-form" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                @csrf

                {{-- Hidden config fields --}}
                <input type="hidden" name="package_id" value="{{ $package?->id }}">
                <input type="hidden" name="duration_id" value="{{ $duration?->id }}">
                <input type="hidden" name="kilometer_id" value="{{ $kilometer?->id }}">
                <input type="hidden" name="down_payment_id" value="{{ $downPayment?->id }}">
                @foreach ($selectedExtras as $svc)
                    <input type="hidden" name="extras[]" value="{{ $svc->id }}">
                @endforeach

                {{-- ====================== MAIN CONTENT ====================== --}}
                <div class="lg:col-span-8 space-y-6">
                    @if ($errors->any())
                        <div class="rounded-2xl bg-red-50 border border-red-200 px-5 py-4 text-red-800 flex items-start gap-3 shadow-sm">
                            <i class="ri-error-warning-fill text-xl shrink-0 text-red-500"></i>
                            <div>
                                <p class="font-bold text-sm">Lütfen hataları düzeltin</p>
                                <ul class="text-xs mt-1 list-disc list-inside space-y-0.5">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- ============== STEP 1: REVIEW ============== --}}
                    <section data-step="1" class="@if ($errorStep !== 1) hidden @endif step-section">
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <header class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                                <span class="w-11 h-11 inline-flex items-center justify-center rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                    <i class="ri-car-line text-xl"></i>
                                </span>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900">Aracı ve yapılandırmayı kontrol edin</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Bilgiler doğruysa aşağıdan devam edin.</p>
                                </div>
                            </header>

                            <div class="p-6 sm:p-8 space-y-6">
                                {{-- Car card --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                                    <div class="sm:col-span-1 aspect-[4/3] rounded-2xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shadow-sm">
                                        @if ($url = $car->displayImageUrl())
                                            <img src="{{ $url }}" alt="{{ $car->title }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line text-3xl"></i></div>
                                        @endif
                                    </div>
                                    <div class="sm:col-span-2">
                                        @if ($car->brand || $car->model)
                                            <p class="text-[11px] uppercase tracking-widest text-[var(--color-primary)] font-bold">
                                                {{ trim(($car->brand ?? '').' '.($car->model ?? '')) }}
                                            </p>
                                        @endif
                                        <h3 class="text-xl font-black text-slate-900 mt-1">{{ $car->title }}</h3>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach (array_filter([
                                                $car->fuel_type ? ['ri-gas-station-line', $car->fuel_type] : null,
                                                $car->transmission_type ? ['ri-settings-3-line', $car->transmission_type] : null,
                                                $car->body_type ? ['ri-car-line', $car->body_type] : null,
                                            ]) as [$icon, $value])
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-[11px] font-medium text-slate-700">
                                                    <i class="{{ $icon }} text-[var(--color-primary)]"></i> {{ $value }}
                                                </span>
                                            @endforeach
                                        </div>
                                        <a href="{{ route('cars.show', $car->slug) }}" class="text-xs text-[var(--color-primary)] font-semibold hover:underline mt-3 inline-flex items-center gap-1">
                                            <i class="ri-pencil-line"></i> Yapılandırmayı değiştir
                                        </a>
                                    </div>
                                </div>

                                {{-- Configuration grid --}}
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3 flex items-center gap-2">
                                        <i class="ri-equalizer-line text-[var(--color-primary)]"></i> Seçtiğiniz yapılandırma
                                    </p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach ([
                                            ['Paket', $package?->name ?: '—', 'ri-shield-check-line'],
                                            ['Vade', $duration?->months ? $duration->months.' ay' : '—', 'ri-calendar-line'],
                                            ['Yıllık km', $kilometer?->kilometer ? number_format((float) $kilometer->kilometer, 0, ',', '.').' km' : '—', 'ri-road-map-line'],
                                            ['Depozito', $downPayment?->amount ? number_format((float) $downPayment->amount, 0, ',', '.').' TL' : '—', 'ri-bank-card-line'],
                                        ] as [$lbl, $val, $ico])
                                            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4 hover:border-[var(--color-primary)]/30 transition-colors">
                                                <div class="flex items-center gap-1.5 text-[10px] uppercase tracking-wide text-slate-500 mb-1.5">
                                                    <i class="{{ $ico }} text-[var(--color-primary)]"></i> {{ $lbl }}
                                                </div>
                                                <p class="text-sm font-bold text-slate-800 truncate">{{ $val }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($package?->description)
                                        <div class="mt-3 rounded-xl bg-[var(--color-primary)]/5 border border-[var(--color-primary)]/15 p-3">
                                            <p class="text-[10px] uppercase tracking-wide text-[var(--color-primary)] font-bold mb-1">Paket detayı</p>
                                            <p class="text-xs text-slate-600 leading-relaxed">{{ $package->description }}</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Extras --}}
                                @if ($selectedExtras->isNotEmpty())
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 mb-3 flex items-center gap-2">
                                            <i class="ri-add-circle-line text-[var(--color-primary)]"></i> Eklenen ek hizmetler
                                        </p>
                                        <ul class="space-y-2">
                                            @foreach ($selectedExtras as $svc)
                                                <li class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 inline-flex items-center justify-center shrink-0">
                                                            <i class="ri-check-line"></i>
                                                        </span>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $svc->name }}</p>
                                                            @if ($svc->description)
                                                                <p class="text-[11px] text-slate-500 line-clamp-1">{{ $svc->description }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if ($svc->price)
                                                        <span class="shrink-0 text-sm font-bold text-[var(--color-primary)] whitespace-nowrap">
                                                            +{{ number_format((float) $svc->price, 0, ',', '.') }} TL
                                                        </span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Vehicle features --}}
                                @if (! empty($specGroups))
                                    <details class="rounded-2xl border border-slate-200 overflow-hidden group">
                                        <summary class="list-none flex items-center justify-between gap-3 px-5 py-4 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                                            <span class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                                <i class="ri-list-check-2 text-[var(--color-primary)]"></i> Aracın özellikleri
                                                <span class="text-xs font-normal text-slate-500">({{ collect($specGroups)->sum(fn ($r) => count($r)) }} özellik)</span>
                                            </span>
                                            <i class="ri-arrow-down-s-line text-slate-400 group-open:rotate-180 transition-transform"></i>
                                        </summary>
                                        <div class="p-5 space-y-5 border-t border-slate-100">
                                            @foreach ($specGroups as $catName => $rows)
                                                <div>
                                                    <p class="text-[11px] uppercase tracking-wide text-slate-500 font-bold mb-2 flex items-center gap-1.5">
                                                        <span class="w-1 h-3 rounded-full bg-[var(--color-primary)]"></span>
                                                        {{ $catName }}
                                                    </p>
                                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                                        @foreach ($rows as $row)
                                                            <div class="flex items-start justify-between gap-3 py-1.5 border-b border-slate-100 last:border-0">
                                                                <dt class="text-slate-500">{{ $row['label'] }}</dt>
                                                                <dd class="text-slate-800 font-medium text-right">{{ $row['value'] ?: '—' }}</dd>
                                                            </div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>

                            {{-- Step 1 footer --}}
                            <div class="px-6 sm:px-8 py-5 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <a href="{{ route('cars.show', $car->slug) }}"
                                    class="inline-flex items-center justify-center gap-2 text-slate-600 hover:text-[var(--color-primary)] text-sm font-semibold transition-colors">
                                    <i class="ri-arrow-left-line"></i> Detay sayfasına dön
                                </a>
                                <button type="button" data-next="2"
                                    class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-7 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25 hover:shadow-xl hover:shadow-[var(--color-primary)]/40">
                                    Onaylıyorum, devam et <i class="ri-arrow-right-line"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    {{-- ============== STEP 2: PERSONAL ============== --}}
                    <section data-step="2" class="@if ($errorStep !== 2) hidden @endif step-section">
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <header class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                                <span class="w-11 h-11 inline-flex items-center justify-center rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                    <i class="ri-user-3-line text-xl"></i>
                                </span>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900">İletişim bilgileri</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Yetkili kişinin bilgilerini paylaşın — sizinle bu bilgilerle iletişime geçeceğiz.</p>
                                </div>
                            </header>

                            <div class="p-6 sm:p-8 space-y-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @php
                                        $inputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none px-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                        $iconInputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none pl-11 pr-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                    @endphp

                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Ad <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i class="ri-user-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ahmet" class="{{ $iconInputClass }} @error('name') border-red-300 @enderror">
                                        </div>
                                        @error('name') <p class="text-xs text-red-600 mt-1 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Soyad <span class="text-red-500">*</span></label>
                                        <input type="text" name="surname" required value="{{ old('surname') }}" placeholder="Yılmaz" class="{{ $inputClass }} @error('surname') border-red-300 @enderror">
                                        @error('surname') <p class="text-xs text-red-600 mt-1 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">E-posta <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i class="ri-mail-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="ad@ornek.com" class="{{ $iconInputClass }} @error('email') border-red-300 @enderror">
                                        </div>
                                        @error('email') <p class="text-xs text-red-600 mt-1 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Telefon <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i class="ri-phone-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="tel" name="phone_number" required value="{{ old('phone_number') }}" placeholder="0 (5__) ___ __ __" class="{{ $iconInputClass }} @error('phone_number') border-red-300 @enderror">
                                        </div>
                                        @error('phone_number') <p class="text-xs text-red-600 mt-1 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Şehir</label>
                                        <div class="relative">
                                            <i class="ri-map-pin-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" name="city" value="{{ old('city') }}" placeholder="İstanbul" class="{{ $iconInputClass }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">İlçe</label>
                                        <input type="text" name="district" value="{{ old('district') }}" placeholder="Kadıköy" class="{{ $inputClass }}">
                                    </div>
                                </div>

                                {{-- Trust badge --}}
                                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5">
                                    <i class="ri-shield-check-fill text-emerald-600 text-lg shrink-0"></i>
                                    <p class="text-xs text-emerald-900 leading-relaxed">
                                        Bilgileriniz <strong>KVKK kapsamında</strong> korunur, sadece teklif iletişimi için kullanılır ve üçüncü taraflarla paylaşılmaz.
                                    </p>
                                </div>
                            </div>

                            <div class="px-6 sm:px-8 py-5 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <button type="button" data-prev="1"
                                    class="inline-flex items-center justify-center gap-2 text-slate-600 hover:text-[var(--color-primary)] text-sm font-semibold transition-colors">
                                    <i class="ri-arrow-left-line"></i> Geri
                                </button>
                                <button type="button" data-next="3" data-validate-step="2"
                                    class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-7 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25 hover:shadow-xl hover:shadow-[var(--color-primary)]/40">
                                    Devam et <i class="ri-arrow-right-line"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    {{-- ============== STEP 3: COMPANY + SUBMIT ============== --}}
                    <section data-step="3" class="@if ($errorStep !== 3) hidden @endif step-section">
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <header class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                                <span class="w-11 h-11 inline-flex items-center justify-center rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                    <i class="ri-building-line text-xl"></i>
                                </span>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900">Firma & talep bilgileri</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Bireysel başvuruysa firma alanlarını boş bırakabilirsiniz.</p>
                                </div>
                            </header>

                            <div class="p-6 sm:p-8 space-y-5">
                                @php
                                    $inputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none px-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                    $iconInputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none pl-11 pr-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                @endphp

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Talep edilen araç sayısı</label>
                                        <div class="relative">
                                            <i class="ri-car-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="number" name="requested_car_count" min="1" max="9999" value="{{ old('requested_car_count', 1) }}" class="{{ $iconInputClass }}">
                                        </div>
                                        @error('requested_car_count') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Mevcut filo (araç sayısı)</label>
                                        <div class="relative">
                                            <i class="ri-roadster-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="number" name="company_total_car_count" min="0" max="99999" value="{{ old('company_total_car_count') }}" placeholder="0" class="{{ $iconInputClass }}">
                                        </div>
                                        @error('company_total_car_count') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Vergi dairesi</label>
                                        <div class="relative">
                                            <i class="ri-government-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" name="tax_office" value="{{ old('tax_office') }}" placeholder="Örn: Kadıköy" class="{{ $iconInputClass }}">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Vergi no / TCKN</label>
                                        <div class="relative">
                                            <i class="ri-file-text-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input type="text" name="tax_number_or_tckn" value="{{ old('tax_number_or_tckn') }}" placeholder="11 / 10 haneli" class="{{ $iconInputClass }}">
                                        </div>
                                        @error('tax_number_or_tckn') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                                    <label class="flex items-start gap-3 text-sm text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="kvkk" value="1" required class="mt-0.5 w-5 h-5 accent-[var(--color-primary)] rounded" @checked(old('kvkk'))>
                                        <span class="flex-1 leading-relaxed">
                                            <a href="#" class="text-[var(--color-primary)] hover:underline font-semibold">KVKK aydınlatma metnini</a>
                                            okudum, iletişim bilgilerimin teklif amaçlı işlenmesini kabul ediyorum.
                                        </span>
                                    </label>
                                    @error('kvkk') <p class="text-xs text-red-600 mt-2 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="px-6 sm:px-8 py-5 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <button type="button" data-prev="2"
                                    class="inline-flex items-center justify-center gap-2 text-slate-600 hover:text-[var(--color-primary)] text-sm font-semibold transition-colors">
                                    <i class="ri-arrow-left-line"></i> Geri
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-emerald-600/25 hover:shadow-xl hover:shadow-emerald-600/40">
                                    <i class="ri-send-plane-fill"></i> Talebi gönder
                                </button>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- ====================== STICKY SUMMARY SIDEBAR ====================== --}}
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-32 space-y-4">
                        {{-- Car summary card --}}
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <div class="aspect-[16/10] bg-gradient-to-br from-slate-100 to-slate-200">
                                @if ($url = $car->displayImageUrl())
                                    <img src="{{ $url }}" alt="{{ $car->title }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line text-4xl"></i></div>
                                @endif
                            </div>
                            <div class="p-5 border-b border-slate-100">
                                @if ($car->brand || $car->model)
                                    <p class="text-[10px] uppercase tracking-widest text-[var(--color-primary)] font-bold">
                                        {{ trim(($car->brand ?? '').' '.($car->model ?? '')) }}
                                    </p>
                                @endif
                                <h3 class="text-base font-black text-slate-900 leading-tight mt-1">{{ $car->title }}</h3>
                            </div>

                            <dl class="divide-y divide-slate-100 text-sm">
                                @foreach ([
                                    ['Paket', $package?->name],
                                    ['Vade', $duration?->months ? $duration->months.' ay' : null],
                                    ['Yıllık km', $kilometer?->kilometer ? number_format((float) $kilometer->kilometer, 0, ',', '.').' km' : null],
                                    ['Depozito', $downPayment?->amount ? number_format((float) $downPayment->amount, 0, ',', '.').' TL' : null],
                                ] as [$lbl, $val])
                                    @if ($val)
                                        <div class="flex items-center justify-between gap-2 px-5 py-2.5">
                                            <dt class="text-xs text-slate-500">{{ $lbl }}</dt>
                                            <dd class="text-xs font-bold text-slate-800 text-right truncate">{{ $val }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                            </dl>

                            @if ($selectedExtras->isNotEmpty())
                                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/40">
                                    <p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-2">Ek hizmetler</p>
                                    <ul class="space-y-1">
                                        @foreach ($selectedExtras as $svc)
                                            <li class="flex items-center justify-between gap-2 text-xs">
                                                <span class="text-slate-600 truncate flex items-center gap-1.5">
                                                    <i class="ri-check-line text-emerald-500"></i> {{ $svc->name }}
                                                </span>
                                                @if ($svc->price)
                                                    <span class="font-bold text-slate-800 whitespace-nowrap">+{{ number_format((float) $svc->price, 0, ',', '.') }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="p-5 bg-gradient-to-br from-[var(--color-primary)]/5 via-transparent to-transparent border-t border-slate-100">
                                <p class="text-[10px] uppercase tracking-wide text-slate-500 mb-1">Tahmini aylık (KDV hariç)</p>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-2xl sm:text-3xl font-black text-[var(--color-primary)] leading-none">
                                        @if ($totalMonthly !== null)
                                            {{ number_format($totalMonthly, 0, ',', '.') }}
                                        @else
                                            {{ $monthlyPrice ?: 'Teklif' }}
                                        @endif
                                    </span>
                                    @if ($totalMonthly !== null || $monthlyPrice)
                                        <span class="text-xs font-semibold text-slate-500">TL / ay</span>
                                    @endif
                                </div>
                                @if ($selectedExtras->isNotEmpty() && $extrasMonthly > 0)
                                    <p class="text-[10px] text-slate-500 mt-1">+{{ number_format($extrasMonthly, 0, ',', '.') }} TL ek hizmet dahil</p>
                                @endif
                            </div>
                        </div>

                        {{-- Trust badges --}}
                        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 space-y-3 hidden lg:block">
                            <p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold">Neden bizi tercih edin?</p>
                            <ul class="space-y-2.5 text-xs text-slate-600">
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-time-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Hızlı dönüş.</strong> Talebiniz aynı gün içinde değerlendirilir.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-customer-service-2-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Uzman destek.</strong> Size özel danışman atanır.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-shield-check-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Şeffaf fiyat.</strong> Sürpriz ücret yoktur.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('quote-form');
            if (!form) return;

            const sections = form.querySelectorAll('[data-step]');
            const indicators = document.querySelectorAll('[data-step-indicator]');
            const progressBar = document.getElementById('stepper-progress');
            const mobileNum = document.getElementById('mobile-step-num');
            const mobileLabel = document.getElementById('mobile-step-label');
            const stepLabels = @json(collect($steps)->mapWithKeys(fn ($s, $k) => [$k => $s[0]]));

            // Track which steps the user has completed (forward-only)
            let maxReached = parseInt(document.getElementById('stepper').dataset.current || '1', 10);

            const showStep = (n) => {
                n = parseInt(n, 10);
                if (n > maxReached) maxReached = n;

                sections.forEach(s => {
                    const match = parseInt(s.dataset.step, 10) === n;
                    s.classList.toggle('hidden', !match);
                    if (match) {
                        s.classList.add('animate-fade-in');
                        setTimeout(() => s.classList.remove('animate-fade-in'), 400);
                    }
                });

                indicators.forEach(li => {
                    const idx = parseInt(li.dataset.stepIndicator, 10);
                    const circle = li.querySelector('.step-circle');
                    const numEl = li.querySelector('.step-num');
                    const checkEl = li.querySelector('.step-check');
                    const labelEl = li.querySelector('p.font-bold, p.text-xs.font-bold');

                    circle.classList.remove(
                        'bg-[var(--color-primary)]', 'border-[var(--color-primary)]', 'shadow-lg', 'shadow-[var(--color-primary)]/30', 'scale-110',
                        'bg-emerald-500', 'border-emerald-500',
                        'bg-white', 'border-slate-300', 'text-slate-400'
                    );
                    circle.classList.remove('text-white');

                    if (idx === n) {
                        circle.classList.add('bg-[var(--color-primary)]', 'text-white', 'border-[var(--color-primary)]', 'shadow-lg', 'shadow-[var(--color-primary)]/30', 'scale-110');
                        numEl?.classList.remove('hidden');
                        checkEl?.classList.add('hidden');
                    } else if (idx < n) {
                        circle.classList.add('bg-emerald-500', 'text-white', 'border-emerald-500');
                        numEl?.classList.add('hidden');
                        checkEl?.classList.remove('hidden');
                    } else {
                        circle.classList.add('bg-white', 'text-slate-400', 'border-slate-300');
                        numEl?.classList.remove('hidden');
                        checkEl?.classList.add('hidden');
                    }
                });

                if (progressBar) progressBar.style.width = ((n - 1) * 38) + '%';
                if (mobileNum) mobileNum.textContent = n;
                if (mobileLabel) mobileLabel.textContent = stepLabels[n] || '';
            };

            const validateStep = (step) => {
                const section = form.querySelector(`[data-step="${step}"]`);
                if (!section) return true;
                const inputs = section.querySelectorAll('input[required], textarea[required], select[required]');
                let firstInvalid = null;
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        if (!firstInvalid) firstInvalid = input;
                    }
                });
                if (firstInvalid) {
                    firstInvalid.reportValidity();
                    return false;
                }
                return true;
            };

            form.querySelectorAll('[data-next]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const validate = btn.dataset.validateStep;
                    if (validate && !validateStep(validate)) return;
                    showStep(btn.dataset.next);
                });
            });

            form.querySelectorAll('[data-prev]').forEach(btn => {
                btn.addEventListener('click', () => showStep(btn.dataset.prev));
            });

            // Click stepper to jump (but only to reached steps)
            indicators.forEach(li => {
                li.addEventListener('click', () => {
                    const target = parseInt(li.dataset.goStep, 10);
                    if (target <= maxReached) showStep(target);
                });
            });
        })();
    </script>
    <style>
        .animate-fade-in { animation: quoteFadeIn .35s ease-out; }
        @keyframes quoteFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush
