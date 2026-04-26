<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Faq;
use App\Models\HomePartner;
use App\Models\HomeServiceTile;
use App\Models\HomeTestimonial;
use App\Models\Slider;
use Illuminate\Database\Seeder;

/**
 * Ana sayfa: hero slayt, hizmet kutuları, partnerler, yorumlar, SSS ve anasayfa filo araçları.
 * Filo demo araçları için önce FleetDemoSeeder çalışmış olmalı.
 */
class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'sort_order' => 0,
                'is_active' => true,
                'badge' => 'Dev Filo',
                'title' => 'Ticari Araçlarla',
                'title_highlight' => 'Yükünüzü Hafifletiyoruz',
                'subtitle' => 'Ticari araçları kiralayın, sermayenizi daha verimli kullanın.',
                'description' => null,
                'image_1' => 'v1/assets/image-1.png',
                'image_2' => 'v1/assets/araba.png',
                'link' => null,
            ],
            [
                'sort_order' => 1,
                'is_active' => true,
                'badge' => 'Dev Filo',
                'title' => 'Ticari Araçlarla',
                'title_highlight' => 'Yükünüzü Hafifletiyoruz',
                'subtitle' => 'Ticari araçları kiralayın, sermayenizi daha verimli kullanın.',
                'description' => null,
                'image_1' => 'v1/assets/image-1.png',
                'image_2' => 'v1/assets/araba-2.png',
                'link' => null,
            ],
            [
                'sort_order' => 2,
                'is_active' => true,
                'badge' => 'Dev Filo',
                'title' => 'Ticari Araçlarla',
                'title_highlight' => 'Yükünüzü Hafifletiyoruz',
                'subtitle' => 'Ticari araçları kiralayın, sermayenizi daha verimli kullanın.',
                'description' => null,
                'image_1' => 'v1/assets/image-2.png',
                'image_2' => 'v1/assets/araba-3.png',
                'link' => null,
            ],
        ];

        foreach ($slides as $row) {
            Slider::query()->updateOrCreate(
                ['sort_order' => $row['sort_order']],
                $row
            );
        }

        $tiles = [
            [
                'sort_order' => 0,
                'is_active' => true,
                'icon' => 'ri-flight-takeoff-line',
                'title' => 'Havalimanı transferi',
                'description' => 'Büyük terminallere zamanında ve konforlu ulaşım.',
                'link_url' => null,
                'image' => null,
            ],
            [
                'sort_order' => 1,
                'is_active' => true,
                'icon' => 'ri-user-star-line',
                'title' => 'Şoförlü hizmet',
                'description' => 'Profesyonel sürücülerle kurumsal ve VIP deneyim.',
                'link_url' => null,
                'image' => null,
            ],
            [
                'sort_order' => 2,
                'is_active' => true,
                'icon' => 'ri-building-4-line',
                'title' => 'Kurumsal filo',
                'description' => 'İşletmenize özel filo yönetimi ve sözleşme seçenekleri.',
                'link_url' => null,
                'image' => null,
            ],
        ];

        foreach ($tiles as $row) {
            HomeServiceTile::query()->updateOrCreate(
                ['sort_order' => $row['sort_order']],
                $row
            );
        }

        $partnerNames = ['FERRARI', 'BENTLEY', 'BMW', 'AUDI', 'TESLA', 'ROLLS ROYCE'];
        foreach ($partnerNames as $i => $name) {
            HomePartner::query()->updateOrCreate(
                ['name' => $name],
                [
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
        }

        $testimonials = [
            [
                'sort_order' => 0,
                'is_active' => true,
                'name' => 'Ahmet Yılmaz',
                'role' => 'Operasyon Müdürü',
                'quote' => 'Filo genişletme sürecinde ekibin hızlı dönüşü ve şeffaf fiyatlandırma bizi çok rahatlattı.',
                'rating' => 5,
                'avatar' => null,
            ],
            [
                'sort_order' => 1,
                'is_active' => true,
                'name' => 'Elif Kaya',
                'role' => 'Finans',
                'quote' => 'Uzun dönem kiralama ile nakit akışını öngörülebilir tuttuk; raporlama net.',
                'rating' => 5,
                'avatar' => null,
            ],
            [
                'sort_order' => 2,
                'is_active' => true,
                'name' => 'Can Demir',
                'role' => 'Lojistik',
                'quote' => 'Araç teslimatları planlandığı gibi; sahada sorun yaşamadık.',
                'rating' => 5,
                'avatar' => null,
            ],
        ];

        foreach ($testimonials as $row) {
            HomeTestimonial::query()->updateOrCreate(
                [
                    'name' => $row['name'],
                    'quote' => $row['quote'],
                ],
                $row
            );
        }

        $faqs = [
            [
                'sort_order' => 0,
                'is_active' => true,
                'question' => 'Kiralama için hangi belgeler gerekir?',
                'answer_body' => 'Geçerli sürücü belgesi, kimlik ve şirket adına fatura için vergi levhası / imza sirküleri talep edilebilir. Kurumsal müşterilerde yetkili imza ve ticari sicil özeti istenebilir.',
            ],
            [
                'sort_order' => 1,
                'is_active' => true,
                'question' => 'Sigorta fiyata dahil mi?',
                'answer_body' => 'Standart paketlerde teminatlar sözleşmede açıkça listelenir. İsteğe bağlı genişletilmiş güvence paketleri için temsilcimiz teklif sunar.',
            ],
            [
                'sort_order' => 2,
                'is_active' => true,
                'question' => 'Aracı farklı lokasyonda iade edebilir miyim?',
                'answer_body' => 'Tek yön iade bazı hatlarda mümkündür; mesafeye göre ek ücret uygulanabilir. Talebinizi ön görüşmede netleştirelim.',
            ],
        ];

        foreach ($faqs as $row) {
            $body = $row['answer_body'];
            Faq::query()->updateOrCreate(
                ['question' => $row['question']],
                [
                    'sort_order' => $row['sort_order'],
                    'is_active' => $row['is_active'],
                    'answer_body' => $body,
                    'answer' => mb_substr($body, 0, 255),
                ]
            );
        }

        $featured = [
            'toyota-corolla-15-hybrid' => 1,
            'vw-passat-16-tdi' => 2,
            'dacia-duster-15-dci' => 3,
        ];

        foreach ($featured as $slug => $order) {
            Car::query()->where('slug', $slug)->update([
                'home_featured' => true,
                'home_sort_order' => $order,
            ]);
        }

        $this->command?->info('Ana sayfa içeriği hazır (slayt, kutular, partnerler, yorumlar, SSS, öne çıkan araçlar).');
    }
}
