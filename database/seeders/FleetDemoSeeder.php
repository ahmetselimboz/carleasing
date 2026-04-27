<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarAttribute;
use App\Models\CarAttributeCategory;
use App\Models\CarAttributePivot;
use App\Models\CarAttributeValue;
use App\Models\CarDownPayment;
use App\Models\CarDuration;
use App\Models\CarExtraService;
use App\Models\CarKilometerOption;
use App\Models\CarPackage;
use App\Models\CarPriceMatrix;
use App\Models\RentalRequest;
use Illuminate\Database\Seeder;

/**
 * Filo demo verisi: katalog → araçlar → fiyat matrisi → özellik pivotları → örnek talep.
 * Tekrar çalıştırıldığında mümkün olduğunca firstOrCreate ile çoğaltmaz.
 */
class FleetDemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- Katalog: peşinat ---
        $dp15 = CarDownPayment::query()->firstOrCreate(
            ['amount' => '%15 Peşinat'],
            ['is_active' => true]
        );
        $dp25 = CarDownPayment::query()->firstOrCreate(
            ['amount' => '%25 Peşinat'],
            ['is_active' => true]
        );
        $dp0 = CarDownPayment::query()->firstOrCreate(
            ['amount' => 'Peşinatsız'],
            ['is_active' => true]
        );

        // --- Paketler ---
        $pkgFull = CarPackage::query()->firstOrCreate(
            ['name' => 'Tam Paket'],
            [
                'description' => 'Kasko, bakım, lastik, vergi ve cam dahil.',
                'is_active' => true,
            ]
        );
        $pkgFlex = CarPackage::query()->firstOrCreate(
            ['name' => 'Esnek Paket'],
            [
                'description' => 'Kasko + periyodik bakım; lastik hariç.',
                'is_active' => true,
            ]
        );

        // --- Süreler (ay) ---
        $d12 = CarDuration::query()->firstOrCreate(
            ['months' => '12'],
            ['is_active' => true]
        );
        $d24 = CarDuration::query()->firstOrCreate(
            ['months' => '24'],
            ['is_active' => true]
        );
        $d36 = CarDuration::query()->firstOrCreate(
            ['months' => '36'],
            ['is_active' => true]
        );

        // --- Kilometre ---
        $km10 = CarKilometerOption::query()->firstOrCreate(
            ['kilometer' => '10.000 km/yıl'],
            ['is_active' => true]
        );
        $km15 = CarKilometerOption::query()->firstOrCreate(
            ['kilometer' => '15.000 km/yıl'],
            ['is_active' => true]
        );
        $km20 = CarKilometerOption::query()->firstOrCreate(
            ['kilometer' => '20.000 km/yıl'],
            ['is_active' => true]
        );

        // --- Ek hizmetler ---
        CarExtraService::query()->firstOrCreate(
            ['name' => 'İkinci sürücü'],
            [
                'description' => 'Filo sözleşmesine ek sürücü tanımı.',
                'price' => '750',
                'price_type' => 1,
                'is_active' => true,
            ]
        );
        CarExtraService::query()->firstOrCreate(
            ['name' => 'HGS / OGS paketi'],
            [
                'description' => 'Aylık geçiş ücreti faturası.',
                'price' => '350',
                'price_type' => 1,
                'is_active' => true,
            ]
        );
        CarExtraService::query()->firstOrCreate(
            ['name' => 'Hasarsızlık indirimi'],
            [
                'description' => 'Yıllık hasarsızlığa bağlı indirim.',
                'price' => '5',
                'price_type' => 2,
                'is_active' => true,
            ]
        );

        // --- Özellik altyapısı ---
        $catComfort = CarAttributeCategory::query()->firstOrCreate(
            ['name' => 'Konfor'],
            ['is_active' => true]
        );
        $catSafety = CarAttributeCategory::query()->firstOrCreate(
            ['name' => 'Güvenlik'],
            ['is_active' => true]
        );
        $catTech = CarAttributeCategory::query()->firstOrCreate(
            ['name' => 'Multimedya'],
            ['is_active' => true]
        );

        $attrClimate = CarAttribute::query()->firstOrCreate(
            ['title' => 'Klima'],
        );
        $attrCamera = CarAttribute::query()->firstOrCreate(
            ['title' => 'Geri görüş kamerası'],
        );
        $attrNav = CarAttribute::query()->firstOrCreate(
            ['title' => 'Navigasyon'],
        );
        $attrAbs = CarAttribute::query()->firstOrCreate(
            ['title' => 'ABS / ESP'],
        );

        $valYes = CarAttributeValue::query()->firstOrCreate(
            ['title' => 'Var'],
        );
        $valNo = CarAttributeValue::query()->firstOrCreate(
            ['title' => 'Yok'],
        );
        $valAutoAc = CarAttributeValue::query()->firstOrCreate(
            ['title' => 'Otomatik (çift bölge)'],
        );

        // --- Araçlar ---
        $corolla = $this->seedCar(
            slug: 'toyota-corolla-15-hybrid',
            title: 'Toyota Corolla 1.5 Hybrid',
            brand: 'Toyota',
            model: 'Corolla',
            fuel: 'Benzin / Hibrit',
            transmission: 'e-CVT',
            body: 'Sedan',
            description: 'Şehir ve uzun yol için düşük tüketimli filo sedanı.',
            magicbox: ['segment' => 'C', 'featured' => true],
        );

        $passat = $this->seedCar(
            slug: 'vw-passat-16-tdi',
            title: 'Volkswagen Passat 1.6 TDI',
            brand: 'Volkswagen',
            model: 'Passat',
            fuel: 'Dizel',
            transmission: 'DSG',
            body: 'Sedan',
            description: 'Kurumsal kullanım için geniş iç hacimli sedan.',
            magicbox: ['segment' => 'D', 'featured' => true],
        );

        $duster = $this->seedCar(
            slug: 'dacia-duster-15-dci',
            title: 'Dacia Duster 1.5 dCi',
            brand: 'Dacia',
            model: 'Duster',
            fuel: 'Dizel',
            transmission: 'Manuel',
            body: 'SUV',
            description: 'Saha ve lojistik için ekonomik SUV seçeneği.',
            magicbox: ['segment' => 'B-SUV', 'featured' => false],
        );

        // --- Fiyat matrisi (tum kombinasyonlar) ---
        $vehicles = [
            $corolla,
            $passat,
            $duster,
        ];
        $packages = [$pkgFull, $pkgFlex];
        $durations = [$d12, $d24, $d36];
        $kilometers = [$km10, $km15, $km20];
        $downPayments = [$dp15, $dp25, $dp0];

        foreach ($vehicles as $vehicle) {
            foreach ($packages as $pkg) {
                foreach ($durations as $dur) {
                    foreach ($kilometers as $km) {
                        foreach ($downPayments as $dp) {
                            CarPriceMatrix::query()->updateOrCreate(
                                [
                                    'car_id' => $vehicle->id,
                                    'car_package_id' => $pkg->id,
                                    'car_duration_id' => $dur->id,
                                    'car_kilometer_option_id' => $km->id,
                                    'car_down_payment_id' => $dp->id,
                                ],
                                [
                                    'monthly_price' => $this->calculateMatrixPrice(
                                        car: $vehicle,
                                        package: $pkg,
                                        duration: $dur,
                                        kilometer: $km,
                                        downPayment: $dp,
                                    ),
                                    'is_active' => true,
                                ]
                            );
                        }
                    }
                }
            }
        }

        // --- Araç özellik pivotları ---
        $pivotRows = [
            [$corolla, $catComfort, $attrClimate, $valAutoAc],
            [$corolla, $catSafety, $attrAbs, $valYes],
            [$corolla, $catTech, $attrNav, $valYes],
            [$corolla, $catTech, $attrCamera, $valYes],
            [$passat, $catComfort, $attrClimate, $valAutoAc],
            [$passat, $catSafety, $attrAbs, $valYes],
            [$passat, $catTech, $attrNav, $valYes],
            [$passat, $catTech, $attrCamera, $valYes],
            [$duster, $catComfort, $attrClimate, $valAutoAc],
            [$duster, $catSafety, $attrAbs, $valYes],
            [$duster, $catTech, $attrNav, $valNo],
            [$duster, $catTech, $attrCamera, $valNo],
        ];

        foreach ($pivotRows as [$vehicle, $category, $attribute, $value]) {
            CarAttributePivot::query()->firstOrCreate(
                [
                    'car_id' => $vehicle->id,
                    'attribute_id' => $attribute->id,
                    'attribute_category_id' => $category->id,
                    'attribute_value_id' => $value->id,
                ],
            );
        }

        // --- Örnek kiralama talepleri (sabit e-posta ile tekrar çalışmada tekilleşir) ---
        RentalRequest::query()->firstOrCreate(
            ['email' => 'filo-seed-bekleyen@example.test'],
            [
                'name' => 'Mehmet',
                'surname' => 'Kaya',
                'phone_number' => '+90 532 111 22 33',
                'city' => 'İstanbul',
                'district' => 'Ataşehir',
                'requested_car_count' => 5,
                'company_total_car_count' => 42,
                'tax_office' => 'Üsküdar',
                'tax_number_or_tckn' => '1234567890',
                'cars' => [
                    ['slug' => $corolla->slug, 'adet' => 3, 'not' => 'Hybrid tercih'],
                    ['slug' => $passat->slug, 'adet' => 2],
                ],
                'is_active' => true,
                'read_at' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'FleetDemoSeeder',
            ]
        );

        RentalRequest::query()->firstOrCreate(
            ['email' => 'filo-seed-okunmus@example.test'],
            [
                'name' => 'Selin',
                'surname' => 'Demir',
                'phone_number' => '+90 533 444 55 66',
                'city' => 'Ankara',
                'district' => 'Çankaya',
                'requested_car_count' => 12,
                'company_total_car_count' => 80,
                'tax_office' => 'Çankaya',
                'tax_number_or_tckn' => '0987654321',
                'cars' => [
                    ['slug' => $duster->slug, 'adet' => 8],
                    ['slug' => $corolla->slug, 'adet' => 4],
                ],
                'is_active' => true,
                'read_at' => now()->subDay(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'FleetDemoSeeder',
            ]
        );

        $this->command?->info('Filo demo verisi hazır (katalog, 3 araç, tum fiyat kombinasyonlari, pivotlar, 2 örnek talep).');
    }

    /**
     * @param  array<string, mixed>  $magicbox
     */
    private function seedCar(
        string $slug,
        string $title,
        string $brand,
        string $model,
        string $fuel,
        string $transmission,
        string $body,
        string $description,
        array $magicbox,
    ): Car {
        $slugHash = abs(crc32($slug)) % 2147483647;

        $car = Car::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'slug_hash' => $slugHash,
                'title' => $title,
                'description' => $description,
                'brand' => $brand,
                'model' => $model,
                'fuel_type' => $fuel,
                'transmission_type' => $transmission,
                'body_type' => $body,
                'is_active' => true,
                'status' => true,
                'magicbox' => $magicbox,
            ]
        );

        $expectedHash = abs(crc32($car->slug)) % 2147483647;
        if ($car->slug_hash !== $expectedHash) {
            $car->forceFill(['slug_hash' => $expectedHash])->save();
        }

        return $car;
    }

    private function calculateMatrixPrice(
        Car $car,
        CarPackage $package,
        CarDuration $duration,
        CarKilometerOption $kilometer,
        CarDownPayment $downPayment,
    ): string {
        $basePriceBySlug = [
            'toyota-corolla-15-hybrid' => 33250,
            'vw-passat-16-tdi' => 40800,
            'dacia-duster-15-dci' => 27100,
        ];

        $base = $basePriceBySlug[$car->slug] ?? 30000;

        $packageAdjustment = $package->name === 'Tam Paket' ? 2200 : -1200;

        $months = (int) $duration->months;
        $durationAdjustment = match ($months) {
            12 => 2800,
            24 => 900,
            36 => -1800,
            default => 0,
        };

        $kmPerYear = (int) preg_replace('/\D+/', '', (string) $kilometer->kilometer);
        $kilometerAdjustment = match ($kmPerYear) {
            10000 => -1200,
            15000 => 0,
            20000 => 1700,
            default => 0,
        };

        $downPaymentAdjustment = match ($downPayment->amount) {
            '%25 Peşinat' => -1900,
            '%15 Peşinat' => -700,
            'Peşinatsız' => 1400,
            default => 0,
        };

        $price = max(
            15000,
            $base + $packageAdjustment + $durationAdjustment + $kilometerAdjustment + $downPaymentAdjustment
        );

        return number_format($price, 0, ',', '.') . ' TL';
    }
}
