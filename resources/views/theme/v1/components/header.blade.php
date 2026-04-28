@php
    $siteTitle = $site['title'] ?? config('app.name');
    $mb = $site['magicbox'] ?? [];
    $socialNav = [
        'facebook' => ['label' => 'Facebook', 'icon' => 'ri-facebook-fill'],
        'twitter' => ['label' => 'X (Twitter)', 'icon' => 'ri-twitter-x-fill'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'ri-instagram-line'],
        'linkedin' => ['label' => 'LinkedIn', 'icon' => 'ri-linkedin-fill'],
        'youtube' => ['label' => 'YouTube', 'icon' => 'ri-youtube-fill'],
        'tiktok' => ['label' => 'TikTok', 'icon' => 'ri-tiktok-fill'],
    ];

    $buildMenuTree = function (array $rows): array {
        $tree = [];
        $groupIndex = [];
        foreach ($rows as $row) {
            $type = $row['type'] ?? 'custom';
            $label = trim((string) ($row['label'] ?? ''));

            if ($type === 'group') {
                if ($label === '' || isset($groupIndex[$label])) continue;
                $tree[] = ['type' => 'group', 'label' => $label, 'children' => []];
                $groupIndex[$label] = array_key_last($tree);
                continue;
            }

            $url = trim((string) ($row['url'] ?? ''));
            $parent = trim((string) ($row['parent'] ?? ''));
            if ($label === '' && $url === '') continue;
            $item = ['type' => $type, 'label' => $label, 'url' => $url];

            if ($parent !== '') {
                if (! isset($groupIndex[$parent])) {
                    $tree[] = ['type' => 'group', 'label' => $parent, 'children' => []];
                    $groupIndex[$parent] = array_key_last($tree);
                }
                $tree[$groupIndex[$parent]]['children'][] = $item;
            } else {
                $tree[] = ['type' => 'item', 'label' => $label, 'url' => $url];
            }
        }
        return $tree;
    };

    $navbarTree = $buildMenuTree(data_get($mb, 'menus.navbar', []));
