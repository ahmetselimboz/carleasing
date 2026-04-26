<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarAttributePivot extends Model
{
    use HasFactory;

    protected $table = 'car_attribute_pivots';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'car_id',
        'attribute_id',
        'attribute_category_id',
        'attribute_value_id',
    ];

    /**
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * @return BelongsTo<CarAttribute, $this>
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(CarAttribute::class, 'attribute_id');
    }

    /**
     * @return BelongsTo<CarAttributeCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CarAttributeCategory::class, 'attribute_category_id');
    }

    /**
     * @return BelongsTo<CarAttributeValue, $this>
     */
    public function value(): BelongsTo
    {
        return $this->belongsTo(CarAttributeValue::class, 'attribute_value_id');
    }
}
