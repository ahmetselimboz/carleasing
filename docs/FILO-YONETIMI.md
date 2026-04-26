# Filo ve araç yönetimi — teknik özet

Bu doküman, projedeki **araç / filo / kiralama talebi** altyapısının baştan sona nelerden oluştuğunu ve parçaların nasıl ilişkili olduğunu açıklar. Ön koşul: ilgili **migration** dosyalarının veritabanında çalışmış olması.

---

## 1. Veri modeli (migration’lar)

Aşağıdaki tablolar migration’larla tanımlıdır (sıra, dosya adlarındaki tarihe göre):

| Tablo | Amaç |
|--------|------|
| `cars` | Araç kartı: başlık, slug, açıklama, marka/model, yakıt, şanzıman, kasa, `is_active`, `status`, `magicbox` (JSON), soft delete |
| `car_down_payments` | Peşinat seçenekleri (metin, örn. `%15`) |
| `car_packages` | Paket adı / açıklama |
| `car_durations` | Kiralama süresi (ay, metin) |
| `car_kilometer_options` | Yıllık km bandı (metin) |
| `car_price_matrix` | Bir araç için paket × süre × km × peşinata göre **aylık fiyat** satırı |
| `car_extra_services` | Ek hizmet: ad, açıklama, fiyat, `price_type` (0 sabit, 1 aylık, 2 yüzde) |
| `car_attribute_categories` | Özellik grupları (örn. Konfor) |
| `car_attributes` | Özellik adı (örn. Klima) |
| `car_attribute_values` | Özellik değeri (örn. Var / Yok) |
| `car_attribute_pivots` | Araç ↔ kategori ↔ özellik ↔ değer bağlantısı |
| `rental_requests` | Kurumsal / filo talep formu kayıtları (`cars` JSON, okunma, IP, user agent, soft delete) |

**Not:** `car_price_matrix` satırları `cars` ve diğer katalog tablolarına foreign key ile bağlıdır; katalog önce doldurulmalıdır.

---

## 2. Eloquent modelleri (`app/Models`)

| Model | Tablo | Öne çıkanlar |
|--------|--------|----------------|
| `Car` | `cars` | `SoftDeletes`; `priceMatrices()`, `attributePivots()`; `magicbox` array cast |
| `CarDownPayment` | `car_down_payments` | `priceMatrices()` |
| `CarPackage` | `car_packages` | `priceMatrices()` |
| `CarDuration` | `car_durations` | `priceMatrices()` |
| `CarKilometerOption` | `car_kilometer_options` | `priceMatrices()` |
| `CarPriceMatrix` | `car_price_matrix` | `$table` açık; `car()`, `package()`, `duration()`, `kilometerOption()`, `downPayment()` |
| `CarExtraService` | `car_extra_services` | Bağımsız ücret kalemi (matrise FK yok) |
| `CarAttributeCategory` | `car_attribute_categories` | `attributePivots()` |
| `CarAttribute` | `car_attributes` | `attributePivots()` (`attribute_id`) |
| `CarAttributeValue` | `car_attribute_values` | `attributePivots()` |
| `CarAttributePivot` | `car_attribute_pivots` | `car()`, `attribute()`, `category()`, `value()` |
| `RentalRequest` | `rental_requests` | `cars` array cast; `read_at`, soft delete |

---

## 3. Yetkilendirme (policy’ler)

### `FleetManagementPolicy`

Şu modellere **tek policy** ile kayıtlıdır (`AppServiceProvider` içinde döngüyle):

`Car`, `CarDownPayment`, `CarPackage`, `CarDuration`, `CarKilometerOption`, `CarPriceMatrix`, `CarExtraService`, `CarAttributeCategory`, `CarAttribute`, `CarAttributeValue`, `CarAttributePivot`

- **Görüntüleme** (`viewAny`, `view`): süper admin, **Admin**, **Müşteri hizmetleri** (hesap aktif olmalı).
- **Oluşturma / güncelleme / silme**: süper admin ve **Admin** (müşteri hizmetleri yazamaz).

### `RentalRequestPolicy`

- **Görüntüleme ve güncelleme** (ör. okundu işareti): süper admin, Admin, Müşteri hizmetleri.
- **Silme**: süper admin ve Admin.
- **create**: her zaman `false` (talepler panel dışı akışla oluşturulur).

---

## 4. HTTP katmanı

### Form request’ler (`app/Http/Requests/Manage`)

- `StoreCarRequest` / `UpdateCarRequest`: araç alanları; `magicbox` formda `mb[]` satırlarından (alan adı, tip: metin/tam sayı/evet-hayır, değer) `App\Support\MagicboxForm` ile diziye çevrilir; `is_active` / `status` checkbox birleştirmesi.
- `StoreCarPriceMatrixRequest` / `UpdateCarPriceMatrixRequest`: matris alanları; aynı araçta aynı (paket, süre, km, peşinat) kombinasyonu **unique** kuralıyla engellenir.

### Controller’lar (`app/Http/Controllers/Manage`)

| Controller | Görev |
|------------|--------|
| `CarController` | Araç CRUD; `storeAttributePivot` / `destroyAttributePivot` |
| `CarPriceMatrixController` | İç içe `cars.price-matrices` + shallow `price-matrices` ile matris oluşturma / düzenleme / silme |
| `CarDownPaymentController`, `CarPackageController`, `CarDurationController`, `CarKilometerOptionController` | Katalog CRUD |
| `CarExtraServiceController` | Ek hizmet CRUD (`price_type` seçenekleri) |
| `CarAttributeCategoryController`, `CarAttributeController`, `CarAttributeValueController` | Özellik sözlüğü CRUD |
| `RentalRequestController` | Talep listesi, detay, okundu/okunmadı (`PATCH`), silme |

**Slug / `slug_hash`:** Araç kaydında slug boşsa başlık veya marka-modelden üretilir; çakışmada sonuna `-1`, `-2` eklenir. `slug_hash`, MySQL `INT` sınırına sığması için `abs(crc32(slug)) % 2147483647` ile hesaplanır (panel `CarController` ile uyumlu).

---

## 5. Rotalar

Tümü `routes/web.php` içinde **`manage` prefix’i** ve `auth` + `manage` middleware’i altında:

- `cars` resource (show hariç)
- `POST cars/{car}/attribute-pivots`, `DELETE cars/{car}/attribute-pivots/{car_attribute_pivot}`
- `cars.price-matrices` resource → **shallow**: oluşturma `cars/{car}/price-matrices/...`, düzenle/sil `price-matrices/{price_matrix}`
- Katalog: `car-down-payments`, `car-packages`, `car-durations`, `car-kilometer-options`, `car-extra-services`, `car-attribute-categories`, `car-attributes`, `car-attribute-values` (show hariç)
- `rental-requests`: `index`, `show`, `update`, `destroy`

Tam liste: `php artisan route:list --path=manage`

---

## 6. Arayüz (Blade)

- Düzen: `resources/views/admin/layout.blade.php`
- Menü: `resources/views/admin/components/aside.blade.php` — **Filo** grubu; araç/katalog linkleri `viewAny(Car)` ile, talepler `viewAny(RentalRequest)` ile; ana Filo linki önce araç listesine, yalnızca talep yetkisi varsa talep listesine gider.
- Filo sayfaları:
  - `resources/views/admin/fleet/cars/` — index, create, edit (edit içinde matris tablosu + pivot formu)
  - `resources/views/admin/fleet/price_matrices/` — create, edit
  - `resources/views/admin/fleet/simple/` — ortak katalog listesi ve formu
  - `resources/views/admin/fleet/rental_requests/` — index, show

Toast bildirimleri mevcut admin `session('toast')` yapısı ile uyumludur.

---

## 7. Demo verisi (seeder)

- **`database/seeders/FleetDemoSeeder.php`**: Katalog → 3 örnek araç → 8 fiyat satırı → 12 özellik pivotu → 2 örnek `rental_request`. Çoğu kayıt **`firstOrCreate`** ile tekrar çalıştırmada mümkün olduğunca çoğalmaz.
- **`database/seeders/DatabaseSeeder.php`**: `SuperAdminSeeder` sonrasında **`production` dışı** ortamlarda `FleetDemoSeeder` otomatik çağrılır. Canlı ortamda denemek için:

  ```bash
  php artisan db:seed --class=Database\\Seeders\\FleetDemoSeeder
  ```

---

## 8. Önerilen operasyon sırası (panel)

1. Peşinat, paket, süre, kilometre (ve isteğe bağlı ek hizmet / özellik sözlüğü) tanımları.
2. Araç oluştur; düzenleme ekranından **fiyat matrisi** ve **özellik satırları** ekleme.
3. Kiralama talepleri menüsünden başvuruları takip etme.

---

## 9. Bilinçli olarak yapılmayanlar

- Ön yüz (theme) üzerinde araç listesi / detay / teklif formu bu dokümanın kapsamı dışında bırakılmış olabilir; veri katmanı ve yönetim paneli hazırdır.
- `car_extra_services` ile araç veya matris arasında veritabanı FK yok; ilişki ihtiyacı ileride ayrı pivot veya JSON ile genişletilebilir.

---

*Son güncelleme: filo paneli, policy’ler, seeder ve `slug_hash` düzeltmesi ile uyumlu olacak şekilde yazılmıştır.*
