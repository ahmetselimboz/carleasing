@php
    $mb = $site['magicbox'] ?? [];
    $siteTitle = $site['title'] ?? config('app.name');
    $contactEmail = data_get($mb, 'contact.email');
    $contactPhone = data_get($mb, 'contact.phone');
    $contactAddress = data_get($mb, 'contact.address');
    $copyright = data_get($mb, 'site.copyright');
    $socialMap = [
        'facebook' => ['label' => 'Facebook', 'icon' => 'ri-facebook-fill'],
        'twitter' => ['label' => 'X (Twitter)', 'icon' => 'ri-twitter-x-fill'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'ri-instagram-line'],
        'linkedin' => ['label' => 'LinkedIn', 'icon' => 'ri-linkedin-fill'],
        'youtube' => ['label' => 'YouTube', 'icon' => 'ri-youtube-fill'],
        'tiktok' => ['label' => 'TikTok', 'icon' => 'ri-tiktok-fill'],
    ];

    // Footer menusunu kolonlara cevir: her grup bir kolon, gruplanmamis ust seviye ogeler tek bir "Baglantilar" kolonu
    $footerRowsRaw = data_get($mb, 'menus.footer', []);
    $footerColumns = [];
    $footerGroupIndex = [];
    $footerLooseColumnIndex = null;
    foreach ($footerRowsRaw as $row) {
        $type = $row['type'] ?? 'custom';
        $label = trim((string) ($row['label'] ?? ''));

        if ($type === 'group') {
            if ($label === '' || isset($footerGroupIndex[$label])) continue;
            $footerColumns[] = ['label' => $label, 'items' => []];
            $footerGroupIndex[$label] = array_key_last($footerColumns);
            continue;
        }

        $url = trim((string) ($row['url'] ?? ''));
        $parent = trim((string) ($row['parent'] ?? ''));
        if ($label === '' && $url === '') continue;
        $item = ['label' => $label, 'url' => $url];

        if ($parent !== '') {
            if (! isset($footerGroupIndex[$parent])) {
                $footerColumns[] = ['label' => $parent, 'items' => []];
                $footerGroupIndex[$parent] = array_key_last($footerColumns);
            }
            $footerColumns[$footerGroupIndex[$parent]]['items'][] = $item;
        } else {
            if ($footerLooseColumnIndex === null) {
                $footerColumns[] = ['label' => 'Bağlantılar', 'items' => []];
                $footerLooseColumnIndex = array_key_last($footerColumns);
            }
            $footerColumns[$footerLooseColumnIndex]['items'][] = $item;
        }
    }
    // Bos kolonlari at
    $footerColumns = array_values(array_filter($footerColumns, fn ($c) => ! empty($c['items'])));
@endphp
<footer class="bg-background-dark text-white pt-20 pb-10 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
        <div class="space-y-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 h-12 w-fit">
                @if (! empty($site['logo_url']))
                    <img src="{{ $site['logo_url'] }}" alt="{{ $siteTitle }}" class="h-full w-auto max-w-[200px] object-contain object-left drop-shadow-sm"
                        loading="lazy" decoding="async">
                @else
                    <span class="material-symbols-outlined text-3xl text-primary">directions_car</span>
                    <span class="text-xl font-black tracking-tight">{{ $siteTitle }}</span>
                @endif
            </a>
            <p class="text-slate-400 text-sm leading-relaxed">
                {{ \Illuminate\Support\Str::limit(strip_tags($site['description'] ?? ''), 220) ?: 'Kurumsal filo ve uzun dönem araç kiralama çözümleriyle yanınızdayız.' }}
            </p>
            @php
                $hasSocial = false;
                foreach (array_keys($socialMap) as $key) {
                    if (filled(data_get($mb, 'social.'.$key))) {
                        $hasSocial = true;
                        break;
                    }
                }
            @endphp
            @if ($hasSocial)
                <div class="flex flex-wrap gap-3">
                    @foreach ($socialMap as $key => $meta)
                        @php $url = data_get($mb, 'social.'.$key); @endphp
                        @if (filled($url))
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                aria-label="{{ $meta['label'] }}"
                                class="size-10 rounded-full glass-dark flex items-center justify-center hover:bg-primary transition-colors">
                                <i class="{{ $meta['icon'] }} text-sm"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        @if (! empty($footerColumns))
            @foreach ($footerColumns as $column)
                <div>
                    <h5 class="font-bold mb-6 text-white">{{ $column['label'] }}</h5>
                    <ul class="space-y-4 text-sm text-slate-400">
                        @foreach ($column['items'] as $item)
                            <li>
                                <a class="hover:text-white transition-colors" href="{{ $item['url'] ?: '#' }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @else
            <div>
                <h5 class="font-bold mb-6 text-white">Hızlı bağlantılar</h5>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}">Ana sayfa</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#filo">Filo</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#sss">Sık sorulan sorular</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#iletisim">İletişim</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6 text-white">Bilgi</h5>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#sss">SSS</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#iletisim">Teklif ve danışmanlık</a></li>
                    <li><a class="hover:text-white transition-colors" href="{{ route('home') }}#filo">Öne çıkan araçlar</a></li>
                </ul>
            </div>
        @endif
        <div id="iletisim">
            <h5 class="font-bold mb-6 text-white">İletişim</h5>
            <ul class="space-y-4 text-sm text-slate-400">
                @if (filled($contactAddress))
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-white shrink-0">
                            <i class="ri-map-pin-line"></i>
                        </span>
                        <span>{{ $contactAddress }}</span>
                    </li>
                @endif
                @if (filled($contactPhone))
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white shrink-0">
                            <i class="ri-phone-line"></i>
                        </span>
                        <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}"
                            class="hover:text-white transition-colors">{{ $contactPhone }}</a>
                    </li>
                @endif
                @if (filled($contactEmail))
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-white shrink-0">
                            <i class="ri-mail-line"></i>
                        </span>
                        <a href="mailto:{{ $contactEmail }}" class="hover:text-white transition-colors break-all">{{ $contactEmail }}</a>
                    </li>
                @endif
                @if (! filled($contactAddress) && ! filled($contactPhone) && ! filled($contactEmail))
                    <li class="text-slate-500">İletişim bilgileri yönetim panelinden (Ayarlar → İletişim) eklenebilir.</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto pt-8 border-t border-white/10 text-center text-xs text-slate-500">
        {{ $copyright ?: '© '.date('Y').' '.$siteTitle.'. Tüm hakları saklıdır.' }}
    </div>
</footer>
