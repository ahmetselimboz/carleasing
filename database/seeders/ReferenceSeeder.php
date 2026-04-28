<?php

namespace Database\Seeders;

use App\Models\Reference;
use Illuminate\Database\Seeder;

/**
 * Referanslar (kurumsal müşteriler) için demo veriler.
 */
class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $references = [
            [
                'name' => 'Aselsan',
                'title' => 'Savunma Sanayii — Filo Yönetimi',
                'image' => null,
                'link' => 'https://www.aselsan.com.tr',
                'detail' => 'Aselsan kurumsal filosunun bakım, sigorta ve operasyon yönetimini sürdürüyoruz.',
            ],
            [
                'name' => 'Türk Telekom',
                'title' => 'Telekomünikasyon — Saha Operasyonu',
                'image' => null,
                'link' => 'https://www.turktelekom.com.tr',
                'detail' => 'Saha operasyonları için ticari ve binek araçlardan oluşan filo desteği sağlıyoruz.',
            ],
            [
                'name' => 'Ziraat Bankası',
                'title' => 'Bankacılık — Yönetici Filosu',
                'image' => null,
                'link' => 'https://www.ziraatbank.com.tr',
                'detail' => 'Üst yönetici filosunda uzun dönem kiralama ve VIP servis hizmeti.',
            ],
            [
                'name' => 'Türkiye İş Bankası',
                'title' => 'Bankacılık — Filo Kiralama',
                'image' => null,
                'link' => 'https://www.isbank.com.tr',
                'detail' => 'Şube ve genel müdürlük yöneticileri için uzun dönem filo kiralama.',
            ],
            [
                'name' => 'Yapı Kredi',
                'title' => 'Bankacılık — Operasyonel Kiralama',
                'image' => null,
                'link' => 'https://www.yapikredi.com.tr',
                'detail' => 'Operasyonel kiralama modeli ile bilanço dostu filo çözümü.',
            ],
            [
                'name' => 'Akbank',
                'title' => 'Bankacılık — Bölge Müdürlüğü Filosu',
                'image' => null,
                'link' => 'https://www.akbank.com',
                'detail' => 'Bölge müdürlüğü ve denetim ekipleri için filo desteği.',
            ],
            [
                'name' => 'Migros',
                'title' => 'Perakende — Lojistik Filosu',
                'image' => null,
                'link' => 'https://www.migros.com.tr',
                'detail' => 'Hızlı teslimat operasyonu için ticari panelvan filosu.',
            ],
            [
                'name' => 'Trendyol',
                'title' => 'E-Ticaret — Son Mil Teslimat',
                'image' => null,
                'link' => 'https://www.trendyol.com',
                'detail' => 'Son mil teslimat için elektrikli ve hibrit araç filosu.',
            ],
            [
                'name' => 'Getir',
                'title' => 'Q-Commerce — Hızlı Teslimat',
                'image' => null,
                'link' => 'https://getir.com',
                'detail' => 'Şehir içi hızlı teslimat operasyonu için kompakt araçlar.',
            ],
            [
                'name' => 'Turkcell',
                'title' => 'Telekomünikasyon — Saha Servis',
                'image' => null,
                'link' => 'https://www.turkcell.com.tr',
                'detail' => 'Saha servis ve kurulum ekipleri için ticari araç filosu.',
            ],
            [
                'name' => 'Vodafone',
                'title' => 'Telekomünikasyon — Bayi Operasyonu',
                'image' => null,
                'link' => 'https://www.vodafone.com.tr',
                'detail' => 'Bayi denetim ve saha operasyonları için filo çözümleri.',
            ],
            [
                'name' => 'Garanti BBVA',
                'title' => 'Bankacılık — Üst Yönetici Filosu',
                'image' => null,
                'link' => 'https://www.garantibbva.com.tr',
                'detail' => 'Üst yönetici filosunda lüks segmentte uzun dönem kiralama.',
            ],
            [
                'name' => 'Anadolu Sigorta',
                'title' => 'Sigorta — Eksper Filosu',
                'image' => null,
                'link' => 'https://www.anadolusigorta.com.tr',
                'detail' => 'Eksper ve saha denetim ekipleri için filo kiralama.',
            ],
            [
                'name' => 'Allianz',
                'title' => 'Sigorta — Hasar Operasyonu',
                'image' => null,
                'link' => 'https://www.allianz.com.tr',
                'detail' => 'Hasar yerinde inceleme ekipleri için ticari filo.',
            ],
            [
                'name' => 'Coca-Cola İçecek',
                'title' => 'FMCG — Satış Filosu',
                'image' => null,
                'link' => 'https://www.cci.com.tr',
                'detail' => 'Satış ekipleri için binek ve hafif ticari karma filo.',
            ],
            [
                'name' => 'Anadolu Efes',
                'title' => 'FMCG — Bölge Saha Filosu',
                'image' => null,
                'link' => 'https://www.anadoluefes.com',
                'detail' => 'Bölge saha satış ekipleri için uzun dönem kiralama.',
            ],
            [
                'name' => 'Arçelik',
                'title' => 'Beyaz Eşya — Servis Filosu',
                'image' => null,
                'link' => 'https://www.arcelik.com.tr',
                'detail' => 'Yetkili servis ekipleri için ticari panelvan filosu.',
            ],
            [
                'name' => 'Ford Otosan',
                'title' => 'Otomotiv — Yönetici Filosu',
                'image' => null,
                'link' => 'https://www.fordotosan.com.tr',
                'detail' => 'Üst yönetici ve mühendis kadrosu filosu.',
            ],
            [
                'name' => 'Aselsan Konya',
                'title' => 'Savunma — Bölge Filosu',
                'image' => null,
                'link' => 'https://www.aselsan.com.tr',
                'detail' => 'Konya kampüsü saha ve yönetici filosu.',
            ],
            [
                'name' => 'BSH Ev Aletleri',
                'title' => 'Beyaz Eşya — Servis Operasyonu',
                'image' => null,
                'link' => 'https://www.bsh-group.com/tr',
                'detail' => 'Yetkili servis ve montaj ekipleri için filo desteği.',
            ],
        ];

        foreach ($references as $ref) {
            Reference::query()->updateOrCreate(
                ['name' => $ref['name']],
                array_merge($ref, ['is_active' => true])
            );
        }

        $this->command?->info('Referanslar yüklendi: '.count($references).' kayıt.');
    }
}
