<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Genel ortak sayfalar (içerik sayfaları) için demo veriler.
 * Hedef Filo'nun "elektrikli-arac-kiralama" benzeri yapısı temel alınmıştır:
 * hero, intro, CTA butonlar, başlıklı içerik blokları, avantaj listesi,
 * karşılaştırma tablosu, gereklilikler, ek hizmetler ve SSS.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'rental' => 'Araç Kiralama Çözümleri',
            'fleet' => 'Filo Yönetimi',
            'corporate' => 'Kurumsal',
            'support' => 'Yardım & Destek',
            'legal' => 'Hukuki',
        ];

        $catIds = [];
        foreach ($categories as $key => $name) {
            $cat = PageCategory::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
            $catIds[$key] = $cat->id;
        }

        $pages = $this->pages($catIds);

        foreach ($pages as $page) {
            $slug = $page['slug'];
            Page::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'slug_hash' => abs(crc32($slug)) % 2147483647,
                    'description' => $page['description'],
                    'page_category_id' => $page['page_category_id'],
                    'is_active' => true,
                    'magicbox' => $page['magicbox'],
                ]
            );
        }

        $this->command?->info('Ortak sayfalar yüklendi: '.count($pages).' sayfa.');
    }

    /**
     * @param  array<string,int>  $cats
     * @return list<array<string,mixed>>
     */
    private function pages(array $cats): array
    {
        $cta = [
            ['label' => 'Uzun Dönem Kiralama Teklifi Al', 'href' => '/biz-sizi-arayalim', 'icon' => 'ri-calendar-check-line'],
            ['label' => 'Filo Yönetimi Teklifi Al', 'href' => '/biz-sizi-arayalim', 'icon' => 'ri-truck-line'],
        ];

        $commonFaq = [
            ['q' => 'Kiralama için hangi belgeler gerekir?', 'a' => 'Bireysel kiralamada kimlik ve sürücü belgesi yeterlidir. Kurumsal kiralamada Ticaret Sicili Gazetesi, vergi levhası, imza sirküleri ve yetkili kişinin sürücü belgesi talep edilir.'],
            ['q' => 'Kira süresi ne kadardır?', 'a' => 'Uzun dönem kiralama tipik olarak 12 ile 48 ay arası planlanır. İhtiyaca göre daha esnek dönemler için ekibimizle görüşebilirsiniz.'],
            ['q' => 'Sigorta, kasko ve bakım fiyata dahil mi?', 'a' => 'Standart paketlerimizde trafik sigortası, kasko, periyodik bakım ve lastik dahildir. Genişletilmiş güvence paketleri opsiyoneldir.'],
            ['q' => 'Aracı erken iade edebilir miyim?', 'a' => 'Evet, sözleşmede tanımlı erken iade koşulları çerçevesinde mümkündür. Detaylar müşteri temsilcimiz tarafından şeffaf biçimde paylaşılır.'],
        ];

        return [
            [
                'title' => 'Elektrikli Araç Kiralama',
                'slug' => 'elektrikli-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Sıfır emisyonlu elektrikli filolar ile sürdürülebilir mobiliteye geçin. Şirketinizin karbon ayak izini düşürürken işletme maliyetlerinden tasarruf edin.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Sürdürülebilir Filo',
                        'title' => 'Elektrikli Araç Kiralama',
                        'subtitle' => 'Sıfır karbon salınımı, düşük yakıt maliyeti ve devlet teşvikleri ile geleceğin ulaşım çözümü.',
                    ],
                    'intro' => 'Şirketiniz için filo kiralamak istiyor ve aynı zamanda çevreci bir yaklaşımla sürdürülebilirlik hedeflerinize katkı sağlamak mı istiyorsunuz? Elektrikli araç kiralama çözümlerimizle hem operasyonel verimliliği artırın hem de kurumsal sosyal sorumluluk hedeflerinizi destekleyin.',
                    'ctas' => $cta,
                    'sections' => [
                        ['type' => 'text', 'title' => 'Uzun Dönem Elektrikli Araç Kiralama', 'body' => 'Karbon ayak izini azaltma hedefleri ve devlet teşviklerinin etkisiyle elektrikli araçlar kurumsal filolarda hızla yaygınlaşıyor. Uzun dönem kiralama modeli, yüksek satın alma maliyetini bertaraf ederek elektrikli araçlara erişimi kolaylaştırır.'],
                        ['type' => 'features', 'title' => 'Genel Avantajlar', 'items' => [
                            ['icon' => 'ri-leaf-line', 'title' => 'Sıfır karbon salınımı', 'body' => 'Çevreye duyarlı operasyon ve sürdürülebilirlik karnesinde güçlü performans.'],
                            ['icon' => 'ri-volume-mute-line', 'title' => 'Sessiz, konforlu sürüş', 'body' => 'Düşük gürültü ve titreşim ile şehir içi kullanımda üst düzey deneyim.'],
                            ['icon' => 'ri-flashlight-line', 'title' => 'Düşük yakıt maliyeti', 'body' => 'Elektrik kWh maliyeti, benzin/dizel litre maliyetinin önemli ölçüde altındadır.'],
                            ['icon' => 'ri-tools-line', 'title' => 'Daha az bakım masrafı', 'body' => 'Daha az hareketli parça, daha az aşınma; periyodik bakım gereksinimi düşüktür.'],
                            ['icon' => 'ri-percent-line', 'title' => 'Vergi avantajları', 'body' => 'MTV, KDV, ÖTV ve Kurumlar Vergisi tarafında öngörülebilir avantajlar.'],
                        ]],
                        ['type' => 'comparison', 'title' => 'Satın Alma vs Kiralama', 'columns' => [
                            ['title' => 'Satın Alma', 'tone' => 'muted', 'items' => [
                                'Yüksek peşin sermaye gereksinimi',
                                'Batarya bakımı ve değişim riski şirkete ait',
                                'Amortisman ve ikinci el değer kaybı sizde',
                                'Sigorta, kasko ve operasyon yükü kendinize ait',
                            ]],
                            ['title' => 'Uzun Dönem Kiralama', 'tone' => 'primary', 'items' => [
                                'Sabit aylık ödeme ile öngörülebilir nakit akışı',
                                'Bakım, lastik, sigorta tek pakette',
                                'Modern model her zaman elinizin altında',
                                '7/24 yol yardım ve yedek araç hizmeti',
                            ]],
                        ]],
                    ],
                    'faqs' => $commonFaq,
                    'final_cta' => ['title' => 'Filonuzu elektrikliye taşıyalım', 'subtitle' => 'Sürdürülebilirlik hedeflerinize uygun teklif birkaç dakikada hazır.', 'button' => ['label' => 'Hemen teklif al', 'href' => '/biz-sizi-arayalim']],
                ],
            ],
            [
                'title' => 'Hibrit Araç Kiralama',
                'slug' => 'hibrit-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Hibrit araçlar ile menzil endişesi olmadan düşük yakıt tüketimine kavuşun.',
                'magicbox' => $this->genericLayout(
                    'Hibrit Araç Kiralama',
                    'Düşük tüketim, yüksek verim',
                    'Şehir içinde elektrikli, uzun yolda yakıtlı sürüş avantajıyla hibrit araçlar; menzil kaygısı olmadan düşük tüketim arayan filolar için ideal çözümdür.',
                    [
                        ['icon' => 'ri-gas-station-line', 'title' => 'Düşük yakıt tüketimi', 'body' => 'Şehir içinde dönüşümlü elektrikli sürüş ile %30-40 tasarruf.'],
                        ['icon' => 'ri-road-map-line', 'title' => 'Yüksek menzil', 'body' => 'Şarj zorunluluğu olmadan uzun yolda sorunsuz sürüş.'],
                        ['icon' => 'ri-leaf-line', 'title' => 'Düşük emisyon', 'body' => 'Çevreye saygılı, düşük CO2 değerleriyle teşvik kapsamında.'],
                        ['icon' => 'ri-shield-star-line', 'title' => 'Tam paket güvence', 'body' => 'Bakım, sigorta, lastik ve yol yardım hep dahil.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Uzun Dönem Araç Kiralama',
                'slug' => 'uzun-donem-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => '12 ay ve üzeri uzun dönem kiralama ile filonuzu sabit maliyetle yönetin.',
                'magicbox' => $this->genericLayout(
                    'Uzun Dönem Araç Kiralama',
                    'Sabit maliyet, esnek filo',
                    '12 ila 48 ay arası uzun dönem kiralama ile yüksek peşin sermaye yatırımına gerek kalmadan filonuzu güncel ve verimli tutun. Bakım, sigorta, kasko, lastik ve yol yardım sabit aylık ödemeye dahildir.',
                    [
                        ['icon' => 'ri-line-chart-line', 'title' => 'Öngörülebilir bütçe', 'body' => 'Sabit aylık kira ile finans planlaması kolaylaşır.'],
                        ['icon' => 'ri-refresh-line', 'title' => 'Yenilenen filo', 'body' => 'Sözleşme sonunda güncel modellere geçiş.'],
                        ['icon' => 'ri-customer-service-2-line', 'title' => '7/24 destek', 'body' => 'Çağrı merkezi, yol yardım ve yedek araç hep yanınızda.'],
                        ['icon' => 'ri-bank-card-line', 'title' => 'Vergi avantajı', 'body' => 'KDV indirimi ve gider yazma ile finansal verim.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Kısa Dönem Araç Kiralama',
                'slug' => 'kisa-donem-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Günlük, haftalık ve aylık esnek kiralama seçenekleri.',
                'magicbox' => $this->genericLayout(
                    'Kısa Dönem Araç Kiralama',
                    'Esnek planlar, hızlı teslim',
                    'Geçici proje, etkinlik veya seyahat ihtiyaçlarınız için günlük, haftalık ve aylık kiralama paketleri. Talep formundan sonraki 24 saat içinde araç teslimi.',
                    [
                        ['icon' => 'ri-time-line', 'title' => 'Hızlı teslim', 'body' => '24 saat içinde araç teslim ve adresten teslimat opsiyonu.'],
                        ['icon' => 'ri-calendar-event-line', 'title' => 'Esnek süre', 'body' => '1 günden 11 aya kadar her ihtiyaca uygun paket.'],
                        ['icon' => 'ri-shield-check-line', 'title' => 'Tam güvence', 'body' => 'Kasko, sigorta ve yol yardım dahil.'],
                        ['icon' => 'ri-map-pin-line', 'title' => 'Türkiye geneli', 'body' => 'Tüm büyük şehirlerde teslim ve iade noktası.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Filo Kiralama',
                'slug' => 'filo-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Çoklu araçtan oluşan kurumsal filolar için özel paketler.',
                'magicbox' => $this->genericLayout(
                    'Kurumsal Filo Kiralama',
                    'Filo ölçeğinde indirim ve hizmet',
                    'En az 5 araçtan başlayan filolar için özel ölçek indirimleri, tek noktadan yönetim, raporlama ve filo yöneticisi atama avantajları.',
                    [
                        ['icon' => 'ri-team-line', 'title' => 'Filo Yöneticisi', 'body' => 'Tek temas noktası ile operasyon kolaylığı.'],
                        ['icon' => 'ri-pie-chart-2-line', 'title' => 'Filo raporları', 'body' => 'Aylık tüketim, ceza, bakım ve KPI raporları.'],
                        ['icon' => 'ri-discount-percent-line', 'title' => 'Ölçek indirimi', 'body' => 'Araç sayısına göre kademeli avantajlı fiyat.'],
                        ['icon' => 'ri-calendar-todo-line', 'title' => 'Esnek sözleşme', 'body' => 'Filo büyüdükçe genişletilebilen kontrat yapısı.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Operasyonel Kiralama',
                'slug' => 'operasyonel-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Tüm operasyonel yükü tek bir tedarikçiye devreden çözüm modeli.',
                'magicbox' => $this->genericLayout(
                    'Operasyonel Kiralama',
                    'Operasyonel yükü bize bırakın',
                    'Operasyonel kiralama; aracın kullanımı dışında tüm yönetim ve servis süreçlerini tedarikçiye devreder. Şirketiniz çekirdek işine odaklanırken biz filonuzu yönetiriz.',
                    [
                        ['icon' => 'ri-settings-3-line', 'title' => 'Tam paket yönetim', 'body' => 'Bakım, sigorta, lastik, vergi ve trafik cezası takibi.'],
                        ['icon' => 'ri-money-dollar-circle-line', 'title' => 'OPEX modeli', 'body' => 'Yatırım yerine işletme gideri ile bilanço dostu yapı.'],
                        ['icon' => 'ri-recycle-line', 'title' => 'Sıfır kalıntı değer riski', 'body' => 'İkinci el satış ve değer kaybı sizi etkilemez.'],
                        ['icon' => 'ri-flashlight-line', 'title' => 'Hızlı kurulum', 'body' => 'Sözleşmeden teslime kısa sürede operasyonel olun.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Ticari Araç Kiralama',
                'slug' => 'ticari-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Hafif ticari araçlar (panelvan, kamyonet) ile lojistik ve saha ekipleriniz için özel filo.',
                'magicbox' => $this->genericLayout(
                    'Ticari Araç Kiralama',
                    'Lojistik ve saha ekipleri için',
                    'Panelvan, kamyonet ve hafif ticari araç filoları; e-ticaret, dağıtım, kurulum ve saha servis ekipleri için özel paketlerle.',
                    [
                        ['icon' => 'ri-truck-line', 'title' => 'Geniş yük hacmi', 'body' => 'Sektör ihtiyacınıza uygun yük hacmi seçenekleri.'],
                        ['icon' => 'ri-route-line', 'title' => 'Yüksek km paketi', 'body' => 'Yıllık 60.000 km ve üzeri paketler.'],
                        ['icon' => 'ri-car-line', 'title' => 'Yedek araç', 'body' => 'Servis süresince filo aksamadan devam eder.'],
                        ['icon' => 'ri-tools-line', 'title' => 'Üst donanım', 'body' => 'Markalama, raf sistemi gibi üst donanım opsiyonları.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Lüks Araç Kiralama',
                'slug' => 'luks-arac-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Üst segment ve premium markalar; üst yöneticiler ve VIP kullanım için.',
                'magicbox' => $this->genericLayout(
                    'Lüks Araç Kiralama',
                    'Premium konfor, kurumsal prestij',
                    'Mercedes-Benz E-Class, BMW 5 Serisi, Audi A6 ve üzeri segmentlerde üst yöneticileriniz ve VIP misafirleriniz için premium kiralama paketleri.',
                    [
                        ['icon' => 'ri-vip-crown-line', 'title' => 'VIP segment', 'body' => 'Üst yönetici filosuna uygun premium modeller.'],
                        ['icon' => 'ri-medal-line', 'title' => 'Tam donanım', 'body' => 'En üst donanım paketleri standart olarak.'],
                        ['icon' => 'ri-user-star-line', 'title' => 'Şoförlü opsiyon', 'body' => 'Talebe göre profesyonel şoförlü hizmet.'],
                        ['icon' => 'ri-secure-payment-line', 'title' => 'Üst güvence', 'body' => 'Genişletilmiş kasko ve sigorta paketleri.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'SUV Kiralama',
                'slug' => 'suv-kiralama',
                'page_category_id' => $cats['rental'],
                'description' => 'Kompakt, orta ve büyük SUV segmentlerinde geniş seçenek.',
                'magicbox' => $this->genericLayout(
                    'SUV Kiralama',
                    'Yüksek konfor, geniş kabin',
                    'Kompakt SUV, orta sınıf SUV ve büyük SUV segmentlerinde dizel, benzinli, hibrit ve elektrikli seçeneklerle ihtiyacınıza uygun model.',
                    [
                        ['icon' => 'ri-car-line', 'title' => 'Geniş kabin', 'body' => 'Aileler ve ekipler için ferah iç hacim.'],
                        ['icon' => 'ri-compass-3-line', 'title' => '4x4 opsiyonu', 'body' => 'Saha ve doğa koşulları için çekiş seçenekleri.'],
                        ['icon' => 'ri-shield-flash-line', 'title' => 'Üst güvenlik', 'body' => 'Yüksek pasif/aktif güvenlik donanımı.'],
                        ['icon' => 'ri-leaf-line', 'title' => 'Hibrit/EV', 'body' => 'Çevre dostu motor seçenekleri.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Şoförlü Araç Hizmeti',
                'slug' => 'soforlu-arac-hizmeti',
                'page_category_id' => $cats['rental'],
                'description' => 'Profesyonel şoförlü araç ile kurumsal etkinlikler ve VIP transfer.',
                'magicbox' => $this->genericLayout(
                    'Şoförlü Araç Hizmeti',
                    'Profesyonel şoför, VIP deneyim',
                    'Üst yönetici ulaşımı, havalimanı transferi, kurumsal etkinlik ve VIP misafir karşılaması için tecrübeli ve eğitimli şoförlerle premium hizmet.',
                    [
                        ['icon' => 'ri-user-3-line', 'title' => 'Eğitimli kadro', 'body' => 'Sürüş güvenliği ve protokol eğitimli şoförler.'],
                        ['icon' => 'ri-flight-takeoff-line', 'title' => 'Havalimanı transferi', 'body' => 'Uçuş takipli karşılama ve uğurlama servisi.'],
                        ['icon' => 'ri-time-line', 'title' => '7/24 hizmet', 'body' => 'Tatil, hafta sonu ve gece taleplerinizi karşılarız.'],
                        ['icon' => 'ri-vip-line', 'title' => 'Üst segment araç', 'body' => 'Mercedes Vito, S-Class ve eşdeğer modeller.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Filo Yönetimi',
                'slug' => 'filo-yonetimi',
                'page_category_id' => $cats['fleet'],
                'description' => 'Mevcut filonuzu profesyonel olarak yönetin: bakım, sigorta, ceza, raporlama ve maliyet optimizasyonu.',
                'magicbox' => $this->genericLayout(
                    'Filo Yönetimi',
                    'Filonuza tam zamanlı uzman ekip',
                    'Filonuzu kendisi sahip olduğunuz veya kiraladığınız fark etmeksizin uçtan uca yönetiyoruz. Bakım planlaması, sigorta yenileme, vergi-MTV takibi, trafik cezaları, lastik depolama ve filo raporlamada uzman ekibimiz operasyonu sizden alır.',
                    [
                        ['icon' => 'ri-dashboard-3-line', 'title' => 'Tek panel görünürlük', 'body' => 'Filonuza ait tüm metrikleri tek panelden izleyin.'],
                        ['icon' => 'ri-bill-line', 'title' => 'Otomatik faturalama', 'body' => 'Yakıt, bakım, ceza ve sigorta faturalarının konsolidasyonu.'],
                        ['icon' => 'ri-alarm-warning-line', 'title' => 'Proaktif uyarı', 'body' => 'Vergi, sigorta, muayene tarihleri için otomatik hatırlatma.'],
                        ['icon' => 'ri-bar-chart-2-line', 'title' => 'Aylık KPI raporu', 'body' => 'Her ay performans, maliyet ve risk özeti.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Bakım & Onarım Hizmetleri',
                'slug' => 'bakim-onarim-hizmetleri',
                'page_category_id' => $cats['fleet'],
                'description' => 'Yetkili servis ağı ile periyodik bakım, mekanik ve elektronik onarım.',
                'magicbox' => $this->genericLayout(
                    'Bakım & Onarım Hizmetleri',
                    'Yetkili servis kalitesi',
                    'Türkiye genelinde 500+ yetkili servis noktasında periyodik bakım, mekanik onarım, elektronik tanılama ve yedek parça operasyonu.',
                    [
                        ['icon' => 'ri-tools-fill', 'title' => 'Yetkili servis', 'body' => 'OEM standartlarında orijinal parça ile bakım.'],
                        ['icon' => 'ri-map-pin-2-line', 'title' => 'Geniş ağ', 'body' => '81 ilde anlaşmalı servis noktaları.'],
                        ['icon' => 'ri-time-line', 'title' => 'Hızlı randevu', 'body' => 'Online randevu ile minimum bekleme süresi.'],
                        ['icon' => 'ri-car-line', 'title' => 'Yedek araç', 'body' => 'Servis süresince ücretsiz yedek araç.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Lastik Hizmetleri',
                'slug' => 'lastik-hizmetleri',
                'page_category_id' => $cats['fleet'],
                'description' => 'Yaz/kış lastik değişimi, depolama, balans ve rot ayarı.',
                'magicbox' => $this->genericLayout(
                    'Lastik Hizmetleri',
                    'Yaz-kış lastik tek pakette',
                    'Mevsimsel lastik değişimi, ücretsiz depolama, balans, rot ayarı ve hasarlı lastik değişim hizmetleri filo paketinin standart parçasıdır.',
                    [
                        ['icon' => 'ri-snowflake-line', 'title' => 'Kış lastiği', 'body' => 'Mevsim öncesi otomatik takvimleme.'],
                        ['icon' => 'ri-archive-line', 'title' => 'Ücretsiz depolama', 'body' => 'Kullanılmayan lastikleri depolarımızda saklarız.'],
                        ['icon' => 'ri-equalizer-line', 'title' => 'Balans & rot', 'body' => 'Periyodik balans-rot kontrolü dahil.'],
                        ['icon' => 'ri-shield-cross-line', 'title' => 'Hasar koruma', 'body' => 'Hasarlı lastik için değişim güvencesi.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Sigorta ve Kasko',
                'slug' => 'sigorta-ve-kasko',
                'page_category_id' => $cats['fleet'],
                'description' => 'Trafik sigortası, kasko ve genişletilmiş güvence paketleri.',
                'magicbox' => $this->genericLayout(
                    'Sigorta ve Kasko Hizmetleri',
                    'Tam kapsamlı güvence',
                    'Trafik sigortası, kasko ve ihtiyaca göre genişletilmiş güvence paketleri sözleşmenize dahil edilir. Hasar süreçlerinizi tek noktadan takip ederiz.',
                    [
                        ['icon' => 'ri-shield-check-line', 'title' => 'Tam kasko', 'body' => 'Standart paketlere dahil tam kapsamlı kasko.'],
                        ['icon' => 'ri-customer-service-2-line', 'title' => 'Hasar yönetimi', 'body' => 'Hasar bildirim ve takip uçtan uca.'],
                        ['icon' => 'ri-flag-line', 'title' => 'Yurt dışı', 'body' => 'Yeşil kart ile sınır ötesi sürüş güvencesi.'],
                        ['icon' => 'ri-secure-payment-line', 'title' => 'Genişletilmiş güvence', 'body' => 'Cam, lastik, anahtar gibi opsiyonel teminatlar.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Kaza & Yol Yardım Asistans',
                'slug' => 'kaza-yol-yardim-asistans',
                'page_category_id' => $cats['fleet'],
                'description' => '7/24 yol yardım, çekici, lastik patlağı ve kaza asistans servisleri.',
                'magicbox' => $this->genericLayout(
                    'Kaza & Yol Yardım',
                    '7/24 yanınızdayız',
                    'Türkiye genelinde 7 gün 24 saat yol yardım, çekici, lastik müdahale, akü takviye ve kaza sonrası asistans hizmetlerimiz tüm filo paketlerinde standarttır.',
                    [
                        ['icon' => 'ri-phone-line', 'title' => 'Tek numara', 'body' => '7/24 çağrı merkezi ile tek numara üzerinden destek.'],
                        ['icon' => 'ri-truck-line', 'title' => 'Çekici', 'body' => 'Türkiye genelinde anlaşmalı çekici ağı.'],
                        ['icon' => 'ri-flashlight-line', 'title' => 'Akü takviye', 'body' => 'Yerinde akü takviyesi ile zaman kaybı yok.'],
                        ['icon' => 'ri-car-line', 'title' => 'Yedek araç', 'body' => 'Hasar süresince ikame araç temini.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Yedek Araç Hizmeti',
                'slug' => 'yedek-arac-hizmeti',
                'page_category_id' => $cats['fleet'],
                'description' => 'Servis ve hasar süresince ikame araç hizmeti.',
                'magicbox' => $this->genericLayout(
                    'Yedek Araç Hizmeti',
                    'Filonuz hiç durmaz',
                    'Aracınız servisteyken veya hasar onarımı sürerken size eşdeğer ikame araç sağlıyoruz. Operasyonunuz aksamadan devam eder.',
                    [
                        ['icon' => 'ri-car-line', 'title' => 'Eşdeğer araç', 'body' => 'Segmentinize uygun yedek araç tahsisi.'],
                        ['icon' => 'ri-time-line', 'title' => 'Hızlı tahsis', 'body' => 'Aynı gün veya 24 saat içinde teslim.'],
                        ['icon' => 'ri-map-pin-2-line', 'title' => 'Türkiye geneli', 'body' => 'İl bağımsız ikame araç temini.'],
                        ['icon' => 'ri-shield-check-line', 'title' => 'Tam güvence', 'body' => 'Kasko ve sigorta dahil teslim.'],
                    ],
                    $cta,
                    $commonFaq,
                ),
            ],
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'page_category_id' => $cats['corporate'],
                'description' => 'Filo kiralama ve filo yönetimi alanında uzman ekibimiz ile yıllardır kurumsal müşterilerin yanındayız.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Kurumsal',
                        'title' => 'Hakkımızda',
                        'subtitle' => 'Türkiye filo kiralama sektöründe uzun yıllara dayanan deneyim, binlerce araçlık aktif filo ve uzman ekip.',
                    ],
                    'intro' => 'Kurumsal araç kiralama ve filo yönetimi alanında müşterilerimize katma değer üretmek için kuruluşumuzdan bu yana büyüyen ekibimiz, geniş hizmet ağımız ve modern filomuz ile hizmet veriyoruz. Önceliğimiz; öngörülebilir maliyet, yüksek hizmet kalitesi ve şeffaf iletişim.',
                    'sections' => [
                        ['type' => 'features', 'title' => 'Rakamlarla Biz', 'items' => [
                            ['icon' => 'ri-car-line', 'title' => '10.000+ araç', 'body' => 'Sürekli yenilenen modern filo.'],
                            ['icon' => 'ri-team-line', 'title' => '350+ çalışan', 'body' => 'Uzman ve müşteri odaklı ekip.'],
                            ['icon' => 'ri-map-pin-line', 'title' => '81 ilde hizmet', 'body' => 'Türkiye genelinde teslim ve servis ağı.'],
                            ['icon' => 'ri-trophy-line', 'title' => '15 yıl deneyim', 'body' => 'Sektörde köklü tecrübe ve referans.'],
                        ]],
                        ['type' => 'text', 'title' => 'Vizyonumuz', 'body' => 'Türkiye\'nin filo kiralama ve mobilite alanında en güvenilir çözüm ortağı olmak; sürdürülebilir ve dijital filo deneyimini sektörün standardı haline getirmek.'],
                        ['type' => 'text', 'title' => 'Misyonumuz', 'body' => 'Müşterilerimize öngörülebilir maliyet, yüksek kalite ve uçtan uca hizmet sağlayan kurumsal mobilite çözümleri sunmak; teknoloji, sürdürülebilirlik ve insan odaklı yaklaşımı işin merkezine almak.'],
                    ],
                    'final_cta' => ['title' => 'Birlikte çalışalım', 'subtitle' => 'Filonuz için uzman ekibimizden teklif alın.', 'button' => ['label' => 'İletişime geç', 'href' => '/biz-sizi-arayalim']],
                ],
            ],
            [
                'title' => 'Sürdürülebilirlik',
                'slug' => 'surdurulebilirlik',
                'page_category_id' => $cats['corporate'],
                'description' => 'Çevreye, topluma ve gelecek nesillere karşı sorumluluğumuz: ESG yaklaşımımız.',
                'magicbox' => $this->genericLayout(
                    'Sürdürülebilirlik',
                    'Çevre, toplum, yönetişim',
                    'ESG (Environmental, Social, Governance) çerçevesi ile filomuzu, operasyonumuzu ve tedarik zincirimizi sürdürülebilirlik ilkeleriyle yeniden tasarlıyoruz.',
                    [
                        ['icon' => 'ri-leaf-line', 'title' => 'Karbon ayak izi', 'body' => 'Yıllık karbon ayak izi ölçüm ve dengeleme programı.'],
                        ['icon' => 'ri-charging-pile-line', 'title' => 'Elektrikli geçiş', 'body' => 'Filomuzda elektrikli ve hibrit araç payını her yıl artırıyoruz.'],
                        ['icon' => 'ri-recycle-line', 'title' => 'Döngüsel ekonomi', 'body' => 'Yedek parça, lastik ve akü geri kazanım programları.'],
                        ['icon' => 'ri-community-line', 'title' => 'Toplumsal katkı', 'body' => 'Eğitim, çevre ve afet projelerinde aktif rol.'],
                    ],
                    [['label' => 'Sürdürülebilirlik raporumuz', 'href' => '#', 'icon' => 'ri-file-pdf-line']],
                    [],
                ),
            ],
            [
                'title' => 'Kariyer',
                'slug' => 'kariyer',
                'page_category_id' => $cats['corporate'],
                'description' => 'Bizimle çalışmak isteyenler için kariyer fırsatları.',
                'magicbox' => $this->genericLayout(
                    'Kariyer',
                    'Aramıza katılın',
                    'Hızla büyüyen, yenilikçi ve insan odaklı bir ekibin parçası olun. Açık pozisyonlarımızı inceleyin, başvurunuzu bizimle paylaşın.',
                    [
                        ['icon' => 'ri-rocket-2-line', 'title' => 'Hızlı büyüme', 'body' => 'Hızlı kariyer ve gelişim fırsatları.'],
                        ['icon' => 'ri-graduation-cap-line', 'title' => 'Eğitim & Gelişim', 'body' => 'Yıl boyu sürekli eğitim ve sertifika programları.'],
                        ['icon' => 'ri-heart-line', 'title' => 'Yan haklar', 'body' => 'Özel sağlık, yemek, ulaşım ve daha fazlası.'],
                        ['icon' => 'ri-global-line', 'title' => 'Esnek çalışma', 'body' => 'Hibrit ve esnek çalışma modeli.'],
                    ],
                    [['label' => 'Açık pozisyonları gör', 'href' => '#', 'icon' => 'ri-briefcase-line']],
                    [],
                ),
            ],
            [
                'title' => 'Basın Odası',
                'slug' => 'basin-odasi',
                'page_category_id' => $cats['corporate'],
                'description' => 'Basın bültenleri, kurumsal kimlik ve medya iletişim bilgileri.',
                'magicbox' => $this->genericLayout(
                    'Basın Odası',
                    'Medya ve iletişim',
                    'Basın bültenlerimiz, kurumsal kimlik dokümanları, logo paketleri ve basın iletişimi için bilgilerimiz bu sayfada.',
                    [
                        ['icon' => 'ri-newspaper-line', 'title' => 'Basın bültenleri', 'body' => 'Güncel duyuru ve haberlere erişim.'],
                        ['icon' => 'ri-image-line', 'title' => 'Logo paketi', 'body' => 'Kurumsal logo, renk paleti ve kullanım kılavuzu.'],
                        ['icon' => 'ri-mail-line', 'title' => 'Basın iletişim', 'body' => 'press@example.com adresinden ulaşabilirsiniz.'],
                        ['icon' => 'ri-microphone-line', 'title' => 'Söyleşi talepleri', 'body' => 'Yöneticilerimizle röportaj ve etkinlik talepleri.'],
                    ],
                    [],
                    [],
                ),
            ],
            [
                'title' => 'Kurumsal Sosyal Sorumluluk',
                'slug' => 'kurumsal-sosyal-sorumluluk',
                'page_category_id' => $cats['corporate'],
                'description' => 'Toplum, çevre ve eğitim odaklı KSS projelerimiz.',
                'magicbox' => $this->genericLayout(
                    'Kurumsal Sosyal Sorumluluk',
                    'Topluma değer üretiyoruz',
                    'Eğitime, çevreye ve afet bölgelerine yönelik kurumsal sosyal sorumluluk projeleriyle topluma değer üretmek önceliğimiz.',
                    [
                        ['icon' => 'ri-book-open-line', 'title' => 'Eğitim projeleri', 'body' => 'Burs ve mentorluk programları.'],
                        ['icon' => 'ri-leaf-line', 'title' => 'Ağaçlandırma', 'body' => 'Her yıl artan kurumsal ağaçlandırma kampanyaları.'],
                        ['icon' => 'ri-hand-heart-line', 'title' => 'Afet desteği', 'body' => 'Afet bölgelerine araç ve lojistik desteği.'],
                        ['icon' => 'ri-women-line', 'title' => 'Eşit fırsat', 'body' => 'Çeşitlilik ve kapsayıcılık programları.'],
                    ],
                    [],
                    [],
                ),
            ],
            [
                'title' => 'Sıkça Sorulan Sorular',
                'slug' => 'sikca-sorulan-sorular',
                'page_category_id' => $cats['support'],
                'description' => 'Kiralama, sözleşme, sigorta, bakım ve operasyon konularında sıkça sorulan sorular.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Yardım',
                        'title' => 'Sıkça Sorulan Sorular',
                        'subtitle' => 'Aradığınız cevap büyük ihtimalle aşağıdaki başlıklardan birinde.',
                    ],
                    'intro' => 'Kiralama, sözleşme, sigorta, bakım ve operasyon konularında en çok karşılaştığımız soruları bu sayfada topladık. Eğer sorunuzun cevabını bulamazsanız, müşteri temsilcimize her zaman ulaşabilirsiniz.',
                    'faqs' => array_merge($commonFaq, [
                        ['q' => 'Sözleşme imzaladıktan sonra ne kadar sürede araç teslim edilir?', 'a' => 'Standart araçlarda 5-15 iş günü, özel donanım/marka isteklerinde ise model sevkiyatına göre süre değişebilir.'],
                        ['q' => 'Yıllık km aşımı olursa ne olur?', 'a' => 'Sözleşmedeki yıllık km limitinin üzerinde kullanım için her km başına önceden anlaşılmış bir tutar uygulanır.'],
                        ['q' => 'Aracı yurt dışına çıkarabilir miyim?', 'a' => 'Yeşil kart sigortası ile mümkündür. Sözleşmeye yurt dışı kullanım maddesinin eklenmesi gerekir.'],
                        ['q' => 'Sözleşme süresi sonunda aracı satın alabilir miyim?', 'a' => 'Belirli paketlerimizde sözleşme sonu satın alma opsiyonu sunulmaktadır. Detaylar müşteri temsilcinizden öğrenilebilir.'],
                    ]),
                ],
            ],
            [
                'title' => 'İletişim ve Destek',
                'slug' => 'iletisim-ve-destek',
                'page_category_id' => $cats['support'],
                'description' => 'Müşteri hizmetleri, çağrı merkezi, satış ve destek kanalları.',
                'magicbox' => $this->genericLayout(
                    'İletişim ve Destek',
                    'Bize ulaşın',
                    'Bize farklı kanallardan ulaşabilirsiniz. Çağrı merkezimiz 7/24 hizmetinizdedir, satış ekibimiz hafta içi 09:00-18:00 saatleri arasında dönüş yapar.',
                    [
                        ['icon' => 'ri-phone-line', 'title' => '7/24 Çağrı merkezi', 'body' => '0850 000 00 00'],
                        ['icon' => 'ri-mail-line', 'title' => 'E-posta', 'body' => 'info@example.com'],
                        ['icon' => 'ri-map-pin-line', 'title' => 'Genel müdürlük', 'body' => 'İstanbul, Türkiye'],
                        ['icon' => 'ri-customer-service-line', 'title' => 'Satış ekibi', 'body' => 'satis@example.com'],
                    ],
                    [['label' => 'Bizi arayalım formu', 'href' => '/biz-sizi-arayalim', 'icon' => 'ri-phone-line']],
                    [],
                ),
            ],
            [
                'title' => 'Online İşlemler',
                'slug' => 'online-islemler',
                'page_category_id' => $cats['support'],
                'description' => 'Trafik cezası, ruhsat, sözleşme ve iade işlemlerini online yapın.',
                'magicbox' => $this->genericLayout(
                    'Online İşlemler',
                    'Tüm işlemler tek panelde',
                    'Trafik cezası ödeme, sözleşme görüntüleme, iade randevusu, fatura indirme ve hasar bildirimi gibi tüm işlemleri online müşteri panelinden tamamlayabilirsiniz.',
                    [
                        ['icon' => 'ri-file-list-3-line', 'title' => 'Sözleşme görüntüle', 'body' => 'Aktif sözleşmelerinize tek tıkla erişin.'],
                        ['icon' => 'ri-bill-line', 'title' => 'Fatura indir', 'body' => 'Geçmiş faturalarınızı PDF olarak indirin.'],
                        ['icon' => 'ri-error-warning-line', 'title' => 'Hasar bildir', 'body' => 'Online hasar bildirim ve takip ekranı.'],
                        ['icon' => 'ri-calendar-check-line', 'title' => 'İade randevusu', 'body' => 'Sözleşme sonu araç iade randevusu alın.'],
                    ],
                    [['label' => 'Online panele git', 'href' => '#', 'icon' => 'ri-login-circle-line']],
                    [],
                ),
            ],
            [
                'title' => 'Gizlilik Politikası',
                'slug' => 'gizlilik-politikasi',
                'page_category_id' => $cats['legal'],
                'description' => 'Kişisel verilerinizin işlenmesi ve gizlilik ilkelerimiz.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Hukuki', 'title' => 'Gizlilik Politikası',
                        'subtitle' => 'Kişisel verilerinizi nasıl topluyor, işliyor ve koruyoruz.',
                    ],
                    'intro' => 'Bu Gizlilik Politikası, web sitemiz ve hizmetlerimiz aracılığıyla topladığımız kişisel verilerin nasıl işlendiğini, hangi amaçla kullanıldığını ve haklarınızı açıklamaktadır. KVKK ve GDPR mevzuatına uygun ilkelerle hareket ederiz.',
                    'sections' => [
                        ['type' => 'text', 'title' => 'Toplanan Veriler', 'body' => 'Adınız, iletişim bilgileriniz, kimlik bilgileriniz, sürücü belgesi bilgileri ve hizmet kullanım bilgileri gibi veriler hizmet sunumu için toplanır.'],
                        ['type' => 'text', 'title' => 'Kullanım Amaçları', 'body' => 'Sözleşme yönetimi, müşteri hizmetleri, faturalama, mevzuat gereği bildirim ve hizmet kalitesinin artırılması amaçlarıyla işlenir.'],
                        ['type' => 'text', 'title' => 'Haklarınız', 'body' => 'KVKK kapsamında verilerinize erişme, düzeltme, silme ve işlemeye itiraz etme haklarınız bulunmaktadır. Talepleriniz için kvkk@example.com adresine başvurabilirsiniz.'],
                    ],
                ],
            ],
            [
                'title' => 'KVKK Aydınlatma Metni',
                'slug' => 'kvkk-aydinlatma-metni',
                'page_category_id' => $cats['legal'],
                'description' => '6698 sayılı KVKK kapsamında aydınlatma metni.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Hukuki', 'title' => 'KVKK Aydınlatma Metni',
                        'subtitle' => '6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında bilgilendirme.',
                    ],
                    'intro' => '6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) kapsamında veri sorumlusu sıfatıyla, kişisel verilerinizin işlenme amacı, hukuki sebebi, aktarımı ve haklarınız hakkında bilgilendirme yapılmaktadır.',
                    'sections' => [
                        ['type' => 'text', 'title' => 'Veri Sorumlusu', 'body' => 'İlgili kişiler, KVKK kapsamında veri sorumlusu sıfatıyla işlenen verilerine ilişkin başvurularını şirketimize iletebilir.'],
                        ['type' => 'text', 'title' => 'İşleme Amaçları', 'body' => 'Sözleşmenin kurulması, yürütülmesi, hukuki yükümlülüklerin yerine getirilmesi ve meşru menfaatler kapsamında işleme yapılır.'],
                        ['type' => 'text', 'title' => 'Aktarım', 'body' => 'Veriler, mevzuat gereği yetkili kamu kurum ve kuruluşları, sigorta şirketleri ve hizmet aldığımız iş ortaklarımız ile sınırlı şekilde paylaşılır.'],
                    ],
                ],
            ],
            [
                'title' => 'Çerez Politikası',
                'slug' => 'cerez-politikasi',
                'page_category_id' => $cats['legal'],
                'description' => 'Web sitemizde kullanılan çerezler ve kullanım amaçları.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Hukuki', 'title' => 'Çerez Politikası',
                        'subtitle' => 'Web sitemizde kullanılan çerezler hakkında bilgilendirme.',
                    ],
                    'intro' => 'Web sitemizi kullanırken size daha iyi bir deneyim sunmak amacıyla çerezler (cookie) kullanılmaktadır. Bu sayfa hangi çerezlerin kullanıldığını ve nasıl yönetebileceğinizi açıklar.',
                    'sections' => [
                        ['type' => 'text', 'title' => 'Çerez Türleri', 'body' => 'Zorunlu çerezler, performans çerezleri, işlevsel çerezler ve hedefleme/reklam çerezleri kullanılır.'],
                        ['type' => 'text', 'title' => 'Çerezleri Yönetme', 'body' => 'Tarayıcı ayarlarınızdan çerezleri silebilir veya engelleyebilirsiniz. Bazı çerezleri devre dışı bırakmak sitenin işlevselliğini kısıtlayabilir.'],
                    ],
                ],
            ],
            [
                'title' => 'Yasal Uyarı',
                'slug' => 'yasal-uyari',
                'page_category_id' => $cats['legal'],
                'description' => 'Web sitemizin kullanım koşulları ve yasal uyarı.',
                'magicbox' => [
                    'hero' => [
                        'badge' => 'Hukuki', 'title' => 'Yasal Uyarı',
                        'subtitle' => 'Web sitemizin kullanımına ilişkin yasal koşullar.',
                    ],
                    'intro' => 'Web sitemizdeki tüm içerikler bilgilendirme amaçlıdır. İçeriklerin doğruluğu için azami özen gösterilmekle birlikte, içeriklerin değişmesi veya hata içermesi durumunda sorumluluk kabul edilmez.',
                    'sections' => [
                        ['type' => 'text', 'title' => 'Telif Hakları', 'body' => 'Web sitesinde yer alan tüm görsel, metin ve marka unsurları şirketimizin mülkiyetindedir. İzinsiz kullanım, kopyalama veya çoğaltma yasaktır.'],
                        ['type' => 'text', 'title' => 'Sorumluluk Reddi', 'body' => 'Sitedeki bilgilerin kullanımı kullanıcının sorumluluğundadır. Doğrudan veya dolaylı zararlardan şirketimiz sorumlu tutulamaz.'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Hedef Filo benzeri jenerik sayfa şablonu üretir.
     *
     * @param  list<array<string,string>>  $features
     * @param  list<array<string,string>>  $ctas
     * @param  list<array<string,string>>  $faqs
     * @return array<string,mixed>
     */
    private function genericLayout(string $title, string $heroSubtitle, string $intro, array $features, array $ctas, array $faqs): array
    {
        return [
            'hero' => [
                'badge' => 'Hizmet',
                'title' => $title,
                'subtitle' => $heroSubtitle,
            ],
            'intro' => $intro,
            'ctas' => $ctas,
            'sections' => [
                ['type' => 'features', 'title' => 'Avantajlar', 'items' => $features],
            ],
            'faqs' => $faqs,
            'final_cta' => [
                'title' => 'Hemen ilk adımı atın',
                'subtitle' => 'Size özel teklif birkaç dakika içinde hazırlanır.',
                'button' => ['label' => 'Teklif al', 'href' => '/biz-sizi-arayalim'],
            ],
        ];
    }
}