@endphp
<nav class="fixed top-0 w-full z-50 px-6 py-4 h-18">
    <div class="max-w-7xl mx-auto glass rounded-xl px-6 py-3 flex items-center justify-between shadow-lg">
        <a href="{{ route('home') }}" class="flex items-center gap-2 h-12">
            <img src="{{ $site['logo_url'] }}" alt="{{ $siteTitle }}" class="h-full w-auto " loading="eager"
                fetchpriority="high" decoding="async">
        </a>
        <div class="hidden md:flex items-center gap-8">
            @if (empty($navbarTree))
                <a class="text-sm font-semibold hover:text-primary transition-colors" href="{{ route('home') }}">Ana sayfa</a>
            @else
                @foreach ($navbarTree as $node)
                    @if ($node['type'] === 'group')
                        <div class="relative group">
                            <button type="button"
                                class="text-sm font-semibold hover:text-primary transition-colors inline-flex items-center gap-1">
                                {{ $node['label'] }}
                                <i class="ri-arrow-down-s-line text-base"></i>
                            </button>
                            @if (! empty($node['children']))
                                <div
                                    class="absolute left-1/2 -translate-x-1/2 top-full pt-3 min-w-[12rem] hidden group-hover:block z-50">
                                    <ul
                                        class="rounded-xl bg-[var(--color-card)] border border-[var(--color-border)] shadow-xl py-2">
                                        @foreach ($node['children'] as $child)
                                            <li>
                                                <a href="{{ $child['url'] ?: '#' }}"
                                                    class="block px-4 py-2 text-sm hover:bg-[var(--color-surface)] hover:text-primary transition-colors">
                                                    {{ $child['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        <a class="text-sm font-semibold hover:text-primary transition-colors"
                            href="/sayfa{{ $node['url'] ?: '#' }}">{{ $node['label'] }}</a>
                        <a class="text-sm font-semibold hover:text-primary transition-colors"
                            href="/biz-sizi-arayalim">İletişim</a>
                    @endif
                @endforeach
            @endif
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('favorites.index') }}"
                class="relative flex items-center justify-center gap-2 w-10 h-10 rounded-md glass md:text-lg text-sm text-primary ui-transition ui-lift ui-soft"
                data-tooltip="Listem" data-tooltip-position="bottom">
                <i class="ri-file-list-line"></i>
                <span data-favorites-count-badge
                    class="hidden absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-[var(--color-primary)] text-white text-[10px] font-bold items-center justify-center">
                </span>
            </a>
            <div class="h-6 border-l border-[var(--color-border)] hidden md:block"></div>
            <button id="openNavcanvas" type="button" data-tooltip="Menü" data-tooltip-position="bottom"
                class="flex items-center justify-center cursor-pointer gap-2 w-10 h-10 rounded-md glass  md:text-lg text-sm text-primary ui-transition ui-lift ui-soft">
                <i class="ri-menu-line"></i>
            </button>

            {{-- <button id="themeToggle" data-tooltip="Tema Değiştir" data-tooltip-position="bottom"
                class="flex items-center justify-center gap-2  w-10 h-10 rounded-md  glass  md:text-lg text-sm text-primary ui-transition ui-lift ui-soft">
                <i class="ri-sun-line hidden text-primary" data-theme-icon="light"></i>
                <i class="ri-moon-line text-primary " data-theme-icon="dark"></i>
            </button> --}}
        </div>
    </div>

    <div id="search-modal" class="fixed inset-0 z-40 hidden">
        <div class="search-modal-overlay absolute inset-0 bg-black/50 fade-overlay"></div>
        <div
            class="search-modal-card relative max-w-lg mx-auto mt-24 p-6 rounded-2xl bg-[var(--color-card)] border border-[var(--color-border)] shadow-xl space-y-3 fade-scale">
            <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-4 ">
                <h3 class="text-2xl font-semibold">Sayfada ara</h3>
                <button type="button"
                    class="search-modal-close h-9 w-9 rounded-full border border-[var(--color-border)] hover:bg-[var(--color-surface)]"><i
                        class="ri-close-line"></i></button>
            </div>
            <p class="text-sm text-[var(--color-muted)]">Ana sayfadaki filo, SSS ve iletişim bölümlerine hızlıca gitmek için aşağıdaki bağlantıları kullanabilirsiniz.</p>
            <div class="flex flex-col gap-2 text-sm">
                <a href="{{ route('home') }}#filo"
                    class="rounded-lg border border-[var(--color-border)] px-3 py-2 hover:border-[var(--color-primary)] ui-transition">Filo bölümü</a>
                <a href="{{ route('home') }}#sss"
                    class="rounded-lg border border-[var(--color-border)] px-3 py-2 hover:border-[var(--color-primary)] ui-transition">Sık sorulan sorular</a>
                <a href="{{ route('home') }}#iletisim"
                    class="rounded-lg border border-[var(--color-border)] px-3 py-2 hover:border-[var(--color-primary)] ui-transition">İletişim</a>
            </div>

        </div>
    </div>

    <div id="offNavcanvas" class="fixed inset-0 z-40 hidden">
        <div class="offnavcanvas-overlay absolute inset-0 bg-black/40 fade-overlay-light"></div>
        <div
            class="offnavcanvas-panel absolute left-0 top-0 h-full w-96 bg-[var(--color-card)] border-r border-[var(--color-border)] shadow-xl px-8 lg:py-12 py-4 space-y-4 slide-left">
            <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-4 px-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 h-12 ">
                    <img src="{{ $site['logo_url'] }}" alt="{{ $siteTitle }}" class="h-full w-auto max-w-40 object-contain object-left"
                        fetchpriority="high" loading="eager" decoding="async">
                </a>
                <button type="button"
                    class="offnavcanvas-close text-xl  hover:text-[var(--color-muted)]"><i
                        class="ri-close-line"></i></button>
            </div>
            @php
                $hasSocialNav = false;
                foreach (array_keys($socialNav) as $key) {
                    if (filled(data_get($mb, 'social.'.$key))) {
                        $hasSocialNav = true;
                        break;
                    }
                }
            @endphp
            @if ($hasSocialNav)
                <div class="flex flex-row items-center justify-center gap-2">
                    <ul class="flex flex-wrap items-center justify-center gap-2">
                        @foreach ($socialNav as $key => $meta)
                            @php $url = data_get($mb, 'social.'.$key); @endphp
                            @if (filled($url))
                                <li>
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" data-tooltip="{{ $meta['label'] }}"
                                        data-tooltip-position="bottom"
                                        class="flex items-center justify-center gap-2 w-10 h-10 rounded-lg bg-[var(--color-primary)]  text-base text-white hover:bg-[var(--color-primary-600)] transition">
                                        <i class="{{ $meta['icon'] }} text-white"></i>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <nav
                class="flex flex-col  overflow-y-auto lg:max-h-[calc(100vh-15rem)] max-h-[calc(100vh-15rem)]">
                @if (empty($navbarTree))
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-2 px-3 py-3 hover:bg-[var(--color-surface)] transition-all duration-200 rounded-lg">
                        <i class="ri-arrow-right-s-line text-[var(--color-primary)]"></i>
                        <span class="font-medium">Ana sayfa</span>
                    </a>
                @else
                    @foreach ($navbarTree as $node)
                        @if ($node['type'] === 'group')
                            <details class="offnav-group group">
                                <summary
                                    class="list-none flex items-center justify-between gap-2 px-3 py-3 cursor-pointer hover:bg-[var(--color-surface)] transition-all duration-200 rounded-lg">
                                    <span class="flex items-center gap-2 font-medium">
                                        <i class="ri-folder-2-line text-[var(--color-primary)]"></i>
                                        {{ $node['label'] }}
                                    </span>
                                    <i
                                        class="ri-arrow-down-s-line text-[var(--color-muted)] transition-transform group-open:rotate-180"></i>
                                </summary>
                                @if (! empty($node['children']))
                                    <div class="pl-3 mt-1 space-y-1 border-l border-[var(--color-border)] ml-4">
                                        @foreach ($node['children'] as $child)
                                            <a href="{{ $child['url'] ?: '#' }}"
                                                class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-[var(--color-surface)] transition-all duration-200 rounded-lg">
                                                <i class="ri-arrow-right-s-line text-[var(--color-primary)]"></i>
                                                <span>{{ $child['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </details>
                        @else
                            <a href="{{ $node['url'] ?: '#' }}"
                                class="flex items-center gap-2 px-3 py-3 hover:bg-[var(--color-surface)] transition-all duration-200 rounded-lg">
                                <i class="ri-arrow-right-s-line text-[var(--color-primary)]"></i>
                                <span class="font-medium">{{ $node['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('favorites.index') }}"
                    class="flex items-center gap-2 px-3 py-3 mt-2 rounded-lg border border-[var(--color-border)] hover:bg-[var(--color-surface)] transition-all duration-200 text-sm">
                    <i class="ri-heart-line text-[var(--color-primary)]"></i>
                    <span>Listem <span data-favorites-count-inline></span></span>
                </a>
                <a href="{{ route('login') }}"
                    class="flex items-center gap-2 px-3 py-3 mt-2 rounded-lg border border-[var(--color-border)] hover:bg-[var(--color-surface)] transition-all duration-200 text-sm">
                    <i class="ri-dashboard-line text-[var(--color-primary)]"></i>
                    <span>Yönetim paneli</span>
                </a>
            </nav>

        </div>
    </div>

    <div id="user-login-modal" class="fixed inset-0 z-40 hidden">
        <div class="user-login-modal-overlay absolute inset-0 bg-black/50 fade-overlay"></div>
        <div
            class="user-login-modal-card relative max-w-lg mx-auto mt-24 p-6 rounded-2xl bg-[var(--color-card)] border border-[var(--color-border)] shadow-xl space-y-3 fade-scale">
            <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-4 ">
                <h3 class="text-2xl font-semibold">Yönetim paneli girişi</h3>
                <button type="button"
                    class="user-login-modal-close h-9 w-9 rounded-full border border-[var(--color-border)] hover:bg-[var(--color-surface)]"><i
                        class="ri-close-line"></i></button>
            </div>
            <p class="text-sm text-[var(--color-muted)]">Filo ve içerik yönetimi için yetkili hesabınızla panele giriş yapın.</p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 pt-2">
                <button type="button"
                    class="user-login-modal-close px-4 py-2 rounded-lg border border-[var(--color-border)] hover:bg-[var(--color-surface)]">Kapat</button>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white hover:opacity-95 transition-opacity">Panele git</a>
            </div>
        </div>
    </div>

    <div id="user-register-modal" class="fixed inset-0 z-40 hidden">
        <div class="user-register-modal-overlay absolute inset-0 bg-black/50 fade-overlay"></div>
        <div
            class="user-register-modal-card relative max-w-lg mx-auto mt-24 p-6 rounded-2xl bg-[var(--color-card)] border border-[var(--color-border)] shadow-xl space-y-3 fade-scale">
            <div class="flex items-center justify-between border-b border-[var(--color-border)] pb-4 ">
                <h3 class="text-2xl font-semibold">Kurumsal teklif</h3>
                <button type="button"
                    class="user-register-modal-close h-9 w-9 rounded-full border border-[var(--color-border)] hover:bg-[var(--color-surface)]"><i
                        class="ri-close-line"></i></button>
            </div>

            <p class="text-sm text-[var(--color-muted)]">Filo kiralama ve uzun dönem çözümler için iletişim bilgilerimiz üzerinden bize ulaşın veya iletişim formu bölümünü kullanın.</p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3">
                <button type="button"
                    class="user-register-modal-close px-4 py-2 rounded-lg border border-[var(--color-border)] hover:bg-[var(--color-surface)]">Kapat</button>
                <a href="{{ route('home') }}#iletisim"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white hover:opacity-95 transition-opacity">İletişime geç</a>
            </div>
        </div>
    </div>
</nav>
