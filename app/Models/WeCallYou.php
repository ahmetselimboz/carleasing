<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeCallYou extends Model
{
    use SoftDeletes;

    protected $table = 'we_call_you';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone_number',
        'car_id',
        'city',
        'preferred_time',
        'note',
        'requested_car_count',
        'car_park_count',
        'is_active',
        'magicbox',
        'read_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'magicbox' => 'array',
            'read_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderByDesc('id');
    }
}
