<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Message;
use App\Models\RentalRequest;
use App\Models\WeCallYou;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ReportHeavyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('tr_TR');
        $faker->seed(20260427);

        $cars = Car::query()->select(['id', 'title', 'slug'])->get()->values();
        if ($cars->isEmpty()) {
            $this->command?->warn('ReportHeavyDataSeeder: Arac bulunamadi, once FleetDemoSeeder calisacak.');
            return;
        }

        $cities = [
            'Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Kocaeli', 'Konya', 'Gaziantep', 'Adana', 'Mersin',
            'Samsun', 'Trabzon', 'Kayseri', 'Eskisehir', 'Balikesir', 'Tekirdag', 'Sakarya', 'Denizli', 'Manisa',
        ];
        $districts = ['Merkez', 'Atasehir', 'Cankaya', 'Bornova', 'Nilufer', 'Muratpasa', 'Selcuklu', 'Sahinbey'];
        $preferredTimes = ['Sabah', 'Oglen', 'Aksam', 'Fark etmez'];
        $messageCategories = array_keys(Message::categoryLabels());

        $this->seedRentalRequests($faker, $cars->all(), $cities, $districts);
        $this->seedMessages($faker, $cities, $messageCategories);
        $this->seedWeCallYou($faker, $cars->all(), $cities, $preferredTimes);

        $this->command?->info('ReportHeavyDataSeeder: Yogun raporlama verisi olusturuldu.');
    }

    private function seedRentalRequests(\Faker\Generator $faker, array $cars, array $cities, array $districts): void
    {
        $total = 5000;
        $batch = [];
        $stamp = now()->format('YmdHis');

        for ($i = 0; $i < $total; $i++) {
            $createdAt = now()->subDays(random_int(0, 720))->subMinutes(random_int(0, 1440));
            $selectedCars = collect($cars)->random(random_int(1, min(3, count($cars))));
            $carsPayload = $selectedCars->map(function ($car): array {
                return [
                    'slug' => $car->slug,
                    'title' => $car->title,
                    'adet' => random_int(1, 8),
                ];
            })->values()->all();

            $batch[] = [
                'name' => $faker->firstName(),
                'surname' => $faker->lastName(),
                'email' => 'report.rental.'.$stamp.'.'.$i.'@example.test',
                'phone_number' => $faker->phoneNumber(),
                'city' => Arr::random($cities),
                'district' => Arr::random($districts),
                'requested_car_count' => random_int(1, 30),
                'company_total_car_count' => random_int(1, 600),
                'tax_office' => Arr::random($districts),
                'tax_number_or_tckn' => (string) random_int(1000000000, 9999999999),
                'cars' => json_encode($carsPayload, JSON_UNESCAPED_UNICODE),
                'is_active' => true,
                'read_at' => random_int(0, 100) < 68 ? $createdAt->copy()->addHours(random_int(1, 96)) : null,
                'ip_address' => $faker->ipv4(),
                'user_agent' => 'ReportHeavyDataSeeder/Rental',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($batch) >= 500) {
                RentalRequest::query()->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            RentalRequest::query()->insert($batch);
        }
    }

    private function seedMessages(\Faker\Generator $faker, array $cities, array $messageCategories): void
    {
        $total = 3500;
        $batch = [];
        $stamp = now()->format('YmdHis');

        for ($i = 0; $i < $total; $i++) {
            $createdAt = now()->subDays(random_int(0, 720))->subMinutes(random_int(0, 1440));

            $batch[] = [
                'category' => Arr::random($messageCategories),
                'name' => $faker->firstName(),
                'surname' => $faker->lastName(),
                'email' => 'report.message.'.$stamp.'.'.$i.'@example.test',
                'phone_number' => $faker->phoneNumber(),
                'content' => Str::limit($faker->realText(260), 240),
                'read_at' => random_int(0, 100) < 62 ? $createdAt->copy()->addHours(random_int(1, 72)) : null,
                'ip_address' => $faker->ipv4(),
                'user_agent' => 'ReportHeavyDataSeeder/Message/'.Arr::random($cities),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($batch) >= 500) {
                Message::query()->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            Message::query()->insert($batch);
        }
    }

    private function seedWeCallYou(\Faker\Generator $faker, array $cars, array $cities, array $preferredTimes): void
    {
        $total = 4200;
        $batch = [];
        $stamp = now()->format('YmdHis');

        for ($i = 0; $i < $total; $i++) {
            $createdAt = now()->subDays(random_int(0, 720))->subMinutes(random_int(0, 1440));
            $car = Arr::random($cars);

            $batch[] = [
                'name' => $faker->firstName(),
                'surname' => $faker->lastName(),
                'email' => 'report.callback.'.$stamp.'.'.$i.'@example.test',
                'phone_number' => $faker->phoneNumber(),
                'car_id' => $car->id,
                'city' => Arr::random($cities),
                'preferred_time' => Arr::random($preferredTimes),
                'note' => Str::limit($faker->sentence(18), 160),
                'requested_car_count' => random_int(1, 20),
                'car_park_count' => random_int(1, 450),
                'is_active' => true,
                'magicbox' => json_encode([
                    'source' => 'report-seeder',
                    'campaign' => Arr::random(['google', 'meta', 'seo', 'direct']),
                ], JSON_UNESCAPED_UNICODE),
                'read_at' => random_int(0, 100) < 66 ? $createdAt->copy()->addHours(random_int(1, 96)) : null,
                'ip_address' => $faker->ipv4(),
                'user_agent' => 'ReportHeavyDataSeeder/WeCallYou',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if (count($batch) >= 500) {
                WeCallYou::query()->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            WeCallYou::query()->insert($batch);
        }
    }
}
