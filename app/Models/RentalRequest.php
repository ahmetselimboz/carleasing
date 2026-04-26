<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone_number',
        'city',
        'district',
        'requested_car_count',
        'company_total_car_count',
        'tax_office',
        'tax_number_or_tckn',
        'cars',
        'is_active',
        'read_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'read_at' => 'datetime',
            'cars' => 'array',
        ];
    }
}
