<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarPriceMatrix extends Model
{
    use HasFactory;

    protected $table = 'car_price_matrix';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'car_package_id',
        'car_duration_id',
        'car_kilometer_option_id',
        'car_down_payment_id',
        'monthly_price',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return BelongsTo<CarPackage, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(CarPackage::class, 'car_package_id');
    }

    /**
     * @return BelongsTo<CarDuration, $this>
     */
    public function duration(): BelongsTo
    {
        return $this->belongsTo(CarDuration::class, 'car_duration_id');
    }

    /**
     * @return BelongsTo<CarKilometerOption, $this>
     */
    public function kilometerOption(): BelongsTo
    {
        return $this->belongsTo(CarKilometerOption::class, 'car_kilometer_option_id');
    }

    /**
     * @return BelongsTo<CarDownPayment, $this>
     */
    public function downPayment(): BelongsTo
    {
        return $this->belongsTo(CarDownPayment::class, 'car_down_payment_id');
    }
}
