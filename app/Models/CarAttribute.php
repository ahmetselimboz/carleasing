<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarAttribute extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
    ];

    /**
     * @return HasMany<CarAttributePivot, $this>
     */
    public function attributePivots(): HasMany
    {
        return $this->hasMany(CarAttributePivot::class, 'attribute_id');
    }
}
