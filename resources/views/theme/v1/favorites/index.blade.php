@extends('theme.v1.layout')

@section('meta')
    @include('theme.v1.components.meta', [
        'title' => 'Listem',
        'description' => 'Karşılaştırmak için listeye eklediğiniz uzun dönem kiralama araçları.',
        'canonical' => route('favorites.index'),
        'noindex' => true,
    ])
@endsection

@section('content')
    @php
        $steps = [
            1 => ['Araç listesi', 'ri-car-line', 'Listendeki araçları kontrol et'],
            2 => ['İletişim', 'ri-user-3-line', 'Sizinle nasıl iletişime geçelim'],
            3 => ['Onay & gönder', 'ri-checkbox-circle-line', 'Firma & talep bilgileri'],
        ];
        $errorStep = 1;
        if ($errors->any()) {
            $personalKeys = ['name', 'surname', 'email', 'phone_number', 'city', 'district'];
            $companyKeys = ['requested_car_count', 'company_total_car_count', 'tax_office', 'tax_number_or_tckn', 'kvkk', 'cart_items'];
            foreach ($errors->keys() as $k) {
                if (in_array($k, $companyKeys, true)) { $errorStep = 3; break; }
                if (in_array($k, $personalKeys, true)) { $errorStep = max($errorStep, 2); }
            }
        }
    @endphp

    {{-- ======================= HERO STRIP ======================= --}}
    <section class="pt-28 pb-8 px-6 bg-gradient-to-br from-[var(--color-primary)] via-[var(--color-primary-600)] to-[var(--color-primary)] relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 pointer-events-none"
            style="background-image: radial-gradient(circle at 15% 30%, rgba(255,255,255,0.25), transparent 45%), radial-gradient(circle at 85% 80%, rgba(255,255,255,0.2), transparent 40%);"></div>

        <div class="relative max-w-7xl mx-auto">
            <nav class="text-xs text-white/70 mb-6 flex flex-wrap items-center gap-1.5">
                <a href="{{ route('home') }}" class="hover:text-white">Ana sayfa</a>
                <i class="ri-arrow-right-s-line text-white/50"></i>
                <span class="text-white font-semibold">Listem</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 text-white">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-xs font-bold uppercase tracking-widest mb-3">
                        <i class="ri-list-check-2"></i> Liste
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Listem</h1>
                    <p class="text-white/80 text-sm sm:text-base mt-2 max-w-xl">
                        Listende olan araçları görüntüle ve kiralama talebi gönder.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-white/80">
                    <i class="ri-shield-check-line text-emerald-300 text-base"></i>
                    KVKK kapsamında güvenli işlem
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= STEPPER ======================= --}}
    <div class="bg-white border-b border-slate-200  shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-5">
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
            <div class="sm:hidden text-center mt-3">
                <p class="text-xs uppercase tracking-wide text-slate-500">Adım <span id="mobile-step-num">{{ $errorStep }}</span> / 3</p>
                <p class="text-sm font-bold text-[var(--color-primary)]" id="mobile-step-label">{{ $steps[$errorStep][0] }}</p>
            </div>
        </div>
    </div>

    {{-- ======================= MAIN ======================= --}}
    <section class="px-6 py-10 bg-[var(--color-surface)] min-h-screen">
        <div class="max-w-7xl mx-auto">

            {{-- ============ EMPTY STATE ============ --}}
            <div id="favorites-empty" class="hidden">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm p-12 text-center max-w-2xl mx-auto">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 text-slate-500 mb-4">
                        <i class="ri-list-check-2 text-3xl"></i>
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mb-2">Listende henüz araç yok</h2>
                    <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">
                        Araç detay sayfalarında <strong>"Listeye ekle"</strong> ile aracı listeye al, tek talepte birden fazla araç gönder.
                    </p>
                    <a href="{{ route('home') }}#filo"
                        class="inline-flex items-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-6 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25">
                        <i class="ri-car-line"></i> Araçları incele
                    </a>
                </div>
            </div>

            {{-- ============ MAIN GRID (visible when has items) ============ --}}
            <div id="favorites-main" class="hidden lg:grid lg:grid-cols-12 gap-6">

                {{-- LEFT: Step content --}}
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

                    {{-- ======= STEP 1: CARS GRID ======= --}}
                    <section data-step="1" class="@if ($errorStep !== 1) hidden @endif step-section">
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <header class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                                <span class="w-11 h-11 inline-flex items-center justify-center rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                    <i class="ri-car-line text-xl"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900">Listendeki araçlar</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Her araç için yapılandırmasını ayarla — fiyatlar anlık güncellenir.</p>
                                </div>
                                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-xs font-bold">
                                    <i class="ri-shopping-bag-3-line"></i> <span data-cart-count>0</span> araç
                                </span>
                            </header>

                            <div id="favorites-grid" class="p-5 sm:p-6 grid grid-cols-1 xl:grid-cols-2 gap-5"></div>

                            <div class="px-6 sm:px-8 py-5 bg-slate-50/60 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <a href="{{ route('home') }}#filo"
                                    class="inline-flex items-center justify-center gap-2 text-slate-600 hover:text-[var(--color-primary)] text-sm font-semibold transition-colors">
                                    <i class="ri-arrow-left-line"></i> Filoya dön / araç ekle
                                </a>
                                <button type="button" data-next="2"
                                    class="inline-flex items-center justify-center gap-2 bg-[var(--color-primary)] hover:bg-[var(--color-primary-600)] text-white px-7 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-[var(--color-primary)]/25 hover:shadow-xl hover:shadow-[var(--color-primary)]/40">
                                    Araçları onayla, devam et <i class="ri-arrow-right-line"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    {{-- ======= FORM (Steps 2 & 3) ======= --}}
                    <form id="list-request-form" method="POST" action="{{ route('list.request.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="cart_items" id="cart-items-field">

                        {{-- ======= STEP 2: PERSONAL ======= --}}
                        <section data-step="2" class="@if ($errorStep !== 2) hidden @endif step-section">
                            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                                <header class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center gap-3">
                                    <span class="w-11 h-11 inline-flex items-center justify-center rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                                        <i class="ri-user-3-line text-xl"></i>
                                    </span>
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-black text-slate-900">İletişim bilgileri</h2>
                                        <p class="text-xs text-slate-500 mt-0.5">Yetkili kişinin bilgilerini paylaşın.</p>
                                    </div>
                                </header>

                                <div class="p-6 sm:p-8 space-y-5">
                                    @php
                                        $inputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none px-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                        $iconInputClass = 'w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/15 focus:outline-none pl-11 pr-4 py-3 text-sm transition-all bg-white placeholder:text-slate-400';
                                    @endphp

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                                    <div class="flex items-start gap-3 rounded-xl bg-emerald-50 border border-emerald-200 p-3.5">
                                        <i class="ri-shield-check-fill text-emerald-600 text-lg shrink-0"></i>
                                        <p class="text-xs text-emerald-900 leading-relaxed">
                                            Bilgileriniz <strong>KVKK kapsamında</strong> korunur, sadece teklif iletişimi için kullanılır.
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

                        {{-- ======= STEP 3: COMPANY + SUBMIT ======= --}}
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
                                                <input type="number" name="requested_car_count" min="1" max="9999" value="{{ old('requested_car_count') }}" class="{{ $iconInputClass }}">
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

                                    @if ($errors->has('cart_items'))
                                        <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-xs text-red-700 flex items-start gap-2">
                                            <i class="ri-error-warning-line text-base"></i>
                                            <span>{{ $errors->first('cart_items') }}</span>
                                        </div>
                                    @endif

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
                    </form>
                </div>

                {{-- ============ RIGHT: STICKY SUMMARY ============ --}}
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-32 space-y-4">
                        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                            <header class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="ri-shopping-bag-3-line text-[var(--color-primary)]"></i>
                                    <span class="text-sm font-bold text-slate-800">Talep özeti</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)] text-[10px] font-bold">
                                    <span data-cart-count>0</span> araç
                                </span>
                            </header>

                            <ul id="request-summary-list" class="divide-y divide-slate-100 max-h-[40vh] overflow-y-auto"></ul>

                            <div class="p-5 bg-gradient-to-br from-[var(--color-primary)]/5 via-transparent to-transparent border-t border-slate-100">
                                <p class="text-[10px] uppercase tracking-wide text-slate-500 mb-1">Tahmini toplam aylık (KDV hariç)</p>
                                <div class="flex items-baseline gap-1.5">
                                    <span id="cart-total-display" class="text-2xl sm:text-3xl font-black text-[var(--color-primary)] leading-none">—</span>
                                    <span class="text-xs font-semibold text-slate-500">TL / ay</span>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1">Konfigürasyon ve ek hizmet seçimine göre güncellenir.</p>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 space-y-3 hidden lg:block">
                            <p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold">Neden tek talep?</p>
                            <ul class="space-y-2.5 text-xs text-slate-600">
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-time-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Hızlı dönüş.</strong> Tek başvuru, çoklu araç değerlendirmesi.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-customer-service-2-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Tek danışman.</strong> Tüm araçlar için aynı kişiyle iletişim.</span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="ri-shield-check-line text-[var(--color-primary)] text-base shrink-0"></i>
                                    <span><strong class="text-slate-800">Güvenli.</strong> Bilgileriniz KVKK kapsamında korunur.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const LIST_KEY = 'carleasing:favorites';
            const carMap = @json($favoriteCarMap);
            const extraServices = @json($extraServices);
            const stepLabels = @json(collect($steps)->mapWithKeys(fn ($s, $k) => [$k => $s[0]]));

            const $grid = document.getElementById('favorites-grid');
            const $empty = document.getElementById('favorites-empty');
            const $main = document.getElementById('favorites-main');
            const $requestForm = document.getElementById('list-request-form');
            const $cartItemsField = document.getElementById('cart-items-field');
            const $summaryList = document.getElementById('request-summary-list');
            const $totalDisplay = document.getElementById('cart-total-display');
            const $countBadges = document.querySelectorAll('[data-cart-count]');
            const $progressBar = document.getElementById('stepper-progress');
            const $mobileNum = document.getElementById('mobile-step-num');
            const $mobileLabel = document.getElementById('mobile-step-label');
            const $stepper = document.getElementById('stepper');
            const $indicators = document.querySelectorAll('[data-step-indicator]');

            const fmtTL = new Intl.NumberFormat('tr-TR');
            const cartConfig = {};
            let maxReached = parseInt($stepper?.dataset.current || '1', 10);

            const getList = () => {
                try {
                    const parsed = JSON.parse(localStorage.getItem(LIST_KEY) || '[]');
                    return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
                } catch (err) {
                    return [];
                }
            };

            const setList = (items) => {
                localStorage.setItem(LIST_KEY, JSON.stringify(Array.from(new Set(items))));
                window.dispatchEvent(new CustomEvent('favorites:changed'));
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

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

            const buildCard = (car) => {
                const packagesHtml = (car.packages || []).map((pkg, idx) => `
                    <label class="block cursor-pointer rounded-xl border-2 border-slate-200 hover:border-[var(--color-primary)]/40 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 px-3 py-2 transition-all">
                        <input type="radio" name="cfg_package_${car.slug}" value="${pkg.id}" class="sr-only" ${idx === 0 ? 'checked' : ''}>
                        <span class="text-sm font-semibold text-slate-800">${escapeHtml(pkg.name)}</span>
                    </label>
                `).join('');
                const durationsHtml = (car.durations || []).map((dur, idx) => `
                    <label class="cursor-pointer text-center rounded-xl border-2 border-slate-200 hover:border-[var(--color-primary)]/40 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 px-2 py-2 transition-all">
                        <input type="radio" name="cfg_duration_${car.slug}" value="${dur.id}" class="sr-only" ${idx === 0 ? 'checked' : ''}>
                        <span class="block text-sm font-black text-slate-800">${dur.months}</span>
                        <span class="block text-[10px] uppercase tracking-wide text-slate-500">ay</span>
                    </label>
                `).join('');
                const kilometersHtml = (car.kilometers || []).map((km) =>
                    `<option value="${km.id}">${formatPrice(toNumber(km.kilometer))} km</option>`).join('');
                const downPaymentsHtml = (car.down_payments || []).map((dp) =>
                    `<option value="${dp.id}">${formatPrice(toNumber(dp.amount))} TL</option>`).join('');
                const extrasHtml = (extraServices || []).map((svc) => `
                    <label class="flex items-start gap-2 rounded-xl border border-slate-200 p-3 cursor-pointer hover:border-[var(--color-primary)]/35 has-[:checked]:border-[var(--color-primary)] has-[:checked]:bg-[var(--color-primary)]/5 transition-all">
                        <input type="checkbox" name="extras_${car.slug}[]" value="${svc.id}" data-extra-name="${escapeHtml(svc.name)}" data-extra-price="${svc.price ?? 0}" class="mt-0.5 w-4 h-4 accent-[var(--color-primary)]">
                        <span class="flex-1 text-xs text-slate-700">
                            <span class="block font-semibold text-slate-800">${escapeHtml(svc.name)}</span>
                            ${svc.price ? `<span class="text-[var(--color-primary)] font-bold">+${formatPrice(toNumber(svc.price))} TL</span>` : ''}
                        </span>
                    </label>
                `).join('');
                const specsHtml = Object.entries(car.spec_groups || {}).map(([cat, rows]) => `
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-slate-500 font-bold mb-1.5 flex items-center gap-1.5">
                            <span class="w-1 h-3 rounded-full bg-[var(--color-primary)]"></span>${escapeHtml(cat)}
                        </p>
                        <div class="space-y-1">
                            ${(rows || []).slice(0, 8).map((row) => `
                                <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-1 text-xs">
                                    <span class="text-slate-500">${escapeHtml(row.label)}</span>
                                    <span class="font-medium text-slate-800 text-right">${escapeHtml(row.value || '—')}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('');

                const wrapper = document.createElement('div');
                wrapper.className = 'flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:border-[var(--color-primary)]/30 transition-all';
                wrapper.innerHTML = `
                    <div class="relative">
                        <a href="${car.url}" class="block aspect-[16/10] bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
                            ${car.image_url
                                ? `<img src="${car.image_url}" alt="${escapeHtml(car.title)}" class="w-full h-full object-cover" loading="lazy" decoding="async">`
                                : `<div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line text-4xl"></i></div>`}
                        </a>
                        <button type="button" data-remove-favorite="${car.slug}"
                            class="absolute top-3 right-3 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/95 backdrop-blur-md border border-white shadow-md text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-all"
                            title="Listeden çıkar">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <a href="${car.url}" class="block">
                            <h3 class="text-base font-black text-slate-900 leading-snug hover:text-[var(--color-primary)] transition-colors">${escapeHtml(car.title)}</h3>
                        </a>
                        <div class="flex items-baseline justify-between gap-2 mt-2 mb-3">
                            <span class="text-[10px] uppercase tracking-wide text-slate-500">Aylık (KDV hariç)</span>
                            <div class="flex items-baseline gap-1">
                                <span data-list-price class="text-lg font-black text-[var(--color-primary)] leading-none">${escapeHtml(car.price || 'Teklif alın')}</span>
                                <span class="text-[10px] font-semibold text-slate-500">TL</span>
                            </div>
                        </div>

                        <details class="rounded-xl border border-slate-200 bg-slate-50/60 mt-auto group" data-offer-root data-car-slug="${car.slug}">
                            <summary class="cursor-pointer list-none flex items-center justify-between gap-2 px-4 py-3 hover:bg-slate-100/50 transition-colors rounded-xl">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <i class="ri-equalizer-line text-[var(--color-primary)]"></i> Yapılandırma
                                </span>
                                <i class="ri-arrow-down-s-line text-slate-500 group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <div class="p-4 border-t border-slate-200 space-y-3" data-offer-form>
                                ${(packagesHtml || durationsHtml || kilometersHtml || downPaymentsHtml) ? `
                                    <div class="grid grid-cols-1 gap-3">
                                        ${packagesHtml ? `<div><p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-1.5">Paket</p><div class="space-y-1.5">${packagesHtml}</div></div>` : ''}
                                        ${durationsHtml ? `<div><p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-1.5">Vade</p><div class="grid grid-cols-3 gap-1.5">${durationsHtml}</div></div>` : ''}
                                        ${kilometersHtml ? `<div><p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-1.5">Yıllık km</p><select name="cfg_kilometer_${car.slug}" class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-3 py-2 text-xs font-semibold bg-white">${kilometersHtml}</select></div>` : ''}
                                        ${downPaymentsHtml ? `<div><p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-1.5">Depozito</p><select name="cfg_down_payment_${car.slug}" class="w-full rounded-xl border-2 border-slate-200 focus:border-[var(--color-primary)] focus:outline-none px-3 py-2 text-xs font-semibold bg-white">${downPaymentsHtml}</select></div>` : ''}
                                    </div>` : ''}
                                ${extrasHtml ? `<div><p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mb-1.5">Ek hizmetler</p><div class="grid grid-cols-1 gap-1.5">${extrasHtml}</div></div>` : ''}
                                ${specsHtml ? `<details class="rounded-lg border border-slate-200 bg-white"><summary class="cursor-pointer list-none px-3 py-2 text-xs font-bold text-slate-700 flex items-center justify-between"><span class="flex items-center gap-2"><i class="ri-list-check-2 text-[var(--color-primary)]"></i> Araç özellikleri</span><i class="ri-arrow-down-s-line text-slate-400"></i></summary><div class="p-3 border-t border-slate-100 space-y-3">${specsHtml}</div></details>` : ''}
                                <div class="rounded-xl bg-[var(--color-primary)]/5 border border-[var(--color-primary)]/15 p-3 text-xs text-slate-700">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800">Tahmini aylık:</span>
                                        <span data-offer-summary-price class="font-black text-[var(--color-primary)]">${escapeHtml(car.price || 'Teklif alın')} TL</span>
                                    </div>
                                    <div class="mt-1 flex items-start gap-2">
                                        <span class="font-semibold text-slate-600 shrink-0">Ek hizmetler:</span>
                                        <span data-offer-summary-extras class="text-slate-500">—</span>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>
                `;
                initializeOfferFlow(wrapper, car);
                return wrapper;
            };

            const initializeOfferFlow = (wrapper, car) => {
                const root = wrapper.querySelector('[data-offer-root]');
                const form = wrapper.querySelector('[data-offer-form]');
                if (!root || !form) return;
                const slug = car.slug;
                const getCurrent = () => {
                    const getRadio = (name) => {
                        const el = form.querySelector(`[name="${name}_${slug}"]:checked`) || form.querySelector(`[name="${name}_${slug}"]`);
                        return el ? Number(el.value) : null;
                    };
                    const getSelect = (name) => {
                        const el = form.querySelector(`[name="${name}_${slug}"]`);
                        return el ? Number(el.value) : null;
                    };
                    return {
                        package_id: getRadio('cfg_package'),
                        duration_id: getRadio('cfg_duration'),
                        kilometer_id: getSelect('cfg_kilometer'),
                        down_payment_id: getSelect('cfg_down_payment'),
                    };
                };
                const findRow = (sel) => (car.matrix_rows || []).find((r) =>
                    (sel.package_id == null || Number(r.package_id) === sel.package_id) &&
                    (sel.duration_id == null || Number(r.duration_id) === sel.duration_id) &&
                    (sel.kilometer_id == null || Number(r.kilometer_id) === sel.kilometer_id) &&
                    (sel.down_payment_id == null || Number(r.down_payment_id) === sel.down_payment_id)
                ) || (car.matrix_rows || []).find((item) => toNumber(item.monthly_price) != null) || null;
                const getSelectedExtras = () => Array.from(form.querySelectorAll(`[name="extras_${slug}[]"]:checked`)).map((el) => ({
                    id: Number(el.value),
                    name: el.dataset.extraName || 'Ek hizmet',
                    price: toNumber(el.dataset.extraPrice) || 0,
                }));
                const update = () => {
                    const sel = getCurrent();
                    const row = findRow(sel);
                    const basePrice = row ? toNumber(row.monthly_price) : null;
                    const extras = getSelectedExtras();
                    const extrasTotal = extras.reduce((sum, item) => sum + item.price, 0);
                    const total = basePrice == null ? null : basePrice + extrasTotal;
                    const priceText = formatPrice(total);
                    const summaryPrice = form.querySelector('[data-offer-summary-price]');
                    const summaryExtras = form.querySelector('[data-offer-summary-extras]');
                    if (summaryPrice) summaryPrice.textContent = priceText + (total != null ? ' TL' : '');
                    if (summaryExtras) summaryExtras.textContent = extras.length ? extras.map((e) => e.name).join(', ') : '—';
                    cartConfig[slug] = {
                        slug,
                        package_id: sel.package_id,
                        duration_id: sel.duration_id,
                        kilometer_id: sel.kilometer_id,
                        down_payment_id: sel.down_payment_id,
                        extras: extras.map((item) => item.id),
                        title: car.title,
                        image_url: car.image_url,
                        price_text: priceText,
                        price_numeric: total,
                    };
                    updateListSummary();
                    const listPrice = wrapper.querySelector('[data-list-price]');
                    if (listPrice) listPrice.textContent = priceText;
                };
                form.querySelectorAll('input, select').forEach((el) => el.addEventListener('change', update));
                update();
            };

            const updateListSummary = () => {
                const list = getList();
                const items = list.map((slug) => cartConfig[slug]).filter(Boolean);

                if ($summaryList) {
                    $summaryList.innerHTML = items.length
                        ? items.map((item) => `
                            <li class="flex items-center gap-3 px-4 py-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 shrink-0">
                                    ${item.image_url
                                        ? `<img src="${item.image_url}" alt="${escapeHtml(item.title)}" class="w-full h-full object-cover">`
                                        : `<div class="flex h-full items-center justify-center text-slate-300"><i class="ri-image-2-line"></i></div>`}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 truncate leading-tight">${escapeHtml(item.title)}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5">aylık</p>
                                </div>
                                <span class="text-xs font-black text-[var(--color-primary)] whitespace-nowrap shrink-0">${escapeHtml(item.price_text || 'Teklif alın')}</span>
                            </li>
                        `).join('')
                        : '<li class="px-4 py-6 text-xs text-center text-slate-500">Listede araç yok.</li>';
                }

                $countBadges.forEach((el) => { el.textContent = items.length; });

                if ($totalDisplay) {
                    const total = items.reduce((sum, item) => sum + (item.price_numeric || 0), 0);
                    $totalDisplay.textContent = total > 0 ? formatPrice(total) : '—';
                }

                if ($cartItemsField) {
                    $cartItemsField.value = JSON.stringify(items);
                }
            };

            const showStep = (n) => {
                n = parseInt(n, 10);
                if (n > maxReached) maxReached = n;

                document.querySelectorAll('.step-section').forEach((s) => {
                    const match = parseInt(s.dataset.step, 10) === n;
                    s.classList.toggle('hidden', !match);
                    if (match) {
                        s.classList.add('animate-fade-in');
                        setTimeout(() => s.classList.remove('animate-fade-in'), 400);
                    }
                });

                $indicators.forEach((li) => {
                    const idx = parseInt(li.dataset.stepIndicator, 10);
                    const circle = li.querySelector('.step-circle');
                    const numEl = li.querySelector('.step-num');
                    const checkEl = li.querySelector('.step-check');

                    circle.classList.remove(
                        'bg-[var(--color-primary)]', 'border-[var(--color-primary)]', 'shadow-lg', 'shadow-[var(--color-primary)]/30', 'scale-110',
                        'bg-emerald-500', 'border-emerald-500',
                        'bg-white', 'border-slate-300', 'text-slate-400', 'text-white'
                    );

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

                if ($progressBar) $progressBar.style.width = ((n - 1) * 38) + '%';
                if ($mobileNum) $mobileNum.textContent = n;
                if ($mobileLabel) $mobileLabel.textContent = stepLabels[n] || '';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            const validateStep = (step) => {
                const section = document.querySelector(`.step-section[data-step="${step}"]`);
                if (!section) return true;
                const inputs = section.querySelectorAll('input[required], textarea[required], select[required]');
                let firstInvalid = null;
                inputs.forEach((input) => {
                    if (!input.checkValidity() && !firstInvalid) firstInvalid = input;
                });
                if (firstInvalid) {
                    firstInvalid.reportValidity();
                    return false;
                }
                return true;
            };

            const initStepNav = () => {
                document.querySelectorAll('[data-next]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const validate = btn.dataset.validateStep;
                        if (validate && !validateStep(validate)) return;
                        showStep(btn.dataset.next);
                    });
                });
                document.querySelectorAll('[data-prev]').forEach((btn) => {
                    btn.addEventListener('click', () => showStep(btn.dataset.prev));
                });
                $indicators.forEach((li) => {
                    li.addEventListener('click', () => {
                        const target = parseInt(li.dataset.goStep, 10);
                        if (target <= maxReached) showStep(target);
                    });
                });
                if ($requestForm) {
                    $requestForm.addEventListener('submit', (event) => {
                        updateListSummary();
                        const payload = JSON.parse($cartItemsField?.value || '[]');
                        if (!Array.isArray(payload) || payload.length === 0) {
                            event.preventDefault();
                            if (typeof window.toast === 'function') {
                                window.toast({
                                    type: 'warning',
                                    title: 'Liste boş',
                                    message: 'Talep için listede en az bir araç olmalı. Önce filodan araç ekleyin.',
                                });
                            } else {
                                alert('Talep için listede en az bir araç olmalı.');
                            }
                            return;
                        }
                        const submitBtn = $requestForm.querySelector('button[type="submit"]');
                        if (submitBtn && !submitBtn.disabled) {
                            submitBtn.disabled = true;
                            submitBtn.dataset.prevHtml = submitBtn.innerHTML;
                            submitBtn.innerHTML = '<i class="ri-loader-4-line inline-block animate-spin"></i> Gönderiliyor...';
                            if (typeof window.toast === 'function') {
                                window.toast({
                                    type: 'info',
                                    title: 'Gönderiliyor',
                                    message: 'Talebiniz iletilirken lütfen sayfayı kapatmayın.',
                                    duration: 4000,
                                });
                            }
                        }
                    });
                }
            };

            const render = () => {
                Object.keys(cartConfig).forEach((k) => delete cartConfig[k]);
                const list = getList();
                const items = list.map((slug) => carMap[slug]).filter(Boolean);
                $grid.innerHTML = '';
                if (items.length === 0) {
                    $empty.classList.remove('hidden');
                    $main.classList.add('hidden');
                    return;
                }
                $empty.classList.add('hidden');
                $main.classList.remove('hidden');
                items.forEach((car) => {
                    $grid.appendChild(buildCard(car));
                });
                updateListSummary();
            };

            $grid?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-favorite]');
                if (!button) return;
                const slug = button.getAttribute('data-remove-favorite');
                const next = getList().filter((item) => item !== slug);
                setList(next);
                render();
            });

            window.addEventListener('storage', render);
            window.addEventListener('favorites:changed', render);

            initStepNav();
            render();
        })();
    </script>
    <style>
        .animate-fade-in { animation: listFadeIn .35s ease-out; }
        @keyframes listFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush
