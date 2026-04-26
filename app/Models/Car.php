<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'slug_hash',
        'description',
        'brand',
        'model',
        'fuel_type',
        'transmission_type',
        'body_type',
        'image',
        'is_active',
        'status',
        'home_featured',
        'home_sort_order',
        'magicbox',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status' => 'boolean',
            'home_featured' => 'boolean',
            'home_sort_order' => 'integer',
            'magicbox' => 'array',
        ];
    }

    /**
     * @return HasMany<CarPriceMatrix, $this>
     */
    public function priceMatrices(): HasMany
    {
        return $this->hasMany(CarPriceMatrix::class);
    }

    /**
     * @return HasMany<CarExtraService, $this>
     */
    public function carDurations(): HasMany
    {
        return $this->hasMany(CarDuration::class);
    }

    /**
     * @return HasMany<CarAttributePivot, $this>
     */
    public function attributePivots(): HasMany
    {
        return $this->hasMany(CarAttributePivot::class);
    }

    /** public/storage altındaki göreli yol için tam URL (yoksa null). */
    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return asset('storage/'.$this->image);
    }

    /**
     * Listeler için: önce araç görseli, yoksa ayarlardaki yer tutucu.
     */
    public function displayImageUrl(): ?string
    {
        if ($this->imageUrl()) {
            return $this->imageUrl();
        }

        $placeholder = Setting::cachedForViews()['placeholder_image_url'] ?? null;

        return $placeholder ?: null;
    }

    /**
     * Anasayfa / liste için örnek aylık fiyatın kaynağı: matristeki ilk aktif satır.
     * İlişki yüklüyse (ör. `priceMatrices` eager load) ek sorgu yapmaz.
     */
    public function displayMonthlyPriceMatrixRow(): ?CarPriceMatrix
    {
        if ($this->relationLoaded('priceMatrices')) {
            $row = $this->priceMatrices->first();
            if ($row !== null) {
                $row->loadMissing('duration');
            }

            return $row;
        }

        return $this->priceMatrices()
            ->where('is_active', true)
            ->orderBy('id')
            ->with('duration')
            ->first();
    }

    /** Anasayfa kartı için örnek aylık fiyat metni (matristeki ilk aktif satır). */
    public function displayMonthlyPriceLabel(): ?string
    {
        return $this->displayMonthlyPriceMatrixRow()?->monthly_price;
    }

    /**
     * Gösterilen aylık fiyatın bağlı olduğu kiralama süresi (ay sayısı), örn. 12 veya 24.
     */
    public function displayMonthlyPriceDurationMonths(): ?int
    {
        $row = $this->displayMonthlyPriceMatrixRow();
        $months = $row?->duration?->months;

        return $months !== null ? (int) $months : null;
    }

    /**
     * Süre etiketi, örn. "24 ay" (ay sayısı yoksa null).
     */
    public function displayMonthlyPriceDurationLabel(): ?string
    {
        $months = $this->displayMonthlyPriceDurationMonths();

        return $months !== null ? "{$months} ay" : null;
    }

    /**
     * @return array{price: string, months: int|null, duration_label: string|null}|null
     */
    public function displayMonthlyPriceContext(): ?array
    {
        $row = $this->displayMonthlyPriceMatrixRow();
        if ($row === null || $row->monthly_price === null || $row->monthly_price === '') {
            return null;
        }

        $months = $row->duration?->months;

        return [
            'price' => $row->monthly_price,
            'months' => $months !== null ? (int) $months : null,
            'duration_label' => $months !== null ? ((int) $months).' ay' : null,
        ];
    }

    /**
     * @return Collection<CarDuration>
     */
    public function displayCarDurations(): Collection
    {
        return $this->carDurations()->where('is_active', true)->orderBy('id')->get();
    }

}
