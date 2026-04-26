@extends('admin.layout')

@section('content')
    @php
        $m = $mb ?? [];
    @endphp
    <div class="fade-in space-y-6" x-data="{
        activeTab: 'genel',
        previews: @js($media),
        setPreview(key, e) {
            const t = e.target;
            if (t.files && t.files[0]) {
                if (this.previews[key] && String(this.previews[key]).startsWith('blob:')) {
                    URL.revokeObjectURL(this.previews[key]);
                }
                this.previews[key] = URL.createObjectURL(t.files[0]);
            }
        }
    }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Ayarlar</h2>
                <p class="text-slate-500 text-sm mt-1">Sitenin temel bilgilerini, görsellerini ve diğer tercihlerini buradan yönetebilirsiniz.</p>
            </div>
        </div>

        <div class="card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <form action="{{ route('settings.update') }}" method="post" enctype="multipart/form-data" class="flex flex-col">
                @csrf

                <div class="border-b border-slate-200 px-4 lg:px-6">
                    <div class="flex gap-1 overflow-x-auto">
                        <button type="button" @click="activeTab = 'genel'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'genel' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-settings-3-line align-middle mr-1"></i> Genel
                        </button>
                        <button type="button" @click="activeTab = 'iletisim'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'iletisim' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-share-line align-middle mr-1"></i> Sosyal Medya ve İletişim
                        </button>
                        <button type="button" @click="activeTab = 'medya'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'medya' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-image-2-line align-middle mr-1"></i> Logo ve Görseller
                        </button>
                        <button type="button" @click="activeTab = 'seo'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'seo' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-search-eye-line align-middle mr-1"></i> Arama Görünürlüğü
                        </button>
                        <button type="button" @click="activeTab = 'sistem'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'sistem' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-tools-line align-middle mr-1"></i> Bakım ve Özellikler
                        </button>
                        <button type="button" @click="activeTab = 'entegrasyon'"
                            :class="['px-4 py-3 flex items-center font-medium text-sm border-b-2 transition whitespace-nowrap', activeTab === 'entegrasyon' ? 'admin-tab-active' : 'border-transparent text-slate-500 hover:text-slate-700']">
                            <i class="ri-plug-line align-middle mr-1"></i> Bağlantılar ve Ölçüm
                        </button>
                    </div>
                </div>

                <div class="p-4 lg:p-6 space-y-8">

                    {{-- Genel --}}
                    <div x-show="activeTab === 'genel'" x-cloak class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-slate-700 mb-2">Site başlığı</label>
                                <input type="text" name="title" id="title"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft @error('title') border-red-500 ring-2 ring-red-200 @enderror"
                                    value="{{ old('title', $setting->title) }}" placeholder="Örn: Araç Kiralama">
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1 flex items-center gap-1"><i class="ri-error-warning-line"></i> {{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Kısa açıklama</label>
                                <textarea name="description" id="description" rows="3"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand transition-soft"
                                     maxlength="255"
                                    placeholder="Ana sayfada görünecek kısa tanıtım metni.">{{ old('description', $setting->description) }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="mb_site_copyright" class="block text-sm font-medium text-slate-700 mb-2">Telif yazısı</label>
                                <input type="text" name="magicbox[site][copyright]" id="mb_site_copyright"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 text-sm"
                                    value="{{ old('magicbox.site.copyright', data_get($m, 'site.copyright')) }}"
                                    placeholder="© {{ date('Y') }} Şirket Adı. Tüm hakları saklıdır.">
                                @error('magicbox.site.copyright')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Sosyal medya ve iletişim --}}
                    <div x-show="activeTab === 'iletisim'" x-cloak class="space-y-6">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-5 space-y-4">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-contacts-line text-brand"></i> İletişim bilgileri</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="mb_contact_email" class="block text-sm font-medium text-slate-700 mb-2">E-posta</label>
                                    <div class="flex border border-slate-200 rounded-xl overflow-hidden bg-white">
                                        <span class="px-3 py-2.5 bg-slate-100 text-slate-600 border-r border-slate-200"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="magicbox[contact][email]" id="mb_contact_email"
                                            class="flex-1 px-3 py-2.5 focus:ring-2 focus:ring-brand/20 focus:outline-none text-sm"
                                            value="{{ old('magicbox.contact.email', data_get($m, 'contact.email')) }}" placeholder="info@site.com">
                                    </div>
                                    @error('magicbox.contact.email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="mb_contact_phone" class="block text-sm font-medium text-slate-700 mb-2">Telefon</label>
                                    <div class="flex border border-slate-200 rounded-xl overflow-hidden bg-white">
                                        <span class="px-3 py-2.5 bg-slate-100 text-slate-600 border-r border-slate-200"><i class="ri-phone-line"></i></span>
                                        <input type="text" name="magicbox[contact][phone]" id="mb_contact_phone"
                                            class="flex-1 px-3 py-2.5 focus:ring-2 focus:ring-brand/20 focus:outline-none text-sm"
                                            value="{{ old('magicbox.contact.phone', data_get($m, 'contact.phone')) }}" placeholder="+90 …">
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="mb_contact_address" class="block text-sm font-medium text-slate-700 mb-2">Adres</label>
                                    <textarea name="magicbox[contact][address]" id="mb_contact_address" rows="3"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 resize-none text-sm"
                                        placeholder="Şirket / ofis adresi">{{ old('magicbox.contact.address', data_get($m, 'contact.address')) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-links-line text-brand"></i> Sosyal medya hesapları</h3>
                            <p class="text-xs text-slate-500">Tam profil URL’lerini girin (örn. <code class="text-[11px] bg-slate-50 px-1 rounded border border-slate-200">https://instagram.com/kullanici</code>). Boş bırakılan ağlar sitede gizlenir.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ([
                                    'facebook' => ['Facebook', 'ri-facebook-fill', 'https://facebook.com/…'],
                                    'twitter' => ['X (Twitter)', 'ri-twitter-x-fill', 'https://x.com/…'],
                                    'instagram' => ['Instagram', 'ri-instagram-line', 'https://instagram.com/…'],
                                    'linkedin' => ['LinkedIn', 'ri-linkedin-fill', 'https://linkedin.com/company/…'],
                                    'youtube' => ['YouTube', 'ri-youtube-fill', 'https://youtube.com/@…'],
                                    'tiktok' => ['TikTok', 'ri-tiktok-fill', 'https://tiktok.com/@…'],
                                ] as $key => $meta)
                                    <div>
                                        <label for="mb_social_{{ $key }}" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                            <i class="{{ $meta[1] }} text-brand"></i> {{ $meta[0] }}
                                        </label>
                                        <input type="text" name="magicbox[social][{{ $key }}]" id="mb_social_{{ $key }}" inputmode="url" autocomplete="url"
                                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 text-sm"
                                            value="{{ old('magicbox.social.'.$key, data_get($m, 'social.'.$key)) }}"
                                            placeholder="{{ $meta[2] }}">
                                        @error('magicbox.social.'.$key)
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Medya --}}
                    <div x-show="activeTab === 'medya'" x-cloak class="space-y-8">
                        <p class="text-sm text-slate-600">PNG, JPG, WEBP veya GIF görseller yükleyebilirsiniz.</p>

                        @foreach ([
                            'logo' => ['label' => 'Site logosu', 'hint' => 'Geniş yatay logo önerilir (şeffaf PNG).', 'icon' => 'ri-image-line'],
                            'favicon' => ['label' => 'Favicon', 'hint' => 'Kare ikon, tercihen 32×32 veya 64×64.', 'icon' => 'ri-star-line'],
                            'placeholder_image' => ['label' => 'Varsayılan görsel (placeholder)', 'hint' => 'Listelerde görsel yoksa kullanılır.', 'icon' => 'ri-landscape-line'],
                        ] as $field => $meta)
                            <div class="rounded-2xl border border-slate-100 p-5 bg-slate-50/50">
                                <div class="flex flex-col lg:flex-row gap-6 lg:items-start">
                                    <div class="shrink-0">
                                        <div class="w-full max-w-[220px] aspect-[4/3] rounded-xl border-2 border-dashed border-slate-200 bg-white flex items-center justify-center overflow-hidden">
                                            <template x-if="previews['{{ $field }}']">
                                                <img :src="previews['{{ $field }}']" alt="" class="max-h-44 w-auto object-contain">
                                            </template>
                                            <template x-if="!previews['{{ $field }}']">
                                                <div class="text-center p-4 text-slate-400">
                                                    <i class="{{ $meta['icon'] }} text-4xl block mb-2"></i>
                                                    <span class="text-xs">Önizleme yok</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-800">{{ $meta['label'] }}</label>
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $meta['hint'] }}</p>
                                        </div>
                                        <input type="file" name="{{ $field }}" accept="image/*"
                                            @change="setPreview('{{ $field }}', $event)"
                                            class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-solid file:text-white file:cursor-pointer text-sm">
                                        @error($field)
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                        @if($setting->{$field})
                                            <p class="text-xs text-slate-500 break-all">Mevcut dosya: <code class="bg-white px-1 rounded border border-slate-200">{{ $setting->{$field} }}</code></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- SEO --}}
                    <div x-show="activeTab === 'seo'" x-cloak class="space-y-6">
                     
                        @php
                            $allowIndex = old('magicbox.seo.allow_indexing', data_get($m, 'seo.allow_indexing', true));
                            $allowIndexOn = $allowIndex === true || $allowIndex === 1 || $allowIndex === '1';
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl border border-slate-100 p-4 bg-white">
                            <div>
                                <p class="text-sm font-medium text-slate-800">Google'da görünme</p>
                                <p class="text-xs text-slate-500 mt-0.5">Açık olduğunda sayfalar arama motorlarında görünebilir.</p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-sm text-slate-600">{{ $allowIndexOn ? 'Görünür' : 'Gizli' }}</span>
                                <label class="inline-flex cursor-pointer items-center">
                                    <input type="hidden" name="magicbox[seo][allow_indexing]" value="0">
                                    <input type="checkbox" name="magicbox[seo][allow_indexing]" value="1" class="peer sr-only"
                                        @checked($allowIndexOn)>
                                    <span class="relative inline-flex h-6 w-12 shrink-0 rounded-full bg-slate-200 transition-colors peer-checked:bg-brand-solid after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:content-[''] peer-checked:after:translate-x-6"></span>
                                </label>
                            </div>
                        </div>
                        @error('magicbox.seo.allow_indexing')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                        <div>
                            <label for="mb_seo_keywords" class="block text-sm font-medium text-slate-700 mb-2">Anahtar kelimeler (isteğe bağlı)</label>
                            <textarea name="magicbox[seo][meta_keywords]" id="mb_seo_keywords" rows="2"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 resize-none text-sm"
                                placeholder="Virgülle ayırın: araç kiralama, filo, …">{{ old('magicbox.seo.meta_keywords', data_get($m, 'seo.meta_keywords')) }}</textarea>
                        </div>
                        <div>
                            <label for="mb_seo_desc" class="block text-sm font-medium text-slate-700 mb-2">Varsayılan arama açıklaması</label>
                            <textarea name="magicbox[seo][default_meta_description]" id="mb_seo_desc" rows="4"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 resize-none text-sm"
                                placeholder="Arama sonuçlarında görünen özet metin.">{{ old('magicbox.seo.default_meta_description', data_get($m, 'seo.default_meta_description')) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 p-4 bg-white">
                            <div>
                                <p class="text-sm font-medium text-slate-800">Sosyal medya paylaşım ayarı</p>
                                <p class="text-xs text-slate-500 mt-0.5">Açık olduğunda paylaşımlarda varsayılan görsel ve metin kullanılır.</p>
                            </div>
                            <label class="inline-flex cursor-pointer items-center gap-3 shrink-0">
                                <input type="checkbox" name="magicbox[features][open_graph_default]" value="1" class="peer sr-only"
                                    @checked(old('magicbox.features.open_graph_default', (bool) data_get($m, 'features.open_graph_default')))>
                                <span class="relative inline-flex h-6 w-12 shrink-0 rounded-full bg-slate-200 transition-colors peer-checked:bg-brand-solid after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:content-[''] peer-checked:after:translate-x-6"></span>
                            </label>
                        </div>
                    </div>

                    {{-- Bakım ve özellikler --}}
                    <div x-show="activeTab === 'sistem'" x-cloak class="space-y-8">
                        <div class="rounded-xl border border-slate-200 p-5 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Bakım modu</p>
                                    <p class="text-xs text-slate-500 mt-1">Açık olduğunda ziyaretçiler bakım ekranı görür.</p>
                                </div>
                                <label class="inline-flex cursor-pointer items-center gap-3 shrink-0">
                                    <span class="text-sm text-slate-600">Durum</span>
                                    <input type="checkbox" name="maintenance_mode" value="1" class="peer sr-only"
                                        @checked(old('maintenance_mode', $setting->maintenance_mode))>
                                    <span class="relative inline-flex h-6 w-12 shrink-0 rounded-full bg-slate-200 transition-colors peer-checked:bg-brand-solid after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:content-[''] peer-checked:after:translate-x-6"></span>
                                </label>
                            </div>
                            <div>
                                <label for="mb_maint_msg" class="block text-sm font-medium text-slate-700 mb-2">Bakım mesajı</label>
                                <textarea name="magicbox[maintenance][message]" id="mb_maint_msg" rows="4"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 resize-none text-sm"
                                    placeholder="Ziyaretçilere göstermek istediğiniz kısa bilgi.">{{ old('magicbox.maintenance.message', data_get($m, 'maintenance.message')) }}</textarea>
                            </div>
                            <div class="max-w-md">
                                <label for="mb_maint_end" class="block text-sm font-medium text-slate-700 mb-2">Tahmini bitiş (isteğe bağlı)</label>
                                <input type="datetime-local" name="magicbox[maintenance][estimated_end]" id="mb_maint_end"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 text-sm"
                                    value="{{ old('magicbox.maintenance.estimated_end', data_get($m, 'maintenance.estimated_end')) }}">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-slate-800">Ek özellikler</h3>
                            <p class="text-xs text-slate-500">Bu seçenekleri açıp kapatarak site davranışını kolayca yönetebilirsiniz.</p>

                            @foreach ([
                                'newsletter' => ['Bülten / e-posta listesi', 'ri-mail-send-line'],
                                'cookie_banner' => ['Çerez bildirimi', 'ri-cookie-line'],
                                'allow_registration' => ['Kullanıcı kaydına izin ver', 'ri-user-add-line'],
                            ] as $feat => $info)
                                <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-100 p-4 bg-white">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <span class="mt-0.5 text-brand"><i class="{{ $info[1] }} text-lg"></i></span>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $info[0] }}</p>
                                        </div>
                                    </div>
                                    <label class="inline-flex cursor-pointer items-center shrink-0">
                                        <input type="checkbox" name="magicbox[features][{{ $feat }}]" value="1" class="peer sr-only"
                                            @checked(old('magicbox.features.'.$feat, (bool) data_get($m, 'features.'.$feat)))>
                                        <span class="relative inline-flex h-6 w-12 rounded-full bg-slate-200 transition-colors peer-checked:bg-brand-solid after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform after:content-[''] peer-checked:after:translate-x-6"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Entegrasyonlar --}}
                    <div x-show="activeTab === 'entegrasyon'" x-cloak class="space-y-6">

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-bar-chart-line text-brand"></i> Ziyaret ölçümü (GA / GTM)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="ga_id" class="block text-sm font-medium text-slate-700 mb-2">Google Analytics kodu</label>
                                    <input type="text" name="magicbox[integrations][google_analytics_id]" id="ga_id"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 text-sm font-mono"
                                        value="{{ old('magicbox.integrations.google_analytics_id', data_get($m, 'integrations.google_analytics_id')) }}"
                                        placeholder="G-XXXXXXXXXX" autocomplete="off">
                                    @error('magicbox.integrations.google_analytics_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="gtm_id" class="block text-sm font-medium text-slate-700 mb-2">Google Tag Manager</label>
                                    <input type="text" name="magicbox[integrations][google_tag_manager_id]" id="gtm_id"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 text-sm font-mono"
                                        value="{{ old('magicbox.integrations.google_tag_manager_id', data_get($m, 'integrations.google_tag_manager_id')) }}"
                                        placeholder="GTM-XXXXXXX" autocomplete="off">
                                    @error('magicbox.integrations.google_tag_manager_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <label for="mb_ga_track" class="block text-sm font-medium text-slate-700 mb-2">Google Analytics kodu</label>
                                <p class="text-xs text-slate-500 mb-2">Google'dan aldığınız takip kodunu buraya yapıştırabilirsiniz.</p>
                                <textarea name="magicbox[google][analytics_tracking_code]" id="mb_ga_track" rows="6"
                                    class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand/20 font-mono text-xs resize-y"
                                    placeholder="&lt;script async src=&quot;https://www.googletagmanager.com/gtag/js?id=...&quot;&gt;&lt;/script&gt; …">{{ old('magicbox.google.analytics_tracking_code', data_get($m, 'google.analytics_tracking_code')) }}</textarea>
                                @error('magicbox.google.analytics_tracking_code')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-google-fill text-brand"></i> Google bağlantı bilgileri</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="mb_g_client_id" class="block text-sm font-medium text-slate-700 mb-2">Google uygulama kimliği</label>
                                    <input type="text" name="magicbox[google][oauth_client_id]" id="mb_g_client_id"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono"
                                        value="{{ old('magicbox.google.oauth_client_id', data_get($m, 'google.oauth_client_id')) }}" autocomplete="off">
                                    @error('magicbox.google.oauth_client_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="mb_g_client_secret" class="block text-sm font-medium text-slate-700 mb-2">Google gizli anahtar</label>
                                    <input type="password" name="magicbox[google][oauth_client_secret]" id="mb_g_client_secret"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono"
                                        value="{{ old('magicbox.google.oauth_client_secret') }}" autocomplete="new-password"
                                        placeholder="{{ filled(data_get($m, 'google.oauth_client_secret')) ? 'Değiştirmek için yeni gizli anahtar girin' : 'Gizli anahtar' }}">
                                    @error('magicbox.google.oauth_client_secret')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="mb_g_redirect" class="block text-sm font-medium text-slate-700 mb-2">Google yönlendirme adresi</label>
                                    <input type="text" name="magicbox[google][oauth_redirect_url]" id="mb_g_redirect" inputmode="url"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm"
                                        value="{{ old('magicbox.google.oauth_redirect_url', data_get($m, 'google.oauth_redirect_url')) }}"
                                        placeholder="https://site.com/auth/google/callback">
                                    @error('magicbox.google.oauth_redirect_url')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="mb_g_property" class="block text-sm font-medium text-slate-700 mb-2">Google hesap kimliği</label>
                                    <input type="text" name="magicbox[google][property_id]" id="mb_g_property"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono"
                                        value="{{ old('magicbox.google.property_id', data_get($m, 'google.property_id')) }}"
                                        placeholder="Örn: Analytics mülk / Search Console kaynağı">
                                    @error('magicbox.google.property_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-code-s-slash-line text-brand"></i> Özel kod alanları</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="mb_inj_head" class="block text-sm font-medium text-slate-700 mb-2">&lt;head&gt; alanı</label>
                                    <textarea name="magicbox[inject][head]" id="mb_inj_head" rows="4"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl font-mono text-xs resize-y">{{ old('magicbox.inject.head', data_get($m, 'inject.head')) }}</textarea>
                                    @error('magicbox.inject.head')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="mb_inj_body" class="block text-sm font-medium text-slate-700 mb-2">&lt;body&gt; açılış (üst)</label>
                                    <textarea name="magicbox[inject][body]" id="mb_inj_body" rows="4"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl font-mono text-xs resize-y">{{ old('magicbox.inject.body', data_get($m, 'inject.body')) }}</textarea>
                                    @error('magicbox.inject.body')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="mb_inj_footer" class="block text-sm font-medium text-slate-700 mb-2">Footer / &lt;body&gt; kapanış öncesi</label>
                                    <textarea name="magicbox[inject][footer]" id="mb_inj_footer" rows="4"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl font-mono text-xs resize-y">{{ old('magicbox.inject.footer', data_get($m, 'inject.footer')) }}</textarea>
                                    @error('magicbox.inject.footer')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-file-text-line text-brand"></i> ads.txt</h3>
                            <p class="text-xs text-slate-500">Reklam altyapısı kullanıyorsanız bu alana `ads.txt` içeriğini ekleyebilirsiniz.</p>
                            <textarea name="magicbox[ads_txt][content]" id="mb_ads_txt" rows="6"
                                class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl font-mono text-xs resize-y"
                                placeholder="google.com, pub-xxxxxxxxxxxxxxxx, DIRECT, f08c47fec0942fa0">{{ old('magicbox.ads_txt.content', data_get($m, 'ads_txt.content')) }}</textarea>
                            @error('magicbox.ads_txt.content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-white p-5 space-y-4 shadow-sm">
                            <h3 class="text-sm font-semibold text-slate-800 flex items-center gap-2"><i class="ri-mail-send-line text-brand"></i> SMTP e-posta</h3>
                            <p class="text-xs text-slate-500">E-posta gönderimi için gerekli sunucu bilgilerini buradan girebilirsiniz.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="mb_smtp_host" class="block text-sm font-medium text-slate-700 mb-2">SMTP sunucu</label>
                                    <input type="text" name="magicbox[mail][smtp_host]" id="mb_smtp_host"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm"
                                        value="{{ old('magicbox.mail.smtp_host', data_get($m, 'mail.smtp_host')) }}" placeholder="smtp.mailprovider.com">
                                </div>
                                <div>
                                    <label for="mb_smtp_port" class="block text-sm font-medium text-slate-700 mb-2">Port</label>
                                    <input type="text" name="magicbox[mail][smtp_port]" id="mb_smtp_port"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono"
                                        value="{{ old('magicbox.mail.smtp_port', data_get($m, 'mail.smtp_port')) }}" placeholder="587">
                                </div>
                                <div>
                                    <label for="mb_smtp_user" class="block text-sm font-medium text-slate-700 mb-2">Kullanıcı adı</label>
                                    <input type="text" name="magicbox[mail][smtp_username]" id="mb_smtp_user" autocomplete="off"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm"
                                        value="{{ old('magicbox.mail.smtp_username', data_get($m, 'mail.smtp_username')) }}">
                                </div>
                                <div>
                                    <label for="mb_smtp_pass" class="block text-sm font-medium text-slate-700 mb-2">Şifre</label>
                                    <input type="password" name="magicbox[mail][smtp_password]" id="mb_smtp_pass" autocomplete="new-password"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono"
                                        value="{{ old('magicbox.mail.smtp_password') }}" placeholder="{{ filled(data_get($m, 'mail.smtp_password')) ? 'Değiştirmek için yeni şifre' : 'SMTP şifresi' }}">
                                </div>
                                <div>
                                    <label for="mb_smtp_enc" class="block text-sm font-medium text-slate-700 mb-2">Şifreleme</label>
                                    <select name="magicbox[mail][smtp_encryption]" id="mb_smtp_enc"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-white">
                                        @php $enc = old('magicbox.mail.smtp_encryption', data_get($m, 'mail.smtp_encryption', '')); @endphp
                                        <option value="" @selected($enc === '' || $enc === null)>Yok</option>
                                        <option value="tls" @selected($enc === 'tls')>TLS</option>
                                        <option value="ssl" @selected($enc === 'ssl')>SSL</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="mb_mail_from" class="block text-sm font-medium text-slate-700 mb-2">Gönderen e-posta</label>
                                    <input type="email" name="magicbox[mail][from_address]" id="mb_mail_from"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm"
                                        value="{{ old('magicbox.mail.from_address', data_get($m, 'mail.from_address')) }}" placeholder="noreply@site.com">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="mb_mail_from_name" class="block text-sm font-medium text-slate-700 mb-2">Gönderen adı</label>
                                    <input type="text" name="magicbox[mail][from_name]" id="mb_mail_from_name"
                                        class="input-base w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm"
                                        value="{{ old('magicbox.mail.from_name', data_get($m, 'mail.from_name')) }}" placeholder="Site Adı">
                                </div>
                            </div>
                            @foreach (['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_address', 'from_name'] as $mf)
                                @error('magicbox.mail.'.$mf)
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                            <p class="font-medium flex items-center gap-2"><i class="ri-error-warning-line"></i> Lütfen formu kontrol edin.</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100">
                        <p class="text-sm text-slate-500">Kaydetmediğiniz değişiklikler kaybolur.</p>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-solid px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-solid-hover transition-soft">
                            <i class="ri-save-3-line text-lg"></i>
                            Ayarları kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
