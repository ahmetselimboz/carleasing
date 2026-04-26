@php
    $isDash = request()->routeIs('dashboard');
    $isUsers = request()->routeIs('users.*');
    $isSite = request()->routeIs([
        'sliders.*',
        'home-service-tiles.*',
        'home-partners.*',
        'home-testimonials.*',
        'faqs.*',
    ]);
    $isReferences = request()->routeIs('references.*');
    $isPagesRoot = request()->routeIs([
        'pages.*',
        'page-categories.*',
    ]);
    $isFleet = request()->routeIs([
        'cars.*',
        'car-down-payments.*',
        'car-packages.*',
        'car-durations.*',
        'car-kilometer-options.*',
        'car-extra-services.*',
        'car-attribute-categories.*',
        'car-attributes.*',
        'car-attribute-values.*',
        'cars.price-matrices.*',
        'price-matrices.*',
    ]);
    $isOrders = request()->routeIs('rental-requests.*');
    $isSupport = request()->routeIs('messages.*');
    $isMenus = request()->routeIs('menus.*');
@endphp
<aside
    class="sidebar fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">
    <div class="flex flex-col h-full">
        <div class="p-5 border-b border-slate-100">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="h-14 w-14 rounded-md shadow-sm flex items-center justify-center overflow-hidden border border-slate-200">
                    <img src="{{ $site['favicon_url'] }}" alt="Logo" class="h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async">
                </div>
                <div>
                    <img src="{{ $site['logo_url'] }}" alt="Logo" class="h-10 w-auto drop-shadow-sm" loading="eager" fetchpriority="high" decoding="async">
                    <p class="text-xs text-slate-500">Yönetim Paneli</p>
                </div>
            </a>
        </div>
        <nav class="sidebar-nav flex-1 overflow-y-auto p-3 space-y-0.5">
            <a href="{{ route('dashboard') }}"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isDash ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                <i class="ri-dashboard-3-line text-xl"></i>
                <span>Anasayfa</span>
            </a>
            <div class="has-submenu {{ $isUsers ? 'open' : '' }}">
                <a href="{{ route('users.index') }}"
                    class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isUsers ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                    <i class="ri-user-3-line text-xl"></i>
                    <span>Kullanıcılar</span>
                    <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                </a>
                <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isUsers ? 'block' : 'hidden' }}">
                    <a href="{{ route('users.index') }}"
                        class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('users.index') || request()->routeIs('users.edit') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Tüm
                        kullanıcılar</a>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('users.create') }}"
                            class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('users.create') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Yeni
                            kullanıcı</a>
                    @endcan
                </div>
            </div>
            @can('viewAny', App\Models\RentalRequest::class)
            <div class="has-submenu {{ $isOrders ? 'open' : '' }}">
                <a href="#"
                    class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isOrders ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                    <i class="ri-shopping-cart-2-line text-xl"></i>
                    <span>Kiralama talepleri</span>
                    <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                </a>
                <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isOrders ? 'block' : 'hidden' }}">
                    <a href="{{ route('rental-requests.index') }}"
                        class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('rental-requests.*') && request()->input('status') !== 'pending' ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Tüm
                        talepler</a>
                    <a href="{{ route('rental-requests.index', ['status' => 'pending']) }}"
                        class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('rental-requests.*') && request()->input('status') === 'pending' ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Bekleyenler</a>
                </div>
            </div>
            @endcan
            @if (auth()->user()->can('viewAny', App\Models\Car::class))
                <div class="has-submenu {{ $isFleet ? 'open' : '' }}">
                    <a href="{{ route('cars.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isFleet ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <i class="ri-car-line text-xl"></i>
                        <span>Filo</span>
                        <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                    </a>
                    <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isFleet ? 'block' : 'hidden' }}">
                        @can('viewAny', App\Models\Car::class)
                            <a href="{{ route('cars.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('cars.*') && !request()->routeIs(['cars.price-matrices.*', 'price-matrices.*']) ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Araçlar</a>
                            <a href="{{ route('car-down-payments.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-down-payments.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Peşinat seçenekleri</a>
                            <a href="{{ route('car-packages.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-packages.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Paketler</a>
                            <a href="{{ route('car-durations.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-durations.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Süreler</a>
                            <a href="{{ route('car-kilometer-options.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-kilometer-options.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Km seçenekleri</a>
                            <a href="{{ route('car-extra-services.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-extra-services.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Ek hizmetler</a>
                            <a href="{{ route('car-attribute-categories.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-attribute-categories.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Özellik kategorileri</a>
                            <a href="{{ route('car-attributes.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-attributes.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Özellik adları</a>
                            <a href="{{ route('car-attribute-values.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('car-attribute-values.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Özellik değerleri</a>
                        @endcan
                    </div>
                </div>
            @endif
            @if (auth()->user()->can('viewAny', App\Models\Slider::class))
                <div class="has-submenu {{ $isSite ? 'open' : '' }}">
                    <a href="{{ route('sliders.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isSite ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <i class="ri-layout-masonry-line text-xl"></i>
                        <span>Ana sayfa içerik</span>
                        <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                    </a>
                    <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isSite ? 'block' : 'hidden' }}">
                        @can('viewAny', App\Models\Slider::class)
                            <a href="{{ route('sliders.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('sliders.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Hero slayt</a>
                        @endcan
                        @can('viewAny', App\Models\HomeServiceTile::class)
                            <a href="{{ route('home-service-tiles.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('home-service-tiles.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Hizmet kutuları</a>
                        @endcan
                        @can('viewAny', App\Models\HomePartner::class)
                            <a href="{{ route('home-partners.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('home-partners.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Partnerler</a>
                        @endcan
                        @can('viewAny', App\Models\HomeTestimonial::class)
                            <a href="{{ route('home-testimonials.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('home-testimonials.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">Yorumlar</a>
                        @endcan
                        @can('viewAny', App\Models\Faq::class)
                            <a href="{{ route('faqs.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('faqs.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">SSS</a>
                        @endcan
                    </div>
                </div>
            @endif
            @can('viewAny', App\Models\Reference::class)
                <a href="{{ route('references.index') }}"
                    class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isReferences ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                    <i class="ri-award-line text-xl"></i>
                    <span>Referanslar</span>
                </a>
            @endcan
            @if (auth()->user()->can('viewAny', App\Models\Page::class) || auth()->user()->can('viewAny', App\Models\PageCategory::class))
                <div class="has-submenu {{ $isPagesRoot ? 'open' : '' }}">
                    <a href="{{ route('pages.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isPagesRoot ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <i class="ri-file-list-3-line text-xl"></i>
                        <span>Sayfalar</span>
                        <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                    </a>
                    <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isPagesRoot ? 'block' : 'hidden' }}">
                        @can('viewAny', App\Models\Page::class)
                            <a href="{{ route('pages.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('pages.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                                Sayfalar
                            </a>
                        @endcan
                        @can('viewAny', App\Models\PageCategory::class)
                            <a href="{{ route('page-categories.index') }}"
                                class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('page-categories.*') ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                                Sayfa kategorileri
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
            <a href="#"
                class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-soft">
                <i class="ri-bar-chart-box-line text-xl"></i>
                <span>Raporlar</span>
            </a>
            @can('viewAny', App\Models\Message::class)
                <div class="has-submenu {{ $isSupport ? 'open' : '' }}">
                    <a href="{{ route('messages.index') }}"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl transition-soft {{ $isSupport ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }}">
                        <i class="ri-customer-service-2-line text-xl"></i>
                        <span>Destek</span>
                        <i class="ri-arrow-down-s-line ml-auto text-lg submenu-chevron"></i>
                    </a>
                    <div class="submenu pl-4 mt-1 space-y-0.5 {{ $isSupport ? 'block' : 'hidden' }}">
                        <a href="{{ route('messages.index') }}"
                            class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('messages.*') && request()->input('status') !== 'pending' ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            Tüm mesajlar
                        </a>
                        <a href="{{ route('messages.index', ['status' => 'pending']) }}"
                            class="block px-4 py-2 rounded-lg text-sm {{ request()->routeIs('messages.*') && request()->input('status') === 'pending' ? 'text-brand font-medium bg-[var(--color-brand-soft)]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            Bekleyenler
                        </a>
                    </div>
                </div>
            @endcan
         
            <div class="pt-4 mt-4 border-t border-slate-100 space-y-0.5">
                <a href="{{ route('menus.index') }}"
                    class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-soft {{ $isMenus ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : '' }}">
                    <i class="ri-menu-line text-xl"></i>
                    <span>Menüler</span>
                </a>
                <a href="{{ route('settings') }}"
                    class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-soft {{ request()->routeIs('settings') ? 'active text-brand bg-[var(--color-brand-soft)] font-medium' : '' }}">
                    <i class="ri-settings-3-line text-xl"></i>
                    <span>Ayarlar</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit"
                        class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-soft w-full text-left">
                        <i class="ri-logout-box-r-line text-xl"></i>
                        <span>Çıkış Yap</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</aside>
